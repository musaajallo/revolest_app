# User Model & Filament Resource

## Purpose

Represents all users in the system (admin, owner, agent, tenant, normal user) and manages authentication and roles.

## Recommended Fields

-   name (string)
-   email (string, unique)
-   password (string)
-   role (string: admin, owner, agent, tenant, user)
-   email_verified_at (timestamp, nullable)
-   remember_token (string, nullable)

## Relationships

-   Can be linked to Owner, Agent, Tenant (one-to-one)

## Artisan Commands

### Create Eloquent Model & Migration

```bash
php artisan make:model User -m
```

### Create Filament Resource

To scaffold the Filament resource for user management, use:

```bash
php artisan make:filament-resource User --soft-deletes --view --generate
```

-   `--soft-deletes` is recommended for users so you can restore deleted accounts and maintain audit trails.
-   `--view` provides a read-only detail page for user information.

## Recommended Migration

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('password');
    $table->string('role')->default('user');
    $table->timestamp('email_verified_at')->nullable();
    $table->rememberToken();
    $table->softDeletes();
    $table->timestamps();
});
```

// Relationships (in other tables):
// - Owners, Agents, Tenants: user_id (foreign key)

## Test Data: Seeder & Factory

### Create Factory

```bash
php artisan make:factory UserFactory --model=User
```

Example factory (`database/factories/UserFactory.php`):

```php
public function definition()
{
    return [
        'name' => $this->faker->name,
        'email' => $this->faker->unique()->safeEmail,
        'password' => bcrypt('password'),
        'role' => $this->faker->randomElement(['admin', 'owner', 'agent', 'tenant', 'user']),
        'email_verified_at' => now(),
    ];
}
```

### Create Seeder

```bash
php artisan make:seeder UserSeeder
```

Example seeder (`database/seeders/UserSeeder.php`):

```php
public function run()
{
    \App\Models\User::factory()->count(20)->create();
}
```

### Run Seeder

Add to `DatabaseSeeder.php`:

```php
$this->call(UserSeeder::class);
```

Then run:

```bash
php artisan db:seed
```

## Next Steps

-   Edit the migration to add all recommended fields.
-   Set up relationships in the model (`app/Models/User.php`).
-   Configure forms, tables, and widgets in the Filament resource.
-   Document any custom logic or UI in this file as you go.
