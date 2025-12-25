# Receipt Model & Filament Resource

## Purpose
Represents a receipt issued for a payment made by a tenant or owner.

## Recommended Fields
- payment_id (foreign key)
- issued_at (timestamp)
- file (string or media relation)
- amount (decimal)
- description (text, optional)

## Relationships
- Belongs to Payment

## Artisan Commands
### Create Eloquent Model & Migration
```bash
php artisan make:model Receipt -m
```

### Create Filament Resource
To scaffold the Filament resource for receipt management, use:

```bash
php artisan make:filament-resource Receipt --soft-deletes --view --generate
```

- `--soft-deletes` allows restoring deleted receipts and maintaining audit trails.
- `--view` provides a read-only detail page for receipt information.

- You can add `--generate` to auto-generate forms and tables:
```bash
php artisan make:filament-resource Receipt --generate
```

## Recommended Migration
```php
Schema::create('receipts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('payment_id')->constrained('payments')->onDelete('cascade');
    $table->timestamp('issued_at');
    $table->decimal('amount', 15, 2);
    $table->string('file')->nullable();
    $table->text('description')->nullable();
    $table->timestamps();
});
```

## Test Data: Seeder & Factory

### Create Factory
```bash
php artisan make:factory ReceiptFactory --model=Receipt
```

Example factory (`database/factories/ReceiptFactory.php`):
```php
public function definition()
{
    return [
        'payment_id' => \App\Models\Payment::factory(),
        'issued_at' => $this->faker->dateTimeThisYear,
        'amount' => $this->faker->randomFloat(2, 500, 5000),
        'file' => null,
        'description' => $this->faker->sentence,
    ];
}
```

### Create Seeder
```bash
php artisan make:seeder ReceiptSeeder
```

Example seeder (`database/seeders/ReceiptSeeder.php`):
```php
public function run()
{
    \App\Models\Receipt::factory()->count(20)->create();
}
```

### Run Seeder
Add to `DatabaseSeeder.php`:
```php
$this->call(ReceiptSeeder::class);
```
Then run:
```bash
php artisan db:seed
```

## Next Steps
- Edit the migration to add all recommended fields.
- Set up relationships in the model (`app/Models/Receipt.php`).
- Configure forms, tables, and widgets in the Filament resource.
- Document any custom logic or UI in this file as you go.
