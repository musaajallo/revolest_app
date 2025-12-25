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
            'submitted_by_user_id' => \App\Models\User::inRandomOrder()->first()?->id,
            'property_id' => \App\Models\Property::inRandomOrder()->first()?->id,
            'tenant_id' => \App\Models\Tenant::inRandomOrder()->first()?->id,
            'description' => $this->faker->sentence(),
            'complaint_category' => $this->faker->randomElement(['maintenance', 'noise', 'security', 'utilities', 'general']),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high', 'urgent']),
            'status' => $this->faker->randomElement(['open', 'closed', 'in_progress']),
            'submitted_at' => $this->faker->dateTimeThisYear(),
            'resolved_at' => null,
            'resolution_notes' => null,
        ];
    }
}
