<?php
namespace Database\Factories;
use App\Models\Listing;
use Illuminate\Database\Eloquent\Factories\Factory;
class ListingFactory extends Factory
{
    protected $model = Listing::class;
    public function definition(): array
    {
        return [
            'property_id' => \App\Models\Property::inRandomOrder()->first()?->id,
            'agent_id' => $this->faker->boolean(25) ? null : \App\Models\Agent::inRandomOrder()->first()?->id,
            'unit_name' => $this->faker->optional()->bothify('Unit-##'),
            'floor_number' => $this->faker->numberBetween(0, 4),
            'price' => $this->faker->numberBetween(50000, 500000),
            'security_deposit' => $this->faker->optional()->numberBetween(10000, 120000),
            'agent_fee' => $this->faker->optional()->numberBetween(5000, 60000),
            'status' => $this->faker->randomElement(['for_rent', 'for_sale', 'rented', 'sold']),
            'area' => $this->faker->numberBetween(800, 3200),
            'bedrooms' => $this->faker->numberBetween(0, 6),
            'bathrooms' => $this->faker->numberBetween(0, 4),
            'guest_toilets' => $this->faker->numberBetween(0, 2),
            'has_dining_area' => $this->faker->boolean(),
            'boys_quarters' => $this->faker->optional()->numberBetween(0, 2),
            'kitchens' => $this->faker->optional()->numberBetween(1, 2),
            'has_guest_toilet' => $this->faker->boolean(),
            'amenities' => $this->faker->randomElements(['ac', 'borehole'], $this->faker->numberBetween(0, 2)),
            'description' => $this->faker->paragraph(),
            'listed_by_company' => $this->faker->boolean(20),
            'images' => collect(range(1, $this->faker->numberBetween(1, 15)))
                ->map(fn () => $this->faker->imageUrl())
                ->values()
                ->all(),
        ];
    }
}
