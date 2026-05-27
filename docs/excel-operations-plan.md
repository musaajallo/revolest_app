# Excel Operations — Implementation Plan

**Companion to:** [excel-operations-audit.md](excel-operations-audit.md)

**Goal:** Close every "missing column" flagged in the audit, so the app mirrors how Revolest currently runs the business in Excel. Decision: **keep the 5 separate lead models**, just add missing columns to each.

This plan is ordered by phase. Each phase ships independently and is safe to deploy on its own.

---

## Decisions locked in (formerly open questions)

The six product-level decisions that the Excel sheets didn't document have been resolved with pragmatic defaults. Each is captured in `excel-operations-audit.md` and can be reversed later by changing one column or constant.

- `Vld.` → lease validity, already covered by `Lease.end_date` + `Lease.status`. No new column.
- Commission rate → `Owner.commission_percent` (default), `Lease.commission_percent_override` (per-lease). Access via `$owner->commissionRateFor($lease)`.
- `CMS. EARN` → auto-calculated in `Payment::booted()` `creating` hook, admin-overridable in the form.
- Rent cycle → `Lease.rent_cycle` enum (`monthly` / `quarterly` / `annually`), default `annually`.
- Receipt number → `RCV-{YEAR}-{6-digit-sequence}`, generated in `Receipt::booted()` `creating`.
- Inspection cadence → `Lease.inspection_cycle_months`, default 6, overridable per lease.

---

## Phase 1 — Owners, Tenants, Leases (operational core)

**Why first:** these power the `Landlord-info`, `Tenant_info`, and `Due date & Inspt` sheets. Smallest blast radius, highest day-to-day value.

### Migrations

1. `add_bank_and_commission_to_owners_table`
   - `bank_name` (string, nullable)
   - `bank_account_name` (string, nullable)
   - `bank_account_number` (string, nullable)
   - `bank_branch` (string, nullable)
   - `commission_percent` (decimal 5,2, nullable, default 10.00)

2. `add_id_document_to_tenants_table`
   - `id_document_type` (string, nullable) — values: `national_id` / `passport` / `driver_license` / `other`
   - `id_document_number` (string, nullable)

3. `extend_leases_for_oversight`
   - `security_deposit_amount` (decimal 15,2, nullable)
   - `security_deposit_status` (string, nullable, default `pending`) — values: `pending` / `paid` / `partial` / `refunded` / `forfeited`
   - `rent_cycle` (string, default `annually`) — values: `monthly` / `quarterly` / `annually`
   - `next_rent_due_at` (date, nullable)
   - `commission_percent_override` (decimal 5,2, nullable) — when present, overrides `owners.commission_percent` for this lease
   - `inspection_cycle_months` (unsigned tinyint, default 6)
   - `last_inspection_at` (date, nullable)
   - `last_inspection_status` (string, nullable)
   - `next_inspection_at` (date, nullable)
   - `notes` (text, nullable)
   - Indexes: `next_rent_due_at`, `next_inspection_at`, `status`

4. `create_inspections_table` — historical inspections per lease
   - `id`, `lease_id` FK, `property_id` FK
   - `inspected_at` (datetime)
   - `inspector_user_id` FK to `users` nullable
   - `status` (string) — values: `pass` / `issues_found` / `fail` / `pending_followup`
   - `findings` (text, nullable)
   - `next_inspection_due_at` (date, nullable)
   - `images` (json, nullable) — Filament `FileUpload(multiple)` like properties
   - timestamps + `softDeletes`
   - Indexes: `lease_id`, `status`

### Models

- `Owner` — add new columns to `$fillable`. Add `commission_rate_for(Lease|Property)` helper that returns lease override or owner rate. Add cached accessors `properties_count`, `tenants_count`.
- `Tenant` — add new columns to `$fillable`.
- `Lease` — add new columns to `$fillable` and casts (`next_rent_due_at`, `next_inspection_at`, `last_inspection_at` as `date`). Add `daysUntilRentDue()`, `daysUntilNextInspection()` accessors. In `booted()`, when a `Payment` with `purpose=rent` is created for this lease, advance `next_rent_due_at` by the cycle.
- `Inspection` — new model. `booted()`: on create, update parent Lease's `last_inspection_at`, `last_inspection_status`, and `next_inspection_at`.

