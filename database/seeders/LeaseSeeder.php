<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Lease;
class LeaseSeeder extends Seeder
{
    public function run(): void
    {
        Lease::factory()->count(10)->create();
    }
}
