<?php
namespace Database\Factories;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;
class PaymentFactory extends Factory
{
    protected $model = Payment::class;
    public function definition(): array
    {
        return [
            'lease_id' => \App\Models\Lease::inRandomOrder()->first()?->id,
            'tenant_id' => \App\Models\Tenant::inRandomOrder()->first()?->id,
            'owner_id' => \App\Models\Owner::inRandomOrder()->first()?->id,
            'amount' => fake()->numberBetween(1000, 5000),
            'payment_date' => fake()->date(),
            'method' => fake()->randomElement(['cash', 'bank', 'mobile']),
            'status' => fake()->randomElement(['paid', 'pending', 'failed']),
            'receipt_file' => fake()->filePath(),
        ];
    }
}
