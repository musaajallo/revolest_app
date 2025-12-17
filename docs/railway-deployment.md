# Deploying to Railway

This guide covers deploying SA Property to Railway with automatic CI/CD from GitHub.

## Prerequisites

- A [Railway](https://railway.app) account
- Your project pushed to a GitHub repository
- A PostgreSQL or MySQL database (Railway can provision one)

## Step 1: Create a New Project

1. Log in to [Railway Dashboard](https://railway.app/dashboard)
2. Click **"New Project"**
3. Select **"Deploy from GitHub repo"**
4. Authorize Railway to access your GitHub account if prompted
5. Select your repository (`saproperty`)

## Step 2: Add a Database

1. In your project, click **"New"** → **"Database"**
2. Select **PostgreSQL** (recommended) or **MySQL**
3. Railway will provision the database and provide connection variables

## Step 3: Configure Environment Variables

Go to your app service → **"Variables"** tab and add the following:

### Required Variables

```env
APP_NAME=SA Property
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_KEY
APP_DEBUG=false
APP_URL=https://your-app.up.railway.app

LOG_CHANNEL=stderr
LOG_LEVEL=error

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true

CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=local

BCRYPT_ROUNDS=12
```

### Generate APP_KEY

Run locally and copy the generated key:

```bash
php artisan key:generate --show
```

### Database Variables

Click **"Add Reference"** and link the database variables:

For **PostgreSQL**:
```env
DATABASE_URL=${{Postgres.DATABASE_URL}}
DB_CONNECTION=pgsql
```

For **MySQL**:
```env
DB_CONNECTION=mysql
DB_HOST=${{MySQL.MYSQLHOST}}
DB_PORT=${{MySQL.MYSQLPORT}}
DB_DATABASE=${{MySQL.MYSQLDATABASE}}
DB_USERNAME=${{MySQL.MYSQLUSER}}
DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}
```

### Mail Configuration (Optional)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME=${APP_NAME}
```

## Step 4: Configure Build Settings

Railway should auto-detect the `Dockerfile`. Verify in **Settings**:

- **Builder**: Dockerfile
- **Watch Paths**: `/` (default)

### Custom Start Command (if needed)

If not using the Dockerfile CMD, set:

```
php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT
```

## Step 5: Generate a Domain

1. Go to **Settings** → **Networking**
2. Click **"Generate Domain"** to get a `*.up.railway.app` subdomain
3. Or add a custom domain

Update your `APP_URL` variable to match the generated domain.

## Step 6: Deploy

Railway automatically deploys when you push to the configured branch (usually `main`).

To trigger a manual deploy:
1. Go to your service
2. Click **"Deploy"** → **"Trigger Deploy"**

## CI/CD Configuration

### Automatic Deployments

By default, Railway deploys on every push to `main`. To customize:

1. Go to **Settings** → **Build & Deploy**
2. Configure:
   - **Root Directory**: `/` (or your app subdirectory)
   - **Watch Paths**: Paths that trigger a deploy
   - **Auto Deploy**: Enable/disable automatic deploys

### Branch Deployments

To deploy from a specific branch:

1. Go to **Settings**
2. Under **Service Source**, select your branch

### Environment-Specific Deployments

Create separate Railway environments for staging/production:

1. Go to your project
2. Click the environment dropdown (top-left)
3. Click **"New Environment"**
4. Name it (e.g., `staging`, `production`)
5. Each environment has its own variables and domains

## Monitoring & Logs

### View Logs

1. Go to your service
2. Click **"Deployments"** → select a deployment
3. Click **"View Logs"**

Or use the **"Logs"** tab for real-time logs.

### Health Checks

Add a health check endpoint in your Laravel routes:

```php
// routes/web.php
Route::get('/health', fn() => response()->json(['status' => 'ok']));
```

Configure in Railway:
1. **Settings** → **Networking**
2. Add health check path: `/health`

## Troubleshooting

### Build Fails with Missing PHP Extensions

The included `Dockerfile` installs required extensions. If you still have issues:

1. Check the build logs for specific missing extensions
2. Add them to the `docker-php-ext-install` line in `Dockerfile`

### Database Connection Errors

1. Verify database variables are correctly linked
2. Check `DB_CONNECTION` matches your database type (`pgsql` or `mysql`)
3. Ensure migrations have run (check deploy logs for migration output)

### Assets Not Loading

1. Ensure `npm run build` runs during the Docker build
2. Check `APP_URL` matches your actual domain
3. Verify `public/build` is being generated

### 502 Bad Gateway

1. Check if the app is listening on the correct port (`$PORT` environment variable)
2. Verify the start command uses `--port=$PORT` or `--port=${PORT:-8080}`
3. Check application logs for startup errors

## Useful Railway CLI Commands

Install the Railway CLI:

```bash
npm install -g @railway/cli
```

Common commands:

```bash
# Login
railway login

# Link to existing project
railway link

# View logs
railway logs

# Open Railway dashboard
railway open

# Run commands in Railway environment
railway run php artisan migrate

# SSH into your service (if enabled)
railway shell
```

## Cost Optimization

- **Starter Plan**: $5/month includes 500 hours of usage
- **Pro Plan**: Pay for what you use
- Use **"Sleep"** feature for non-production environments
- Monitor usage in **"Usage"** tab

## Security Checklist

- [ ] `APP_DEBUG=false` in production
- [ ] `APP_KEY` is set and kept secret
- [ ] Database credentials use Railway references (not hardcoded)
- [ ] `SESSION_ENCRYPT=true`
- [ ] HTTPS is enabled (automatic with Railway domains)
- [ ] Sensitive variables are not logged
