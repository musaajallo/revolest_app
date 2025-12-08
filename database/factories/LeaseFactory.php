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
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
            'rent_amount' => $this->faker->numberBetween(1000, 5000),
            'status' => $this->faker->randomElement(['active', 'terminated', 'pending']),
            'contract_file' => $this->faker->filePath(),
        ];
    }
}
