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
            'issued_at' => $this->faker->dateTimeThisYear(),
            'file' => $this->faker->filePath(),
            'amount' => $this->faker->numberBetween(1000, 5000),
            'description' => $this->faker->sentence(),
        ];
    }
}
