<?php
namespace Database\Factories;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
class TenantFactory extends Factory
{
    protected $model = Tenant::class;
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'photo' => fake()->imageUrl(),
            'user_id' => null,
        ];
    }
}
