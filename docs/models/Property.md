# Property Model & Filament Resource

## Purpose
Represents a real estate property (house, apartment, land, etc.) with all relevant details.

## Recommended Fields
- title (string)
- description (text)
- address (string)
- price (decimal)
- type (string: house, apartment, land, etc.)
- status (string: available, sold, rented)
- bedrooms (integer)
- bathrooms (integer)
- area (integer, square meters)
- images (json or media relation)
- owner_id (foreign key)

## Relationships
- Belongs to Owner
- Has many Listings
- Has many RepairRequests, Complaints, Leases

## Artisan Commands
### Create Eloquent Model & Migration
```bash
php artisan make:model Property -m
```

### Create Filament Resource
```bash
php artisan make:filament-resource Property
```

- This will generate the resource in `app/Filament/Resources/PropertyResource.php`.
- You can add `--generate` to auto-generate forms and tables (Filament v3+):
```bash
php artisan make:filament-resource Property --generate
```

### Create Filament Resource
To scaffold the Filament resource for property management, use:

```bash
php artisan make:filament-resource Property --soft-deletes --view --generate
```

- `--soft-deletes` allows restoring deleted properties and maintaining audit trails.
- `--view` provides a read-only detail page for property information.

## Recommended Migration
```php
Schema::create('properties', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('description')->nullable();
    $table->string('address');
    $table->decimal('price', 15, 2);
    $table->string('type');
    $table->string('status')->default('available');
    $table->integer('bedrooms')->nullable();
    $table->integer('bathrooms')->nullable();
    $table->integer('area')->nullable();
    $table->json('images')->nullable();
    $table->foreignId('owner_id')->constrained('owners')->onDelete('cascade');
    $table->softDeletes();
    $table->timestamps();
});

// Relationships (in other tables):
// - Listings: property_id (foreign key)
// - RepairRequests: property_id (foreign key)
// - Complaints: property_id (foreign key)
// - Leases: property_id (foreign key)
```

## Filament Resource Flags
- Use `--soft-deletes` to enable soft deletion management:
  ```bash
  php artisan make:filament-resource Property --soft-deletes --generate
  ```
- Use `--view` if you want a read-only view page for properties.

## Test Data: Seeder & Factory

### Create Factory
```bash
php artisan make:factory PropertyFactory --model=Property
```

Example factory (`database/factories/PropertyFactory.php`):
```php
public function definition()
{
    return [
        'title' => $this->faker->sentence,
        'description' => $this->faker->paragraph,
        'address' => $this->faker->address,
        'price' => $this->faker->randomFloat(2, 50000, 500000),
        'type' => $this->faker->randomElement(['house', 'apartment', 'land']),
        'status' => 'available',
        'bedrooms' => $this->faker->numberBetween(1, 5),
        'bathrooms' => $this->faker->numberBetween(1, 3),
        'area' => $this->faker->numberBetween(50, 500),
        'images' => json_encode([]),
        'owner_id' => \App\Models\Owner::factory(),
    ];
}
```

### Create Seeder
```bash
php artisan make:seeder PropertySeeder
```

Example seeder (`database/seeders/PropertySeeder.php`):
```php
public function run()
{
    \App\Models\Property::factory()->count(20)->create();
}
```

### Run Seeder
Add to `DatabaseSeeder.php`:
```php
$this->call(PropertySeeder::class);
```
Then run:
```bash
php artisan db:seed
```

## Next Steps
- Edit the migration to add all recommended fields.
- Set up relationships in the model (`app/Models/Property.php`).
- Configure forms, tables, and widgets in the Filament resource.
- Document any custom logic or UI in this file as you go.
