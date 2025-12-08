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
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'message' => $this->faker->sentence(),
            'status' => $this->faker->randomElement(['new', 'viewed', 'closed']),
        ];
    }
}
