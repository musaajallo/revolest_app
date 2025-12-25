# Tenant Model & Filament Resource

## Purpose
Represents a tenant who leases properties, makes payments, and submits requests.

## Recommended Fields
- name (string)
- email (string, unique)
- phone (string)
- photo (string or media relation)
- user_id (foreign key, if using User model for authentication)

## Relationships
- Has many Leases
- Has many Payments
- Has many RepairRequests
- Has many Complaints

## Artisan Commands
### Create Eloquent Model & Migration
```bash
php artisan make:model Tenant -m
```

### Create Filament Resource
```bash
php artisan make:filament-resource Tenant
```

- You can add `--generate` to auto-generate forms and tables:
```bash
php artisan make:filament-resource Tenant --generate
```

## Recommended Migration
```php
Schema::create('tenants', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('phone')->nullable();
    $table->string('photo')->nullable();
    $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
    $table->softDeletes();
    $table->timestamps();
});

// Relationships (in other tables):
// - Leases: tenant_id (foreign key)
// - Payments: tenant_id (foreign key)
// - RepairRequests: tenant_id (foreign key)
// - Complaints: tenant_id (foreign key)
```

## Filament Resource Flags
- Use `--soft-deletes` to enable soft deletion management:
  ```bash
  php artisan make:filament-resource Tenant --soft-deletes --generate
  ```
- Use `--view` if you want a read-only view page for tenants.

### Create Filament Resource
To scaffold the Filament resource for tenant management, use:

```bash
php artisan make:filament-resource Tenant --soft-deletes --view --generate
```

- `--soft-deletes` allows restoring deleted tenants and maintaining audit trails.
- `--view` provides a read-only detail page for tenant information.

## Test Data: Seeder & Factory

### Create Factory
```bash
php artisan make:factory TenantFactory --model=Tenant
```

Example factory (`database/factories/TenantFactory.php`):
```php
public function definition()
{
    return [
        'name' => $this->faker->name,
        'email' => $this->faker->unique()->safeEmail,
        'phone' => $this->faker->phoneNumber,
        'photo' => null,
        'user_id' => \App\Models\User::factory(),
    ];
}
```

### Create Seeder
```bash
php artisan make:seeder TenantSeeder
```

Example seeder (`database/seeders/TenantSeeder.php`):
```php
public function run()
{
    \App\Models\Tenant::factory()->count(10)->create();
}
```

### Run Seeder
Add to `DatabaseSeeder.php`:
```php
$this->call(TenantSeeder::class);
```
Then run:
```bash
php artisan db:seed
```

## Next Steps
- Edit the migration to add all recommended fields.
- Set up relationships in the model (`app/Models/Tenant.php`).
- Configure forms, tables, and widgets in the Filament resource.
- Document any custom logic or UI in this file as you go.
