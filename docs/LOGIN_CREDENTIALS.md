# Demo Account Login Credentials

This document contains the login credentials for all demo accounts in the Revolest Property Management System.

## Super Admin Account

| Field    | Value                      |
| -------- | -------------------------- |
| Email    | `admin@revolest.gm`      |
| Password | `password`                 |
| Name     | Mamadou A Jallow           |
| Role     | Super Admin                |

**Permissions:** Full system access including all resources, CSV exports, and contact submissions.

---

## Agent Account

| Field    | Value                      |
| -------- | -------------------------- |
| Email    | `agent@revolest.gm`      |
| Password | `password`                 |
| Name     | Agent Demo User            |
| Role     | Agent                      |

**Permissions:**
- Access to Agent Dashboard with listings and inquiries
- Manage assigned property listings
- View and respond to property inquiries
- Track listing performance

---

## Owner Account

| Field    | Value                      |
| -------- | -------------------------- |
| Email    | `owner@revolest.gm`      |
| Password | `password`                 |
| Name     | Owner Demo User            |
| Role     | Owner                      |

**Permissions:**
- Access to Owner Dashboard with properties and payments
- View owned properties
- Track rental payments
- View and respond to property inquiries
- Monitor property performance

---

## Tenant Account

| Field    | Value                      |
| -------- | -------------------------- |
| Email    | `tenant@revolest.gm`     |
| Password | `password`                 |
| Name     | Tenant Demo User           |
| Role     | Tenant                     |

**Permissions:**
- Access to Tenant Dashboard with lease and payment information
- View payment history
- Submit repair requests
- Submit complaints
- View lease details

---

## Access Level Summary

| Feature                  | Super Admin | Agent | Owner | Tenant | Admin |
|-------------------------|-------------|-------|-------|--------|-------|
| All Resources           | ✓           | ✗     | ✗     | ✗      | ✓     |
| CSV Exports             | ✓           | ✗     | ✗     | ✗      | ✗     |
| Contact Submissions     | ✓           | ✗     | ✗     | ✗      | ✗     |
| Property Inquiries      | ✓           | ✓     | ✓     | ✗      | ✗     |
| Agent Dashboard         | ✗           | ✓     | ✗     | ✗      | ✗     |
| Owner Dashboard         | ✗           | ✗     | ✓     | ✗      | ✗     |
| Tenant Dashboard        | ✗           | ✗     | ✗     | ✓      | ✗     |

---

## Notes

- These credentials are created by the `UserSeeder` when running database migrations and seeders.
- All demo accounts use the same password: `password`
- **Important:** Change all default passwords in production environments.
- The seeder also creates additional random test users with fake emails.

## Admin Panel Access

Access the admin panel at: `http://localhost:8000/admin`

## Running the Seeder

To seed the database with default users:

```bash
php artisan db:seed --class=UserSeeder
```

Or run all seeders:

```bash
php artisan db:seed
```

## Security Recommendations

1. **Production Deployment:**
   - Change all default passwords immediately
   - Use strong, unique passwords for each account
   - Enable two-factor authentication if available
   - Remove or disable demo accounts

2. **Development/Staging:**
   - Keep demo accounts separate from production data
   - Regularly reset demo account passwords
   - Monitor demo account activity logs
