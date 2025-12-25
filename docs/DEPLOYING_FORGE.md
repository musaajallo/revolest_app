# Deploying to Laravel Forge

This guide covers deploying your Laravel Filament real estate application to a production server using Laravel Forge.

---

## Prerequisites

- A Laravel Forge account (https://forge.laravel.com)
- A provisioned server (e.g., DigitalOcean, AWS, Linode, Vultr, Hetzner)
- SSH access to your server
- A domain name (optional, but recommended)
- Your project in a Git repository (GitHub, GitLab, Bitbucket, etc.)

---

## 1. Prepare your repository

- Ensure your code is pushed to a remote Git repository.
- Add `.env.example` to your repo (never commit `.env`).
- Commit all changes and push.

---

## 2. Provision a server in Forge

- Log in to Forge and click "Create Server".
- Choose your provider, region, and server size.
- Wait for Forge to provision and configure the server.

---

## 3. Create a new site

- In Forge, go to your server and click "Create Site".
- Enter your domain (or subdomain) and select the web directory (usually `public`).
- Choose "Install a new site".

---

## 4. Connect your repository

- In the site settings, click "Git Repository".
- Enter your repository URL and branch (e.g., `main` or `master`).
- If private, add your deploy key to your repo's settings.
- Click "Install Repository".

---

## 5. Configure environment variables

- In Forge, go to your site's "Environment" tab.
- Copy your local `.env` settings and update for production:
  - `APP_ENV=production`
  - `APP_DEBUG=false`
  - Set correct `DB_*` credentials (Forge provides these)
  - Set mail settings (`MAIL_*`)
  - Set any third-party API keys
- Save changes.

---

## 6. Set up deployment script

Forge runs a deployment script on each push. Recommended script:

```bash
cd /home/forge/your-domain.com

# Install PHP dependencies
composer install --no-interaction --prefer-dist --optimize-autoloader

# Install Node dependencies and build assets
npm ci --no-audit --no-progress
npm run build

# Run migrations
php artisan migrate --force

# Clear and cache config/routes/views
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Link storage
php artisan storage:link

# Restart queue workers (if used)
php artisan queue:restart
```

Edit paths as needed for your server/site.

---

## 7. SSL (HTTPS)

- In Forge, go to your site's "SSL" tab.
- Click "Let's Encrypt" for free SSL, or upload your own certificate.
- Enable "Force HTTPS" for security.

---

## 8. Queue workers (optional)

- If you use jobs/notifications, set up a queue worker in Forge:
  - Go to "Workers" tab, add a new worker:
    - Command: `php artisan queue:work`
    - User: `forge`
    - Directory: `/home/forge/your-domain.com`

---

## 9. Scheduler (optional)

- Forge can manage Laravel's scheduler:
  - Go to "Scheduler" tab, add a new schedule:
    - Command: `php artisan schedule:run`
    - Frequency: Every minute

---

## 10. Database backups (recommended)

- Set up automated backups in Forge under the "Database" tab.
- Choose frequency and retention policy.

---

## 11. Filament admin setup

- After first deploy, create an admin user:

```bash
php artisan tinker
```

Then in Tinker:

```php
use App\Models\User;
User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => bcrypt('password')]);
```

- Visit `/admin` to log in.

---

## 12. Common troubleshooting

- **White screen or 500 error:** Check `.env` settings, run `php artisan config:cache`, check logs in `storage/logs`.
- **Assets not loading:** Run `npm run build`, ensure `public/build` exists, check Vite config.
- **Migrations fail:** Check DB credentials, run `php artisan migrate:fresh --force` if safe.
- **Queue not working:** Ensure worker is running, check logs.

---

## 13. Useful Forge features

- **Firewall:** Manage server firewall rules.
- **Monitoring:** Enable server monitoring for uptime and resource usage.
- **Daemons:** Run background processes.
- **Sites:** Host multiple apps/sites on one server.

---

## 14. Final checklist

- [ ] Code pushed to repo
- [ ] Server provisioned
- [ ] Site created and domain set
- [ ] Repo connected
- [ ] Environment variables set
- [ ] Deployment script configured
- [ ] SSL enabled
- [ ] Queue/scheduler set up (if needed)
- [ ] Database backups enabled
- [ ] Admin user created

---

For more, see the official Forge docs: https://forge.laravel.com/docs/

If you need help with a specific step or want to automate admin user creation, let me know!
