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
            'plus_code' => $this->faker->optional()->bothify('XXXX+XX'),
            'price' => $this->faker->numberBetween(50000, 500000),
            'type' => $this->faker->randomElement(['house', 'apartment', 'compound', 'duplex', 'commercial', 'land']),
            'purpose' => $this->faker->randomElement(['sale', 'rent', 'mixed']),
            'listing_category' => $this->faker->randomElement(['building', 'land']),
            'status' => 'inactive',
            'is_featured' => false,
            'available_from' => null,
            'is_storey_building' => $this->faker->boolean(35),
            'number_of_storeys' => $this->faker->optional(0.35)->numberBetween(1, 6),
            'bedrooms' => $this->faker->numberBetween(1, 6),
            'bathrooms' => $this->faker->numberBetween(1, 4),
            'area' => $this->faker->numberBetween(50, 500),
            'images' => collect(range(1, $this->faker->numberBetween(1, 15)))
                ->map(fn () => $this->faker->imageUrl())
                ->values()
                ->all(),
            'owner_id' => null,
        ];
    }
}
