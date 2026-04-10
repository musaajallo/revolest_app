<?php
namespace Database\Factories;
use App\Models\Inquiry;
use Illuminate\Database\Eloquent\Factories\Factory;
class InquiryFactory extends Factory
{
    protected $model = Inquiry::class;
    public function definition(): array
    {
        return [
            'listing_id' => \App\Models\Listing::inRandomOrder()->first()?->id,
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'message' => fake()->sentence(),
            'status' => fake()->randomElement(['new', 'viewed', 'closed']),
        ];
    }
}
