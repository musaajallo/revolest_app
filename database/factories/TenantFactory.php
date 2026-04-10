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
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'photo' => $this->faker->imageUrl(),
            'user_id' => null,
        ];
    }
}
