# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Stack

Laravel 12 + Filament 3 admin panel, PHP 8.2+, Tailwind 4 via Vite 7, Livewire (used via Filament + a single standalone notifications component). Default DB in `.env.example` is SQLite; production runs on MySQL (Forge). Tests run against in-memory SQLite with `CACHE_STORE=array`, `QUEUE_CONNECTION=sync`, `SESSION_DRIVER=array`, `BCRYPT_ROUNDS=4` (see `phpunit.xml`) — anything that depends on persisted cache/queue/session will silently no-op in tests.

Newcomer setup steps live in `README.md`; this file documents the non-obvious.

## Commands

```bash
# Full local dev stack (server + queue + log tail + vite, all concurrently)
# Uses `concurrently --kill-others` — killing any one process tears down the rest.
composer dev

# Individual services
php artisan serve
npm run dev
php artisan queue:work        # production uses queue:work; composer dev uses queue:listen
php artisan pail              # log tail

# Build / test / format
npm run build
composer test                 # runs `config:clear` then `php artisan test`
php artisan test --filter=ExampleTest::test_method   # single test
# Tests live under tests/Feature (HTTP/Filament/integration) and tests/Unit (pure PHP).
# Custom base class: tests/TestCase.php.
./vendor/bin/pint             # code style

# DB lifecycle
php artisan migrate
php artisan migrate:fresh --seed
php artisan db:seed
php artisan storage:link      # required: public site reads media via the `public` disk

# Filament-specific
php artisan filament:upgrade  # run after composer install / on deploy
php artisan make:filament-resource ModelName --soft-deletes --view --generate
```

The Forge deploy script (see `docs/forge-deployment.md`) runs `composer install`, `migrate --force`, `filament:upgrade`, `config:cache`, `route:cache`, `view:cache`, then `npm install && npm run build`. Production expects `FILESYSTEM_DISK=public` (commit `1329fd6` fixed media uploads breaking under the default `local` disk).

## Architecture

**Two surfaces, one codebase.**

1. **Public marketing/listings site** — plain Blade under `resources/views/public/*`, routed in `routes/web.php` via `App\Http\Controllers\PublicController`. Only 7 routes: home, properties index/show, agents index/show, contact (GET/POST), inquiry (POST). Public visibility is gated by `Property.status = 'active'` AND `available_from` null-or-past, plus a child `Listing` whose `status` is in `Listing::PUBLIC_STATUSES` (`for_rent`, `for_sale`).

2. **Filament admin panel** at `/admin` — configured in `app/Providers/Filament/AdminPanelProvider.php`. Resources, pages, and widgets are auto-discovered from `app/Filament/{Resources,Pages,Widgets}`. Navigation groups (fixed order): Dashboard, Management, Communication, Properties, CMS, System Management. Brand color is the green palette defined inline in the panel provider.

**Role-gated dashboards.** `User::role` is one of `super_admin | admin | agent | owner | tenant | user`. Each role gets a separate Filament Page (`Dashboard`, `AgentDashboard`, `OwnerDashboard`, `TenantDashboard`) whose `canAccess()` checks the role and which composes a different widget set from `app/Filament/Widgets/`. `User::canAccessPanel()` currently returns `true` for everyone — gating happens at the page level, not the panel level.

**Property ↔ Listing is the central domain split.**
- `Property` is the physical asset (address, owner, type, purpose).
- `Listing` is a unit/offer on that property (price, bedrooms, agent, `for_rent`/`for_sale`/etc.).
- `Property::booted()` normalizes `purpose` (`sale`/`rent`/`mixed`) and keeps `price` in sync with `sale_price`/`rental_price` on every save — be careful when editing this; `sale_price` is nulled when `purpose = 'rent'` and vice versa.
- `Listing::booted()` calls `Property::syncStatusFromUnits()` after every create/update/delete/restore/forceDelete, which flips the parent property's `status` to `active` iff any listing exists. Don't add a separate `status` toggle on Property without accounting for this.
- `Property::publicPrice()` / `publicDeposit()` / `publicAgentFee()` / `publicPriceLabel()` are the canonical accessors used by public views — use them rather than reading the raw fields, since they encode the purpose-vs-listing fallback rules.

**Activity logging is automatic via trait, not observers.** `App\Traits\LogsActivity` is mixed into every domain model (Property, Listing, User, Owner, Agent, Tenant, Lease, Payment, etc.). It writes to `activity_logs` on `created`/`updated`/`deleted`. The `updated` handler is wrapped in try/catch (commit `9f4eeb3`) so a logging failure can't break the underlying save — keep this pattern when extending. Login/logout activity is logged separately via `App\Listeners\AuthActivityListener` registered in `AppServiceProvider::boot()`.

