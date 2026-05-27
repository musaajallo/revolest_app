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
            'bank_name' => $this->faker->randomElement(['EcoBank', 'Trust Bank', 'GTBank', 'Standard Chartered']),
            'bank_account_name' => $this->faker->name(),
            'bank_account_number' => (string) $this->faker->numerify('##########'),
            'bank_branch' => $this->faker->randomElement(['Kairaba Avenue', 'Senegambia', 'Westfield', 'Brusubi']),
            'commission_percent' => $this->faker->randomFloat(2, 5, 15),
        ];
    }
}
