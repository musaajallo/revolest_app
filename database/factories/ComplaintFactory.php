<?php
namespace Database\Factories;
use App\Models\Complaint;
use Illuminate\Database\Eloquent\Factories\Factory;
class ComplaintFactory extends Factory
{
    protected $model = Complaint::class;
    public function definition(): array
    {
        return [
            'property_id' => \App\Models\Property::inRandomOrder()->first()?->id,
            'tenant_id' => \App\Models\Tenant::inRandomOrder()->first()?->id,
            'description' => $this->faker->sentence(),
            'status' => $this->faker->randomElement(['open', 'closed', 'in_progress']),
            'submitted_at' => $this->faker->dateTimeThisYear(),
            'resolved_at' => null,
        ];
    }
}
