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
            'agent_id' => \App\Models\Agent::inRandomOrder()->first()?->id,
            'price' => fake()->numberBetween(50000, 500000),
            'status' => fake()->randomElement(['active', 'inactive', 'sold']),
            'published_at' => fake()->dateTimeThisYear(),
        ];
    }
}
