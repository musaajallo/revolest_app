<?php
namespace Database\Factories;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;
class PropertyFactory extends Factory
{
    protected $model = Property::class;
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'address' => $this->faker->address(),
            'price' => $this->faker->numberBetween(50000, 500000),
            'type' => $this->faker->randomElement(['house', 'apartment', 'condo']),
            'status' => $this->faker->randomElement(['available', 'sold', 'rented']),
            'bedrooms' => $this->faker->numberBetween(1, 6),
            'bathrooms' => $this->faker->numberBetween(1, 4),
            'area' => $this->faker->numberBetween(50, 500),
            'images' => json_encode([$this->faker->imageUrl()]),
            'owner_id' => null,
        ];
    }
}
