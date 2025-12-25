# Owner Model & Filament Resource

## Purpose
Represents a property owner who can add and manage their own properties.

## Recommended Fields
- name (string)
- email (string, unique)
- phone (string)
- bio (text)
- photo (string or media relation)
- user_id (foreign key, if using User model for authentication)

## Relationships
- Has many Properties

## Artisan Commands
### Create Eloquent Model & Migration
```bash
php artisan make:model Owner -m
```

### Create Filament Resource
```bash
php artisan make:filament-resource Owner
```

```bash
php artisan make:filament-resource Owner --generate
```

### Create Filament Resource
To scaffold the Filament resource for owner management, use:

```bash
php artisan make:filament-resource Owner --soft-deletes --view --generate
```

- `--soft-deletes` allows restoring deleted owners and maintaining audit trails.
- `--view` provides a read-only detail page for owner information.

## Recommended Migration
```php
Schema::create('owners', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('phone')->nullable();
    $table->text('bio')->nullable();
    $table->string('photo')->nullable();
    $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
    $table->softDeletes();
    $table->timestamps();
});

// Relationships (in other tables):
// - Properties: owner_id (foreign key)
```

## Filament Resource Flags
- Use `--soft-deletes` to enable soft deletion management:
	```bash
	php artisan make:filament-resource Owner --soft-deletes --generate
	```
- Use `--view` if you want a read-only view page for owners.

## Test Data: Seeder & Factory

### Create Factory
```bash
php artisan make:factory OwnerFactory --model=Owner
```

Example factory (`database/factories/OwnerFactory.php`):
```php
public function definition()
{
    return [
        'name' => $this->faker->name,
        'email' => $this->faker->unique()->safeEmail,
        'phone' => $this->faker->phoneNumber,
        'bio' => $this->faker->paragraph,
        'photo' => null,
        'user_id' => \App\Models\User::factory(),
    ];
}
```

### Create Seeder
```bash
php artisan make:seeder OwnerSeeder
```

Example seeder (`database/seeders/OwnerSeeder.php`):
```php
public function run()
{
    \App\Models\Owner::factory()->count(10)->create();
}
```

### Run Seeder
Add to `DatabaseSeeder.php`:
```php
$this->call(OwnerSeeder::class);
```
Then run:
```bash
php artisan db:seed
```
