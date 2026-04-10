<?php
namespace Database\Factories;
use App\Models\Agent;
use Illuminate\Database\Eloquent\Factories\Factory;
class AgentFactory extends Factory
{
    protected $model = Agent::class;
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'bio' => fake()->paragraph(),
            'photo' => fake()->imageUrl(),
            'user_id' => null,
        ];
    }
}
