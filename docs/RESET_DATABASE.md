# How to Reset Your Laravel Filament Database and Migrations

This guide explains how to completely reset your database and migration history for a fresh start in your Laravel Filament project.

---

## Quick Step-by-Step Summary

1. **Delete all migration files:**
   ```bash
   rm database/migrations/*.php
   ```
2. **Drop and recreate the database:**
   ```bash
   mysql -u root -p -e 'DROP DATABASE IF EXISTS filament_db; CREATE DATABASE filament_db;'
   ```
3. **Create migration for users table with soft deletes:**
   ```bash
   php artisan make:migration create_users_table --create=users
   ```
   - Edit migration to include `$table->softDeletes();`
4. **Update User model for soft deletes:**
   - Add `use Illuminate\Database\Eloquent\SoftDeletes;` and `use SoftDeletes;` to `User.php`.
5. **Run migrations:**
   ```bash
   php artisan migrate
   ```
6. **Create cache table migration (if using database cache):**
   ```bash
   php artisan cache:table
   php artisan migrate
   ```
7. **Run the seeder:**
   ```bash
   php artisan db:seed
   ```
8. **Default login details:**
   - Check your `DatabaseSeeder.php` for seeded user credentials (e.g., email and password).
9. **Verify everything:**
   - Access Filament admin panel and confirm users table, cache table, and soft deletes work.

---

## 1. Delete All Migration Files

Remove all migration files to clear migration history:

```bash
rm database/migrations/*.php
```

---

## 2. Drop and Recreate the Database

Use MySQL commands to drop and recreate your database (replace `filament_db` with your actual database name):

```bash
mysql -u root -p -e 'DROP DATABASE IF EXISTS filament_db; CREATE DATABASE filament_db;'
```
- Enter your MySQL root password when prompted.

---

## 3. Recreate Migrations

Generate new migration files for your models. For example, to create a users table with soft deletes:

```bash
php artisan make:migration create_users_table --create=users
```
- Edit the migration to include `$table->softDeletes();` for soft delete support.

---

## 4. Update Models for Soft Deletes

In your model (e.g., `User.php`), add:

```php
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use SoftDeletes;
    // ...existing code...
}
```

---

## 5. Run Migrations

Apply your new migrations to the fresh database:

```bash
php artisan migrate
```

---

## 6. Reseed the Database (Optional)

If you have seeders, run:

```bash
php artisan db:seed
```

---

## 7. Verify Everything

- Check your admin panel and resources for errors.
- Confirm that soft deletes work as expected.

---

**Tip:** Always back up your database before resetting in production environments.
