# Role-Based Access — Implementation Plan

This document captures the audit findings and the planned changes for owner / tenant / agent admin areas. Implementation starts immediately after this doc lands; this is the spec used to drive the diff.

## Audit summary

`User::canAccessPanel()` returns `true` for everyone, so any authenticated user can land on `/admin`. Per-role gating is supposed to happen at the page and resource level. Today it is partial:

- Role-specific dashboards (`Dashboard`, `AgentDashboard`, `OwnerDashboard`, `TenantDashboard`) are correctly gated by `canAccess()`.
- Several core resources have **no `canAccess()` at all** — every authenticated user (including `tenant`, `owner`, `agent`, `user`) sees them in the sidebar: `PropertyResource`, `ListingResource` (nav off but route open), `LeaseResource`, `PaymentResource`, `ReceiptResource`, `RepairRequestResource`.
- **No resource scopes its queries by the authenticated user.** Every `getEloquentQuery()` override only does `withoutGlobalScopes([SoftDeletingScope::class])`. So when a logged-in owner *does* navigate to `/admin/properties`, they see and can edit every property in the database — not just their own.
- `Dashboard`, `InquiryResource`, `ContactSubmissionResource` exclude the `admin` role from access, almost certainly an oversight.
- The `owners` table no longer has `user_id` (removed in `2026_04_13_000003_remove_user_id_from_owners_table`), but `OwnerStatsWidget`, `OwnerPropertiesWidget`, `OwnerRecentPaymentsWidget`, and `User::owner()` still reference `owners.user_id`. The owner dashboard is therefore **broken at runtime today** — any owner login throws an "unknown column `user_id`" SQL error.

## Decisions

1. **Re-add `owners.user_id`** rather than match owners to users by email. The widgets and the `User::owner()` `hasOne` already assume this column. Re-adding it is the smallest fix that makes both the dashboard and resource scoping correct, and matches the `tenants` and `agents` table shape for consistency. Migration is purely additive (nullable, indexed FK); existing owner rows stay unlinked until an admin assigns them via the Owner edit page in Filament.

2. **Fail-closed scoping.** When an owner/tenant/agent user has no linked Owner/Tenant/Agent record (`$user->owner` is null), the scoped query returns zero rows rather than falling back to the unscoped query. A user whose link record was deleted should not start seeing every other user's data.

3. **Inline scoping in each resource's `getEloquentQuery`.** No shared trait — the join shapes differ per resource (Property → owner_id; Lease → tenant_id or via property; Receipt → only via payment), and inlining keeps the rule next to its resource.

4. **Skip the per-role nav polish (item c) this round.** The user explicitly asked for items (a) + (b) only. A follow-up pass can hide irrelevant nav groups and reorder.

5. **Skip favicon wiring this round.** The user asked about favicons separately; surfaced the gap (upload exists but isn't rendered anywhere) but hasn't said go yet.

## Resource access matrix (`canAccess`)

| Resource | super_admin | admin | agent | owner | tenant |
|---|:-:|:-:|:-:|:-:|:-:|
| `Dashboard` (was super_admin only) | ✓ | **✓ added** | — | — | — |
| `PropertyResource` | ✓ | ✓ | ✓ | ✓ | — |
| `ListingResource` | ✓ | ✓ | ✓ | ✓ | — |
| `LeaseResource` | ✓ | ✓ | ✓ | ✓ | ✓ |
| `PaymentResource` | ✓ | ✓ | ✓ | ✓ | ✓ |
| `ReceiptResource` | ✓ | ✓ | ✓ | ✓ | ✓ |
| `RepairRequestResource` | ✓ | ✓ | ✓ | ✓ | ✓ |
| `InquiryResource` (admin was missing) | ✓ | **✓ added** | ✓ | ✓ | — |
| `ContactSubmissionResource` (admin was missing) | ✓ | **✓ added** | — | — | — |

All other resources stay as-is — already correctly gated:

- `TenantResource`, `OwnerResource`, `AgentResource`, `UserResource` → `super_admin` + `admin` only.
- `ComplaintResource` → `super_admin`, `owner`, `agent`, `tenant`.
- `PageResource` → `super_admin`, `admin`.
- All Submissions-group resources (Land/Built/Purchase/Rental/Pet/CustomerFeedback) → `super_admin`, `admin`, `agent`.

## Row scoping (`getEloquentQuery`)

`super_admin` and `admin` always see every row (no scoping). All others scoped per the table below. The pattern in each resource:

```php
public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    $user = auth()->user();
    if (!$user || in_array($user->role, ['super_admin', 'admin'])) {
        return $query;
    }

    if ($user->role === 'owner') {
        return $query->where('owner_id', $user->owner?->id ?? 0);
    }
    // ... per-role rules
    return $query->whereRaw('1=0'); // fail-closed catch-all
}
```

| Resource | owner sees | tenant sees | agent sees |
|---|---|---|---|
| Property | own (`owner_id = $user->owner->id`) | — | properties with a listing where `agent_id = $user->agent->id` |
| Listing | listings on own properties (via `property.owner_id`) | — | own (`agent_id`) |
| Lease | leases on own properties | own (`tenant_id`) | leases on own listings |
| Payment | own (`owner_id`) | own (`tenant_id`) | through `lease.property.listings.agent_id` |
| Receipt | through `payment.owner_id` | through `payment.tenant_id` | through `payment.lease.property.listings.agent_id` |
| RepairRequest | requests on own properties (via `property.owner_id`) | own (`tenant_id`) | requests on own listings |
| Inquiry | inquiries on own properties (via `listing.property.owner_id`) | — | inquiries on own listings (via `listing.agent_id`) |

Notes:

- `payments` has both `tenant_id` and `owner_id` columns — direct scopes work. Receipt only has `payment_id`, so we go through the payment.
- Listing visibility for owners is intentional: owners need to see (and edit) listings on their own properties.
- Tenant has no ability to see Property or Listing — their world is leases, payments they made, receipts on those payments, and repair requests they filed.

## Migration

`database/migrations/<ts>_add_user_id_to_owners_table.php`:

```php
Schema::table('owners', function (Blueprint $table) {
    $table->foreignId('user_id')
        ->nullable()
        ->after('id')
        ->constrained('users')
        ->nullOnDelete();
});
```

Down: `dropConstrainedForeignId('user_id')`.

## Out of scope (deferred)

- **Favicon wiring** — the `site_favicon` upload field exists in Site Settings but no `<link rel="icon">` is in the public layout and `AdminPanelProvider` doesn't call `->favicon(...)`. To be done after the user confirms.
- **Per-role nav polish** — hide nav groups owners/tenants don't need (e.g. "Submissions" for owners), pick a sensible landing page per role beyond the dashboard, reorder nav items. Cosmetic.
- **Linking existing Owner rows to users** — admins do this manually via the Owner edit page once the migration runs. No bulk match script.
- **Login tabs** — explicitly skipped per user direction; post-login routing by role is sufficient.

## Verification

After implementation:

1. `php artisan migrate` — confirm new column.
2. `php -l` each modified file.
3. `php artisan test` — confirm no existing test regressions.
4. Tinker smoke check: for each role + resource pair, run `Resource::getEloquentQuery()` after `Auth::loginUsingId($userId)` and confirm no SQL errors and reasonable row counts.
