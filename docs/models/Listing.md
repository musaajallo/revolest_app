# Listing Model & Filament Resource

## Purpose
Represents a property listing managed by an agent, available for rent or sale.

## Recommended Fields
- property_id (foreign key)
- agent_id (foreign key)
- price (decimal)
- status (string: active, inactive, sold)
- published_at (timestamp)

## Relationships
- Belongs to Property
- Belongs to Agent
- Has many Inquiries

## Artisan Commands
### Create Eloquent Model & Migration
```bash
php artisan make:model Listing -m
```

### Create Filament Resource
```bash
php artisan make:filament-resource Listing
```

- You can add `--generate` to auto-generate forms and tables:
```bash
php artisan make:filament-resource Listing --generate
```

## Recommended Migration
```php
Schema::create('listings', function (Blueprint $table) {
    $table->id();
    $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
    $table->foreignId('agent_id')->constrained('agents')->onDelete('cascade');
    $table->decimal('price', 15, 2);
    $table->string('status')->default('active');
    $table->timestamp('published_at')->nullable();
    $table->softDeletes();
    $table->timestamps();
});

// Relationships (in other tables):
// - Inquiries: listing_id (foreign key)
```

## Filament Resource Flags
- Use `--soft-deletes` to enable soft deletion management:
  ```bash
  php artisan make:filament-resource Listing --soft-deletes --generate
  ```
- Use `--view` if you want a read-only view page for listings.

### Create Filament Resource
To scaffold the Filament resource for listing management, use:

```bash
php artisan make:filament-resource Listing --soft-deletes --view --generate
```

- `--soft-deletes` allows restoring deleted listings and maintaining audit trails.
- `--view` provides a read-only detail page for listing information.

## Test Data: Seeder & Factory

### Create Factory
```bash
php artisan make:factory ListingFactory --model=Listing
```

Example factory (`database/factories/ListingFactory.php`):
```php
public function definition()
{
    return [
        'property_id' => \App\Models\Property::factory(),
        'agent_id' => \App\Models\Agent::factory(),
        'price' => $this->faker->randomFloat(2, 50000, 500000),
        'status' => 'active',
        'published_at' => $this->faker->dateTimeThisYear,
    ];
}
```

### Create Seeder
```bash
php artisan make:seeder ListingSeeder
```

Example seeder (`database/seeders/ListingSeeder.php`):
```php
public function run()
{
    \App\Models\Listing::factory()->count(20)->create();
}
```

### Run Seeder
Add to `DatabaseSeeder.php`:
```php
$this->call(ListingSeeder::class);
```
Then run:
```bash
php artisan db:seed
```

## Next Steps
- Edit the migration to add all recommended fields.
- Set up relationships in the model (`app/Models/Listing.php`).
- Configure forms, tables, and widgets in the Filament resource.
- Document any custom logic or UI in this file as you go.
