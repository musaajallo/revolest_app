<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            AgentSeeder::class,
            OwnerSeeder::class,
            TenantSeeder::class,
            PropertySeeder::class,
            ListingSeeder::class,
            LeaseSeeder::class,
            PaymentSeeder::class,
            InquirySeeder::class,
            RepairRequestSeeder::class,
            ComplaintSeeder::class,
            ReceiptSeeder::class,
        ]);
    }
}
