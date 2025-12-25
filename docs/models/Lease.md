# Lease Model & Filament Resource

## Purpose
Represents a lease agreement between a tenant and a property.

## Recommended Fields
- property_id (foreign key)
- tenant_id (foreign key)
- start_date (date)
- end_date (date)
- rent_amount (decimal)
- status (string: active, expired, terminated)
- contract_file (string or media relation)

## Relationships
- Belongs to Property
- Belongs to Tenant
- Has many Payments

## Artisan Commands
### Create Eloquent Model & Migration
```bash
php artisan make:model Lease -m
```

### Create Filament Resource
To scaffold the Filament resource for lease management, use:

```bash
php artisan make:filament-resource Lease --soft-deletes --view --generate
```

- `--soft-deletes` allows restoring deleted leases and maintaining audit trails.
- `--view` provides a read-only detail page for lease information.

- You can add `--generate` to auto-generate forms and tables:
```bash
php artisan make:filament-resource Lease --generate
```

## Recommended Migration
```php
Schema::create('leases', function (Blueprint $table) {
    $table->id();
    $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
    $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
    $table->date('start_date');
    $table->date('end_date');
    $table->decimal('rent_amount', 15, 2);
    $table->string('status')->default('active');
    $table->string('contract_file')->nullable();
    $table->softDeletes();
    $table->timestamps();
});

// Relationships (in other tables):
// - Payments: lease_id (foreign key)
```

## Test Data: Seeder & Factory

### Create Factory
```bash
php artisan make:factory LeaseFactory --model=Lease
```

Example factory (`database/factories/LeaseFactory.php`):
```php
public function definition()
{
    return [
        'property_id' => \App\Models\Property::factory(),
        'tenant_id' => \App\Models\Tenant::factory(),
        'start_date' => $this->faker->date(),
        'end_date' => $this->faker->date(),
        'rent_amount' => $this->faker->randomFloat(2, 50000, 500000),
        'status' => 'active',
        'contract_file' => null,
    ];
}
```

### Create Seeder
```bash
php artisan make:seeder LeaseSeeder
```

Example seeder (`database/seeders/LeaseSeeder.php`):
```php
public function run()
{
    \App\Models\Lease::factory()->count(15)->create();
}
```

### Run Seeder
Add to `DatabaseSeeder.php`:
```php
$this->call(LeaseSeeder::class);
```
Then run:
```bash
php artisan db:seed
```

## Next Steps
- Edit the migration to add all recommended fields.
- Set up relationships in the model (`app/Models/Lease.php`).
- Configure forms, tables, and widgets in the Filament resource.
- Document any custom logic or UI in this file as you go.
