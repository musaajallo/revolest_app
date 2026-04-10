<?php
namespace Database\Factories;
use App\Models\Receipt;
use Illuminate\Database\Eloquent\Factories\Factory;
class ReceiptFactory extends Factory
{
    protected $model = Receipt::class;
    public function definition(): array
    {
        return [
            'payment_id' => \App\Models\Payment::inRandomOrder()->first()?->id,
            'issued_at' => fake()->dateTimeThisYear(),
            'file' => fake()->filePath(),
            'amount' => fake()->numberBetween(1000, 5000),
            'description' => fake()->sentence(),
        ];
    }
}
