# Agent Model & Filament Resource

## Purpose
Represents a real estate agent who manages property listings and interacts with users.

## Recommended Fields
- name (string)
- email (string, unique)
- phone (string)
- bio (text)
- photo (string or media relation)
- user_id (foreign key, if using User model for authentication)

## Relationships
- Has many Listings
- Can be linked to Properties (optional)

## Artisan Commands
### Create Eloquent Model & Migration
```bash
php artisan make:model Agent -m
```

### Create Filament Resource
```bash
php artisan make:filament-resource Agent
```

- You can add `--generate` to auto-generate forms and tables:
```bash
php artisan make:filament-resource Agent --generate
```

## Recommended Migration
```php
Schema::create('agents', function (Blueprint $table) {
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
// - Listings: agent_id (foreign key)
```

## Filament Resource Flags
- Use `--soft-deletes` to enable soft deletion management:
  ```bash
  php artisan make:filament-resource Agent --soft-deletes --generate
  ```
- Use `--view` if you want a read-only view page for agents.

### Create Filament Resource
To scaffold the Filament resource for agent management, use:

```bash
php artisan make:filament-resource Agent --soft-deletes --view --generate
```

- `--soft-deletes` allows restoring deleted agents and maintaining audit trails.
- `--view` provides a read-only detail page for agent information.

## Test Data: Seeder & Factory

### Create Factory
```bash
php artisan make:factory AgentFactory --model=Agent
```

Example factory (`database/factories/AgentFactory.php`):
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
php artisan make:seeder AgentSeeder
```

Example seeder (`database/seeders/AgentSeeder.php`):
```php
public function run()
{
    \App\Models\Agent::factory()->count(10)->create();
}
```

### Run Seeder
Add to `DatabaseSeeder.php`:
```php
$this->call(AgentSeeder::class);
```
Then run:
```bash
php artisan db:seed
```

## Next Steps
- Edit the migration to add all recommended fields.
- Set up relationships in the model (`app/Models/Agent.php`).
- Configure forms, tables, and widgets in the Filament resource.
- Document any custom logic or UI in this file as you go.
