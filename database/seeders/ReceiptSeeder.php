<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Receipt;
class ReceiptSeeder extends Seeder
{
    public function run(): void
    {
        Receipt::factory()->count(10)->create();
    }
}
