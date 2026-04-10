<?php
namespace Database\Factories;
use App\Models\RepairRequest;
use Illuminate\Database\Eloquent\Factories\Factory;
class RepairRequestFactory extends Factory
{
    protected $model = RepairRequest::class;
    public function definition(): array
    {
        return [
            'property_id' => \App\Models\Property::inRandomOrder()->first()?->id,
            'tenant_id' => \App\Models\Tenant::inRandomOrder()->first()?->id,
            'description' => fake()->sentence(),
            'status' => fake()->randomElement(['open', 'closed', 'in_progress']),
            'submitted_at' => fake()->dateTimeThisYear(),
            'resolved_at' => null,
        ];
    }
}
