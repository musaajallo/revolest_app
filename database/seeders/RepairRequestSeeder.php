<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\RepairRequest;
class RepairRequestSeeder extends Seeder
{
    public function run(): void
    {
        RepairRequest::factory()->count(10)->create();
    }
}
