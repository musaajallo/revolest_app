<?php
namespace Database\Factories;
use App\Models\Lease;
use Illuminate\Database\Eloquent\Factories\Factory;
class LeaseFactory extends Factory
{
    protected $model = Lease::class;
    public function definition(): array
    {
        $rent = $this->faker->numberBetween(120000, 600000);
        $cycle = $this->faker->randomElement(['monthly', 'quarterly', 'annually']);
        $start = $this->faker->dateTimeBetween('-1 year', 'now');

        return [
            'property_id' => \App\Models\Property::inRandomOrder()->first()?->id,
            'tenant_id' => \App\Models\Tenant::inRandomOrder()->first()?->id,
            'start_date' => $start->format('Y-m-d'),
            'end_date' => (clone $start)->modify('+1 year')->format('Y-m-d'),
            'rent_amount' => $rent,
            'security_deposit_amount' => (int) round($rent / 12),
            'security_deposit_status' => $this->faker->randomElement(['pending', 'paid', 'paid', 'paid', 'partial']),
            'rent_cycle' => $cycle,
            'status' => 'active',
            'contract_file' => null,
            'inspection_cycle_months' => $this->faker->randomElement([3, 6, 12]),
            'notes' => $this->faker->optional(0.3)->sentence(),
        ];
    }
}
