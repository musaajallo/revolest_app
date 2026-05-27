<?php

namespace Tests\Feature;

use App\Models\Inspection;
use App\Models\Lease;
use App\Models\Owner;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Receipt;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Covers the booted-hook behaviour introduced in phases 1–2 of the
 * oversight schema work. These tests guard against quiet regressions in
 * the commission auto-calc, rent-cycle advancement, receipt auto-issue,
 * and inspection cache-sync wiring.
 */
class OversightHooksTest extends TestCase
{
    use RefreshDatabase;

    private function makeLeaseWithOwner(array $leaseOverrides = [], array $ownerOverrides = []): Lease
    {
        $owner = Owner::factory()->create(array_merge([
            'commission_percent' => 10.00,
        ], $ownerOverrides));

        $property = Property::factory()->create(['owner_id' => $owner->id]);
        $tenant = Tenant::factory()->create();

        return Lease::factory()->create(array_merge([
            'property_id' => $property->id,
            'tenant_id' => $tenant->id,
            'start_date' => '2026-01-01',
            'end_date' => '2027-01-01',
            'rent_cycle' => 'annually',
            'rent_amount' => 240000,
            'status' => 'active',
        ], $leaseOverrides));
    }

    public function test_lease_creating_hook_computes_initial_next_rent_due_at(): void
    {
        $lease = $this->makeLeaseWithOwner();

        $this->assertNotNull($lease->next_rent_due_at);
        $this->assertSame('2027-01-01', $lease->next_rent_due_at->format('Y-m-d'));
    }

    public function test_lease_rent_cycle_advance_steps_by_cycle(): void
    {
        $monthly = $this->makeLeaseWithOwner(['rent_cycle' => 'monthly']);
        $monthly->refresh();
        $before = $monthly->next_rent_due_at->copy();
        $monthly->advanceRentDue();

        $this->assertSame($before->copy()->addMonth()->format('Y-m-d'), $monthly->next_rent_due_at->format('Y-m-d'));

        $quarterly = $this->makeLeaseWithOwner(['rent_cycle' => 'quarterly']);
        $before = $quarterly->next_rent_due_at->copy();
        $quarterly->advanceRentDue();
        $this->assertSame($before->copy()->addMonths(3)->format('Y-m-d'), $quarterly->next_rent_due_at->format('Y-m-d'));
    }

    public function test_payment_creates_auto_calculates_commission_from_owner_rate(): void
    {
        $lease = $this->makeLeaseWithOwner([], ['commission_percent' => 12.50]);

        $payment = Payment::create([
            'lease_id' => $lease->id,
            'tenant_id' => $lease->tenant_id,
            'owner_id' => $lease->property->owner_id,
            'amount' => 240000,
            'expected_amount' => 240000,
            'purpose' => 'rent',
            'payment_date' => now(),
            'method' => 'bank_transfer',
            'status' => 'complete',
        ]);

        $this->assertEqualsWithDelta(30000.00, (float) $payment->commission_amount, 0.01);
    }

    public function test_payment_uses_lease_commission_override_when_set(): void
    {
        $lease = $this->makeLeaseWithOwner(
            ['commission_percent_override' => 15.00],
            ['commission_percent' => 10.00]
        );

        $payment = Payment::create([
            'lease_id' => $lease->id,
            'tenant_id' => $lease->tenant_id,
            'owner_id' => $lease->property->owner_id,
            'amount' => 200000,
            'purpose' => 'rent',
            'payment_date' => now(),
            'method' => 'bank_transfer',
            'status' => 'complete',
        ]);

        $this->assertEqualsWithDelta(30000.00, (float) $payment->commission_amount, 0.01);
    }

    public function test_completed_rent_payment_advances_lease_next_rent_due_at(): void
    {
        $lease = $this->makeLeaseWithOwner(['rent_cycle' => 'quarterly', 'next_rent_due_at' => '2026-03-01']);

        Payment::create([
            'lease_id' => $lease->id,
            'tenant_id' => $lease->tenant_id,
            'owner_id' => $lease->property->owner_id,
            'amount' => 60000,
            'purpose' => 'rent',
            'payment_date' => '2026-03-01',
            'method' => 'cash',
            'status' => 'complete',
        ]);

        $lease->refresh();
        $this->assertSame('2026-06-01', $lease->next_rent_due_at->format('Y-m-d'));
    }

