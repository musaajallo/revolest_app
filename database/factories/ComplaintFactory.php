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
            'description' => fake()->sentence(),
            'complaint_category' => fake()->randomElement(['maintenance', 'noise', 'security', 'utilities', 'general']),
            'priority' => fake()->randomElement(['low', 'medium', 'high', 'urgent']),
            'status' => fake()->randomElement(['open', 'closed', 'in_progress']),
            'submitted_at' => fake()->dateTimeThisYear(),
            'resolved_at' => null,
            'resolution_notes' => null,
        ];
    }
}
