# Running the application (Development)

This document explains how to run the Laravel + Filament real estate application locally using `npm` for frontend assets and `php artisan` for backend tasks.

## Prerequisites

- PHP 8.1+ with extensions required by Laravel
- Composer
- Node.js 16+ and NPM (or Yarn)
- MySQL (or another supported database)
- Imagick or GD for image handling (optional)

## Quick setup

1. Install PHP dependencies

```bash
composer install
```

2. Install Node dependencies

```bash
npm install
```

3. Copy `.env` and generate app key

```bash
cp .env.example .env
php artisan key:generate
```

4. Configure the database

- Update the `.env` file with your DB credentials (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).

5. Run migrations (and optional seeders)

```bash
php artisan migrate
# optional: seed sample data
php artisan db:seed
```

6. Link storage for public access to uploaded files

```bash
php artisan storage:link
```

## Run frontend assets (Vite)

- Development (watch / hot reload):

```bash
npm run dev
```

- Build for production:

```bash
npm run build
```

If using the dev watcher, keep this terminal running while you develop.

## Run the application server

You can use Laravel's built-in server for local development:

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

Then open `http://127.0.0.1:8000` in your browser.

## Filament admin

- Filament is mounted at `/admin` by default (verify in your Filament configuration).
- Create an admin user either via a seeder or via Tinker:

```bash
php artisan tinker
```

Then in Tinker, for example:

```php
use App\Models\User;
User::factory()->create(['email' => 'admin@example.com', 'password' => bcrypt('password'), 'name' => 'Admin']);
```

Adjust fields/roles according to your app's auth/role implementation.

## Common maintenance commands

- Run migrations fresh (drops all tables):

```bash
php artisan migrate:fresh --seed
```

- Run queued jobs worker:

```bash
php artisan queue:work
```

- Run tests:

```bash
php artisan test
```

## Notes & troubleshooting

- If migrations fail due to foreign key order, ensure migration filenames/timestamps are ordered so referenced tables are created first (or run `php artisan migrate:rollback` then `php artisan migrate`).
- If you change environment variables, restart the dev server and rebuild frontend assets as needed.
- For production deployment, configure a proper web server (Nginx/Apache), set `APP_ENV=production` and run `npm run build`.

## Useful commands summary

```bash
# install deps
composer install
npm install

# setup
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link

# development
npm run dev
php artisan serve

# build for production
npm run build
php artisan migrate --force
```

---

If you'd like, I can add a small `database/seeders/AdminUserSeeder.php` and wire it into `DatabaseSeeder` to automatically create an admin account for development. Want me to add that next?