**Settings are key/value with cache.** `Setting::get($key, $default)` and `Setting::set($key, $value)` use a 1-hour cache keyed `setting.{key}`; the model's `saved`/`deleted` hooks invalidate. Always use these accessors rather than querying the table directly.

**Soft deletes are pervasive.** All domain models use `SoftDeletes`. Filament resources are scaffolded with `--soft-deletes`; preserve this when generating new ones.

**Production-only HTTPS.** `AppServiceProvider::boot()` calls `URL::forceScheme('https')` only when `app()->environment('production')`. Do not unconditionally force HTTPS — it breaks local dev.

## Public lead-form submissions

Public consultation/listing PDFs (see `docs/forms/`) are implemented as **lead records** under the Filament "Submissions" navigation group, not as direct writes to canonical tables. Four models — `LandPurchaseLead`, `LandSaleLead`, `RentalConsultation`, `BuiltPropertyListingLead` — all share a `status` column (`new → in_review → contacted → converted | closed`), a JSON `details` column for long-tail checkbox fields, and `signed_name`/`signed_at`/`ip_address`/`user_agent`/`submitted_at` audit columns.

- Public forms live at `/forms/*`, controller `App\Http\Controllers\FormsController`, views under `resources/views/public/forms/`. Shared Blade components for the page header, agreement block, and field rendering live under `resources/views/components/public/forms/partials/` (anonymous components — they MUST live under `components/`).
- The Policies & Fees text shown above the signature block is loaded from `settings` rows in the `policies` group (keys: `policy.land_purchase`, `policy.land_sale`, `policy.rental_consultation`, `policy.rental_weekly_agent_fees`, `policy.rental_yearly_agent_fees`, `policy.built_property_listing`). Edit the text via the Site Settings admin page — no redeploy needed. Defaults are seeded by `database/seeders/FormPolicySeeder.php`.
- The two listing-side resources (`LandSaleLeadResource`, `BuiltPropertyListingLeadResource`) expose a "Convert to Property" row action that creates a draft `Property` record (status `inactive`) and stores the FK back on `converted_property_id`. Always go through this action — don't write to `properties` directly from a lead — because `Property::booted()` price/purpose normalization needs to fire.
- Each resource has a `getNavigationBadge()` showing the count of `status = 'new'` records in warning color. There is no Livewire dropdown for lead types (the existing `InquiryNotifications` Livewire component still only counts `Inquiry`).
- **Maintenance Request** is *not* a separate model — it writes to the existing `RepairRequest` table (which had `property_id`/`tenant_id`/`submitted_at` made nullable in `2026_05_03_000006`). Walk-in submissions land with nullable FKs; admins link them during triage. New columns: `first_name`/`last_name`/`email`/`phone`/`property_address`/`apartment_number` for walk-ins, plus `priority`, `category`, `preferred_visit`, `has_pets`/`pet_notes`, `permission_to_enter` (required), `tenant_signature_name`/`signed_at`/`ip_address`/`user_agent`, and completion fields (`completed_at`/`completed_by_name`/`completion_notes`). `RepairRequestResource` lives under the **Properties** group, not Submissions, since it's the same canonical model.
- **Pet Application** is its own `PetApplication` model — multi-pet via JSON `pets` column (1–5 entries). The public form uses Alpine.js for dynamic add/remove of pet entries; checkbox state is captured via `<input type="hidden">` siblings so unchecked boxes still POST `0`. Admin form uses Filament `Repeater` over the same JSON shape.

## Repo conventions worth knowing

- Factories must use `$this->faker`, not the global `fake()` helper. Commit `314ca94` reverted a sweep that changed this; the global helper breaks under Forge's environment.
- The `inquiries` table allows `listing_id = null` — `PublicController::storeContact()` reuses the `Inquiry` model for general contact messages by storing them with no listing and a `[Subject] message` body. There is also a separate `ContactSubmission` model/resource; both currently exist.
- `bootstrap/providers.php` only registers `AppServiceProvider` and `AdminPanelProvider`. There is no separate `EventServiceProvider` — auth event listeners are wired manually in `AppServiceProvider::boot()`.
- `routes/web.php` is intentionally minimal (only public routes); Filament registers its own routes automatically via the panel provider.
- `docs/` contains operational guides and design notes — check there before duplicating setup or design instructions. Note `forge-deployment.md` and `DEPLOYING_FORGE.md` overlap; `forge-deployment.md` is the one referenced from this file.