    public function test_completed_payment_creates_receipt_with_generated_number(): void
    {
        $lease = $this->makeLeaseWithOwner();

        $payment = Payment::create([
            'lease_id' => $lease->id,
            'tenant_id' => $lease->tenant_id,
            'owner_id' => $lease->property->owner_id,
            'amount' => 50000,
            'purpose' => 'rent',
            'payment_date' => now(),
            'method' => 'cash',
            'status' => 'complete',
        ]);

        $receipt = $payment->fresh('receipt')->receipt;

        $this->assertInstanceOf(Receipt::class, $receipt);
        $this->assertMatchesRegularExpression('/^RCV-\d{4}-\d{6}$/', $receipt->receipt_number);
        $this->assertEquals(50000, (float) $receipt->amount);
    }

    public function test_receipt_numbers_increment_within_year(): void
    {
        $lease = $this->makeLeaseWithOwner();

        $a = Payment::create([
            'lease_id' => $lease->id, 'tenant_id' => $lease->tenant_id, 'owner_id' => $lease->property->owner_id,
            'amount' => 1000, 'purpose' => 'rent', 'payment_date' => '2026-01-15', 'method' => 'cash', 'status' => 'complete',
        ]);
        $b = Payment::create([
            'lease_id' => $lease->id, 'tenant_id' => $lease->tenant_id, 'owner_id' => $lease->property->owner_id,
            'amount' => 1000, 'purpose' => 'rent', 'payment_date' => '2026-02-15', 'method' => 'cash', 'status' => 'complete',
        ]);

        $rA = $a->fresh('receipt')->receipt->receipt_number;
        $rB = $b->fresh('receipt')->receipt->receipt_number;

        $this->assertStringStartsWith('RCV-2026-', $rA);
        $this->assertStringStartsWith('RCV-2026-', $rB);
        $this->assertNotSame($rA, $rB);

        $seqA = (int) substr($rA, 9);
        $seqB = (int) substr($rB, 9);
        $this->assertSame($seqA + 1, $seqB);
    }

    public function test_inspection_creation_syncs_cache_columns_on_lease(): void
    {
        $lease = $this->makeLeaseWithOwner(['inspection_cycle_months' => 6]);

        $this->assertNull($lease->last_inspection_at);

        Inspection::create([
            'lease_id' => $lease->id,
            'property_id' => $lease->property_id,
            'inspected_at' => '2026-05-01 10:00:00',
            'status' => 'pass',
        ]);

        $lease->refresh();
        $this->assertSame('2026-05-01', $lease->last_inspection_at->format('Y-m-d'));
        $this->assertSame('pass', $lease->last_inspection_status);
        $this->assertSame('2026-11-01', $lease->next_inspection_at->format('Y-m-d'));
    }

    public function test_all_leads_report_renders_for_admin(): void
    {
        $admin = \App\Models\User::factory()->create(['role' => 'super_admin']);

        // Seed at least one lead in each category so the page has content
        \App\Models\LandPurchaseLead::create([
            'full_name' => 'Test Buyer', 'phone' => '111', 'status' => 'new',
        ]);
        \App\Models\LandSaleLead::create([
            'full_name' => 'Test Seller', 'phone_primary' => '222', 'status' => 'new',
        ]);
        \App\Models\RentalConsultation::create([
            'full_name' => 'Test Renter', 'phone' => '333', 'status' => 'in_review',
        ]);

        $response = $this->actingAs($admin)->get('/admin/all-leads-report');

        $response->assertOk();
        $response->assertSee('All Client Requests');
        $response->assertSee('Land Purchase');
        $response->assertSee('Test Buyer');
        $response->assertSee('Test Seller');
        $response->assertSee('Test Renter');
    }

    public function test_inspection_delete_clears_cache_when_last_one_removed(): void
    {
        $lease = $this->makeLeaseWithOwner();
        $inspection = Inspection::create([
            'lease_id' => $lease->id,
            'property_id' => $lease->property_id,
            'inspected_at' => '2026-05-01 10:00:00',
            'status' => 'pass',
        ]);

        $inspection->delete();

        $lease->refresh();
        $this->assertNull($lease->last_inspection_at);
        $this->assertNull($lease->last_inspection_status);
        $this->assertNull($lease->next_inspection_at);
    }
}
