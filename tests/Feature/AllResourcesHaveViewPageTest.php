<?php

namespace Tests\Feature;

use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Sanity check: every Filament resource registers a "view" page route and
 * that route renders for a super_admin. Falls back to Filament's default
 * infolist (form schema in disabled mode) when a resource has no custom
 * infolist() method — that's intentional and this test ensures it works.
 */
class AllResourcesHaveViewPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_resource_exposes_a_view_route(): void
    {
        $missing = [];

        foreach (Filament::getResources() as $resourceClass) {
            $pages = $resourceClass::getPages();
            if (! array_key_exists('view', $pages)) {
                $missing[] = $resourceClass;
            }
        }

        $this->assertSame(
            [],
            $missing,
            "These resources have no 'view' page route:\n" . implode("\n", $missing)
        );
    }

    public function test_view_pages_render_for_admin_on_a_sample_resource(): void
    {
        $admin = \App\Models\User::factory()->create(['role' => 'super_admin']);

        $owner = \App\Models\Owner::factory()->create();
        $resp = $this->actingAs($admin)->get("/admin/owners/{$owner->id}");
        $resp->assertOk();
        $resp->assertSee($owner->name);

        $tenant = \App\Models\Tenant::factory()->create();
        $resp = $this->actingAs($admin)->get("/admin/tenants/{$tenant->id}");
        $resp->assertOk();
        $resp->assertSee($tenant->name);
    }
}