### Filament resources

- `OwnerResource` — add a "Bank Details" section (collapsible) with the 4 bank fields. Add "Commission" field with `%` suffix.
- `TenantResource` — add an "Identification" section with type + number.
- `LeaseResource` — add a "Security Deposit" section (amount + status), a "Schedule" section (rent_cycle, next_rent_due_at, inspection_cycle_months, next_inspection_at, last_inspection_at, last_inspection_status), and a "Notes" textarea.
- `InspectionResource` — new resource under the **Properties** navigation group. Table columns: lease (with tenant + property summary), inspected_at, status (color-coded), inspector, next_inspection_due_at.

### Dashboards

- `Due date & Inspt` sheet ≈ a Filament page/widget — add to the relevant role dashboards:
  - **Upcoming Rent Due** widget — leases where `next_rent_due_at` is within 14 days, sortable by days left.
  - **Inspections Due** widget — leases where `next_inspection_at` is within 30 days.

---

## Phase 2 — Payments (the money flow)

**Why second:** depends on Phase 1's `commission_percent` and `rent_cycle`.

### Migration

`extend_payments_for_oversight`:
- `period_start` (date, nullable)
- `period_end` (date, nullable)
- `period_label` (string, nullable) — free-form display ("Jan 2026", "Q1 2026")
- `purpose` (string, default `rent`) — values: `rent` / `security_deposit` / `agent_fee` / `commission` / `late_fee` / `other`
- `expected_amount` (decimal 15,2, nullable)
- `paid_by_name` (string, nullable)
- `received_by_user_id` foreign key on `users`, nullable
- `commission_amount` (decimal 15,2, nullable)
- `notes` (text, nullable)
- Adjust `status` default values via documentation only (no schema change): `pending` / `complete` / `incomplete` / `failed`.

### Migration — receipts

`add_receipt_number_to_receipts_table`:
- `receipt_number` (string, unique, nullable initially for backfill)
- Backfill existing receipts with `RCV-{year(issued_at)}-{6-digit}` then drop nullable.

### Models

- `Payment`
  - `$fillable` updated
  - Casts: `period_start`, `period_end` as `date`
  - `booted()` `creating`: if `commission_amount` is blank and `purpose === 'rent'`, compute from `amount × Owner.commission_percent` (or lease override).
  - `booted()` `created` (`status === 'complete'` and `purpose === 'rent'`): advance the lease's `next_rent_due_at` by one cycle.
  - On `created`: also create a `Receipt` row if status is `complete` and no receipt exists yet, with generated `receipt_number`.
- `Receipt`
  - `$fillable` includes `receipt_number`
  - `booted()` `creating`: generate `receipt_number` if blank.

### Filament

- `PaymentResource`
  - New "Period" section (start, end, optional label).
  - "Purpose" select.
  - "Outstanding" computed column on the list view (`expected_amount - amount`).
  - "Paid by" + "Received by" select (queryable from `users` where staff/admin/agent).
  - "Commission" field (auto-filled, overridable).
  - "Notes" textarea.
- `ReceiptResource` — show `receipt_number` as the primary identifier; downloadable PDF view (already on backlog).

### Dashboards

- **CMS Earnings** widget — sum of `commission_amount` for completed rent payments in current month / quarter / year. Already aligns with `successfulDeals` style on home page but for revenue.
- **Outstanding Balances** widget — payments where `expected_amount > amount`.

---

## Phase 3 — Client Requests / Leads (sales pipeline)

**Why third:** independent of phases 1–2; can ship before or after them. Most columns; needs decisions on standardization.

### Standardization migrations

Each migration touches one lead table — easier to roll back if a model misbehaves.

