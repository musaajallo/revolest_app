# RepairRequest Model & Filament Resource

## Purpose
Represents a repair or maintenance request submitted by a tenant for a property.

## Recommended Fields
- property_id (foreign key)
- tenant_id (foreign key)
- description (text)
- status (string: new, in_progress, resolved, closed)
- submitted_at (timestamp)
- resolved_at (timestamp, nullable)

## Relationships
- Belongs to Property
- Belongs to Tenant

## Artisan Commands
### Create Eloquent Model & Migration
```bash
php artisan make:model RepairRequest -m
```

### Create Filament Resource
To scaffold the Filament resource for repair request management, use:

```bash
php artisan make:filament-resource RepairRequest --soft-deletes --view --generate
```

- `--soft-deletes` allows restoring deleted repair requests and maintaining audit trails.
- `--view` provides a read-only detail page for repair request information.

- You can add `--generate` to auto-generate forms and tables:
```bash
php artisan make:filament-resource RepairRequest --generate
```

## Recommended Migration
```php
Schema::create('repair_requests', function (Blueprint $table) {
    $table->id();
    $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
    $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
    $table->text('description');
    $table->string('status')->default('new');
    $table->timestamp('submitted_at');
    $table->timestamp('resolved_at')->nullable();
    $table->softDeletes();
    $table->timestamps();
});
```

## Test Data: Seeder & Factory

### Create Factory
```bash
php artisan make:factory RepairRequestFactory --model=RepairRequest
```

Example factory (`database/factories/RepairRequestFactory.php`):
```php
public function definition()
{
    return [
        'property_id' => \App\Models\Property::factory(),
        'tenant_id' => \App\Models\Tenant::factory(),
        'description' => $this->faker->paragraph,
        'status' => 'new',
        'submitted_at' => $this->faker->dateTimeThisYear,
        'resolved_at' => null,
    ];
}
```

### Create Seeder
```bash
php artisan make:seeder RepairRequestSeeder
```

Example seeder (`database/seeders/RepairRequestSeeder.php`):
```php
public function run()
{
    \App\Models\RepairRequest::factory()->count(10)->create();
}
```

### Run Seeder
Add to `DatabaseSeeder.php`:
```php
$this->call(RepairRequestSeeder::class);
```
Then run:
```bash
php artisan db:seed
```

## Next Steps
- Edit the migration to add all recommended fields.
- Set up relationships in the model (`app/Models/RepairRequest.php`).
- Configure forms, tables, and widgets in the Filament resource.
- Document any custom logic or UI in this file as you go.
