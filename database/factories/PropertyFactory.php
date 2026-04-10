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
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'address' => fake()->address(),
            'price' => fake()->numberBetween(50000, 500000),
            'type' => fake()->randomElement(['house', 'apartment', 'condo']),
            'status' => fake()->randomElement(['available', 'sold', 'rented']),
            'bedrooms' => fake()->numberBetween(1, 6),
            'bathrooms' => fake()->numberBetween(1, 4),
            'area' => fake()->numberBetween(50, 500),
            'images' => json_encode([fake()->imageUrl()]),
            'owner_id' => null,
        ];
    }
}
