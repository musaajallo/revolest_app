<?php
namespace Database\Factories;
use App\Models\Lease;
use Illuminate\Database\Eloquent\Factories\Factory;
class LeaseFactory extends Factory
{
    protected $model = Lease::class;
    public function definition(): array
    {
        return [
            'property_id' => \App\Models\Property::inRandomOrder()->first()?->id,
            'tenant_id' => \App\Models\Tenant::inRandomOrder()->first()?->id,
            'start_date' => fake()->date(),
            'end_date' => fake()->date(),
            'rent_amount' => fake()->numberBetween(1000, 5000),
            'status' => fake()->randomElement(['active', 'terminated', 'pending']),
            'contract_file' => fake()->filePath(),
        ];
    }
}
