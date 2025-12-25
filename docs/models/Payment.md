# Payment Model & Filament Resource

## Purpose
Represents a payment made by a tenant or owner for rent, fees, or other charges.

## Recommended Fields
- lease_id (foreign key)
- tenant_id (foreign key)
- owner_id (foreign key, optional)
- amount (decimal)
- payment_date (date)
- method (string: card, bank, cash, etc.)
- status (string: pending, completed, failed)
- receipt_file (string or media relation)

## Relationships
- Belongs to Lease
- Belongs to Tenant
- Belongs to Owner (optional)
- Has one Receipt

## Artisan Commands
### Create Eloquent Model & Migration
```bash
php artisan make:model Payment -m
```

### Create Filament Resource
To scaffold the Filament resource for payment management, use:

```bash
php artisan make:filament-resource Payment --soft-deletes --view --generate
```

- `--soft-deletes` allows restoring deleted payments and maintaining audit trails.
- `--view` provides a read-only detail page for payment information.

- You can add `--generate` to auto-generate forms and tables:
```bash
php artisan make:filament-resource Payment --generate
```

## Recommended Migration
```php
Schema::create('payments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('lease_id')->nullable()->constrained('leases')->onDelete('set null');
    $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('set null');
    $table->foreignId('owner_id')->nullable()->constrained('owners')->onDelete('set null');
    $table->decimal('amount', 15, 2);
    $table->date('payment_date');
    $table->string('method');
    $table->string('status')->default('pending');
    $table->string('receipt_file')->nullable();
    $table->softDeletes();
    $table->timestamps();
});

// Relationships (in other tables):
// - Receipt: payment_id (foreign key)
```

## Test Data: Seeder & Factory

### Create Factory
```bash
php artisan make:factory PaymentFactory --model=Payment
```

Example factory (`database/factories/PaymentFactory.php`):
```php
public function definition()
{
    return [
        'lease_id' => \App\Models\Lease::factory(),
        'tenant_id' => \App\Models\Tenant::factory(),
        'owner_id' => \App\Models\Owner::factory(),
        'amount' => $this->faker->randomFloat(2, 500, 5000),
        'payment_date' => $this->faker->date(),
        'method' => $this->faker->randomElement(['card', 'bank', 'cash']),
        'status' => 'completed',
        'receipt_file' => null,
    ];
}
```

### Create Seeder
```bash
php artisan make:seeder PaymentSeeder
```

Example seeder (`database/seeders/PaymentSeeder.php`):
```php
public function run()
{
    \App\Models\Payment::factory()->count(30)->create();
}
```

### Run Seeder
Add to `DatabaseSeeder.php`:
```php
$this->call(PaymentSeeder::class);
```
Then run:
```bash
php artisan db:seed
```

## Next Steps
- Edit the migration to add all recommended fields.
- Set up relationships in the model (`app/Models/Payment.php`).
- Configure forms, tables, and widgets in the Filament resource.
- Document any custom logic or UI in this file as you go.
