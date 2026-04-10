<?php
namespace Database\Factories;
use App\Models\Owner;
use Illuminate\Database\Eloquent\Factories\Factory;
class OwnerFactory extends Factory
{
    protected $model = Owner::class;
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'bio' => $this->faker->paragraph(),
            'photo' => $this->faker->imageUrl(),
            'user_id' => null,
        ];
    }
}
