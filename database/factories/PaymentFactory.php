<?php
namespace Database\Factories;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;
class PaymentFactory extends Factory
{
    protected $model = Payment::class;
    public function definition(): array
    {
        $amount = $this->faker->numberBetween(20000, 240000);

        return [
            'lease_id' => \App\Models\Lease::inRandomOrder()->first()?->id,
            'tenant_id' => \App\Models\Tenant::inRandomOrder()->first()?->id,
            'owner_id' => \App\Models\Owner::inRandomOrder()->first()?->id,
            'amount' => $amount,
            'expected_amount' => $amount,
            'purpose' => $this->faker->randomElement(['rent', 'rent', 'rent', 'security_deposit', 'agent_fee']),
            'payment_date' => $this->faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'period_label' => $this->faker->randomElement(['Jan 2026', 'Feb 2026', 'Q1 2026', '2026 annual']),
            'method' => $this->faker->randomElement(['cash', 'bank_transfer', 'mobile_money', 'cheque']),
            'status' => $this->faker->randomElement(['complete', 'complete', 'complete', 'pending']),
            'receipt_file' => null,
            'paid_by_name' => $this->faker->optional(0.4)->name(),
            'notes' => $this->faker->optional(0.2)->sentence(),
        ];
    }
}
