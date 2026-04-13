<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Property;
use App\Models\Listing;
class ListingSeeder extends Seeder
{
    public function run(): void
    {
        Property::query()
            ->where('type', '!=', 'land')
            ->get()
            ->each(function (Property $property): void {
                Listing::factory()
                    ->count(random_int(1, 3))
                    ->for($property)
                    ->create();
            });
    }
}
