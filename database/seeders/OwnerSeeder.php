<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Owner;
class OwnerSeeder extends Seeder
{
    public function run(): void
    {
        Owner::factory()->count(10)->create();
    }
}
