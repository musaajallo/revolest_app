# Revolest Property Management System

A comprehensive property management system built with Laravel and Filament, designed for managing rental properties, tenants, leases, and related operations.

## Features

### Admin Panel (Filament)
- **Property Management**: Create and manage property listings with detailed information
- **Tenant Management**: Track tenant information, leases, and rental history
- **Owner Management**: Manage property owners and their portfolios
- **Agent Management**: Handle real estate agents and their property assignments
- **Lease Management**: Create and monitor lease agreements with payment schedules
- **Payment Tracking**: Record and manage rental payments with receipt generation
- **Repair Requests**: Handle maintenance and repair requests from tenants
- **Complaint Management**: Track and resolve tenant complaints
- **Inquiry System**: Manage property inquiries from potential tenants
- **Activity Logging**: Comprehensive audit trail of system activities
- **Settings Management**: Configure system-wide settings and preferences

### Public Website
- **Property Listings**: Browse available properties with detailed information
- **Agent Profiles**: View agent information and their property listings
- **Contact Forms**: Submit inquiries and contact requests
- **Property Inquiries**: Express interest in specific properties
- **Dynamic Pages**: CMS-managed public pages

## Tech Stack

- **Framework**: Laravel 12.x
- **Admin Panel**: Filament 3.x
- **Frontend**: Tailwind CSS 4.x, Vite
- **Database**: MySQL
- **PHP**: 8.2+

## Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js and npm
- MySQL database

### Setup Steps

1. Clone the repository:
```bash
git clone git@github.com:musaajallo/revolest.git
cd revolest_app
```

2. Install dependencies:
```bash
composer install
npm install
```

3. Configure environment:
```bash
cp .env.example .env
```

4. Update `.env` file with your database credentials:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Run database migrations:
```bash
php artisan migrate
```

7. Seed the database (optional):
```bash
php artisan db:seed
```

8. Build frontend assets:
```bash
npm run build
```

## Development

Run the development environment:
```bash
composer dev
```

This will start:
- PHP development server (http://localhost:8000)
- Queue worker
- Log viewer (Pail)
- Vite dev server for hot module replacement

Or run services individually:
```bash
# Development server
php artisan serve

# Vite dev server
npm run dev

# Queue worker
php artisan queue:work

# Log viewer
php artisan pail
```

## Testing

Run the test suite:
```bash
composer test
# or
php artisan test
```

## Admin Access

Access the admin panel at: `http://localhost:8000/admin`

Default credentials can be found in the documentation (if seeded).

## Project Structure

```
app/
├── Filament/         # Filament admin resources
├── Http/             # Controllers and middleware
├── Models/           # Eloquent models
├── Livewire/         # Livewire components
├── Listeners/        # Event listeners
└── Traits/           # Reusable traits

resources/
├── views/
│   ├── public/       # Public website views
│   └── layouts/      # Layout templates
├── css/              # Stylesheets
└── js/               # JavaScript files

routes/
└── web.php           # Application routes
```

## License

This project is proprietary software. All rights reserved.

## Support

For support or inquiries, contact: info@revolest.gm