1. `standardize_columns_on_land_purchase_leads`
   - Add: `budget_min`, `budget_max` (decimal 15,2 each)
   - Add: `bathrooms` (unsigned tinyint)
   - Add: `property_condition`, `intended_use` (string, nullable)
   - Add: `referred_by_name` (string, nullable)
   - Keep existing `budget` string for legacy data.

2. `standardize_columns_on_land_sale_leads`
   - Same additions as above. `budget_min`/`max` map to "asking price" range.

3. `standardize_columns_on_rental_consultations`
   - Rename `preferred_areas` → `preferred_locations` (matches LandPurchaseLead).
   - Add: `budget_min`, `budget_max`, `bathrooms`, `property_condition`, `intended_use`, `plot_size`, `referred_by_name` (rename `referral_name` → `referred_by_name`, drop `referral_source` if redundant — but check Filament for current usage first).

4. `standardize_columns_on_built_property_listing_leads`
   - Add: `budget_min`, `budget_max`, `bedrooms`, `bathrooms`, `furnished` (boolean), `property_condition`, `intended_use`, `plot_size`, `referred_by_name`.

5. `standardize_columns_on_purchase_build_property_leads`
   - Add: `budget_min`, `budget_max`, `bedrooms`, `bathrooms`, `property_condition`, `intended_use`, `plot_size`, `referred_by_name`.

### Lead activity log

`create_lead_activities_table` — polymorphic timeline.
- `id`, `subject_type` (string), `subject_id` (unsigned big int), composite index
- `user_id` foreign key to `users`, nullable (anonymous public submissions)
- `kind` (string) — values: `note` / `status_change` / `contact_attempt` / `meeting` / `viewing`
- `body` (text, nullable)
- `metadata` (json, nullable) — e.g. for status_change, store from/to
- timestamps

Add `activities()` relationship via `MorphMany` on every lead model + on `Inquiry`. Surface as a Filament RelationManager on each lead resource.

### Filament

- For each of the 5 lead resources, add the new fields to the create/edit form in a "Lead Details" section. Update list views to surface `referred_by_name`, `budget_min/max` summary.
- Add a `LeadActivitiesRelationManager` reusable across all 5 lead resources.

### Public forms

- The matching Blade form for each lead (under `resources/views/public/forms/`) gets the new fields. Bathrooms + property condition + intended use + referred-by are valuable to capture at intake.

### Reporting

- A simple "All Leads" Filament page (read-only) that UNIONs the 5 lead tables for a global view — replicates the flat `Client Request-25` sheet. Not a model; just a query.

---

## Phase 4 — Polish

- **Imports**: importer for `Client Request-25` populated data. Map each row's request type to the appropriate lead model. Free-text Notes column becomes initial `lead_activities` entries.
- **Exports**: CSV exports from each lead model and from `payments` / `leases` that match the Excel column order, so Revolest can compare side-by-side during transition.
- **Documentation**: update `docs/REQUIREMENTS.md` with the new fields. Update `CLAUDE.md` if any conventions change (e.g. commission accessor pattern).

---

## Risk and rollout

- **Backwards compat:** all new columns are nullable; existing seeded data and Filament resources keep working without changes.
- **Data migration:** the only destructive rename is `rental_consultations.preferred_areas` → `preferred_locations`. Migration must `renameColumn` (not drop + add) and update the model. Coordinate with anyone editing the rental consultation form.
- **Receipt numbering backfill:** must run inside a transaction; if the existing receipt count is small this is trivial.
- **Filament resource cache:** after each phase, run `php artisan filament:upgrade` per the deploy script.

---

## Estimation (rough)

| Phase | Migrations | Models touched | Filament resources | Estimate |
|---|---|---|---|---|
| 1 — Owners/Tenants/Leases | 4 | 4 (incl. new Inspection) | 4 | ~1 day |
| 2 — Payments | 2 | 2 | 2 | ~half day |
| 3 — Leads | 5 + 1 polymorphic | 5 + Inquiry | 5 + 1 relation manager | ~1.5 days |
| 4 — Polish | importer + exports | — | reporting page | ~half day |
| **Total** | **12** | **~13** | **12+** | **~3.5 days** |

Each phase is committable + deployable on its own.
