# Deploying to Laravel Forge

This guide covers deploying Revolest Property to Laravel Forge with automatic CI/CD from GitHub.

## Prerequisites

- A [Laravel Forge](https://forge.laravel.com) account
- Your project pushed to a GitHub repository (`musaajallo/revolest_app`)
- A server provisioned in Forge (DigitalOcean, AWS, Linode, etc.)

## Step 1: Create a New Site

1. Log in to [Laravel Forge Dashboard](https://forge.laravel.com)
2. Select your server.
3. Click **"New Site"**:
    - **Root Domain**: yourdomain.com
    - **Project Type**: PHP (Laravel)
    - **PHP Version**: 8.2+
    - **Web Directory**: `/public`
4. Once created, click **"Git Repository"**:
    - **Repository**: `musaajallo/revolest_app`
    - **Branch**: `main`
    - Check **"Install Composer Dependencies"**.

## Step 2: Configure Environment Variables

Go to the **"Environment"** tab and add the following (or use `.env.forge.example` as a template):

### Required Variables

```env
APP_NAME="Revolest Property"
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_KEY
APP_DEBUG=false
APP_URL=https://your-app.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=revolest_db
DB_USERNAME=forge
DB_PASSWORD=your_database_password

SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=public
```

## Step 3: Deployment Script

In the **"Deploy"** tab, ensure your deployment script builds assets and handles Filament upgrades:

```bash
cd /home/forge/yourdomain.com
git pull origin $FORGE_SITE_BRANCH

composer install --no-interaction --prefer-dist --optimize-autoloader

(flock -w 10 9 || exit 1
    echo "Restarting FPM..."; sudo -S service $FORGE_PHP_FPM restart ) 9>/tmp/fpmlock

if [ -f artisan ]; then
    php artisan migrate --force
    php artisan filament:upgrade
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

# Build assets with Vite
npm install
npm run build
```

## Step 4: Database & SSL

1. **Database**: Go to the server's **"Databases"** tab. Create `revolest_db` and ensure the `forge` user has access.
2. **SSL**: Go to the site's **"SSL"** tab and install a free **Let's Encrypt** certificate.

## Step 5: Queues & Scheduler

1. **Queues**: Go to the **"Queues"** tab and create a worker:
    - **Connection**: `database`
    - **Queue**: `default`
2. **Scheduler**: Go to the server's **"Scheduler"** tab and add:
    - **Command**: `php /home/forge/yourdomain.com/artisan schedule:run`
    - **Interval**: `Every Minute`

## Troubleshooting

### Assets Not Loading
Ensure `npm run build` is in your deployment script and `APP_URL` is set correctly in your `.env`.

### Filament Icons/Assets 404
Run `php artisan filament:assets` or ensure your deployment script includes `php artisan filament:upgrade`.
