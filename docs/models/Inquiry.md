# Inquiry Model & Filament Resource

## Purpose
Represents a user inquiry about a property listing.

## Recommended Fields
- listing_id (foreign key)
- name (string)
- email (string)
- phone (string)
- message (text)
- status (string: new, read, closed)

## Relationships
- Belongs to Listing

## Artisan Commands
### Create Eloquent Model & Migration
```bash
php artisan make:model Inquiry -m
```

### Create Filament Resource
To scaffold the Filament resource for inquiry management, use:

```bash
php artisan make:filament-resource Inquiry --view --generate
```

- `--view` provides a read-only detail page for inquiry information.

- You can add `--generate` to auto-generate forms and tables:
```bash
php artisan make:filament-resource Inquiry --generate
```

## Recommended Migration
```php
Schema::create('inquiries', function (Blueprint $table) {
    $table->id();
    $table->foreignId('listing_id')->constrained('listings')->onDelete('cascade');
    $table->string('name');
    $table->string('email');
    $table->string('phone')->nullable();
    $table->text('message');
    $table->string('status')->default('new');
    $table->timestamps();
});
```

## Test Data: Seeder & Factory

### Create Factory
```bash
php artisan make:factory InquiryFactory --model=Inquiry
```

Example factory (`database/factories/InquiryFactory.php`):
```php
public function definition()
{
    return [
        'listing_id' => \App\Models\Listing::factory(),
        'name' => $this->faker->name,
        'email' => $this->faker->safeEmail,
        'phone' => $this->faker->phoneNumber,
        'message' => $this->faker->paragraph,
        'status' => 'new',
    ];
}
```

### Create Seeder
```bash
php artisan make:seeder InquirySeeder
```

Example seeder (`database/seeders/InquirySeeder.php`):
```php
public function run()
{
    \App\Models\Inquiry::factory()->count(30)->create();
}
```

### Run Seeder
Add to `DatabaseSeeder.php`:
```php
$this->call(InquirySeeder::class);
```
Then run:
```bash
php artisan db:seed
```

## Next Steps
- Edit the migration to add all recommended fields.
- Set up relationships in the model (`app/Models/Inquiry.php`).
- Configure forms, tables, and widgets in the Filament resource.
- Document any custom logic or UI in this file as you go.
