# Excel Operations — Schema Audit

**Source files:** `docs/xls/Client-Request-data .xlsm`, `docs/xls/REVOLEST- Oversight-File -2.xlsm`

**Audit date:** 2026-05-27

**Purpose:** Revolest currently runs day-to-day operations from two Excel workbooks. This doc maps every column in those workbooks to the equivalent field in the Laravel schema, and flags everything that is **not yet captured** so we can mirror Revolest's working setup in the app.

The action plan derived from this audit lives in [excel-operations-plan.md](excel-operations-plan.md).

---

## Workbook summary

### `Client-Request-data .xlsm`
- **`Client Request-25`** — 25 populated rows of real client leads (Jan 2025 → late 2025), 13 columns.
- **`Rqst-lst`** — empty template, 21 columns. Looks like an intended cleaner/expanded schema for the same data.

### `REVOLEST- Oversight-File -2.xlsm`
Four sheets, **all empty** (header rows only). Tracking template for ongoing operations.
- `Landlord-info` (11 cols) — owners
- `Tenant_info` (10 cols) — tenants
- `Due date & Inspt` (12 cols) — leases + inspection cadence
- `Payments_Transactions` (18 cols) — rent and other receipts

---

## Legend

- ✅ Already in schema
- ⚠️ Partially covered / unclear
- ➗ Derivable from existing data (no column needed)
- ❌ **Missing** — needs a new column or table

---

## 1. `Landlord-info` → `owners` table

| Excel column | Status | Notes |
|---|---|---|
| Landlord Name | ✅ `name` | |
| Property Registered date | ⚠️ partial | `created_at` reflects when the Owner row was created, not when their first property was registered. Acceptable for now. |
| Phone | ✅ `phone` | |
| Email | ✅ `email` | |
| Bank details (OWNER) | ❌ missing | Add `bank_name`, `bank_account_name`, `bank_account_number`, optional `bank_branch` / `bank_swift` |
| Nu: Properties Own | ➗ derivable | `$owner->properties()->count()` |
| Nu Tenants | ➗ derivable | Through `properties.leases.tenant` |
| Property description / Location / Annual Rent | ➗ live on `properties` / `listings` | OK |
| **% charge** | ❌ missing | Revolest's commission rate on this owner's properties. Add `commission_percent` (decimal 5,2). Load-bearing for `CMS. EARN` in payments sheet. |

## 2. `Tenant_info` → `tenants` table

| Excel column | Status | Notes |
|---|---|---|
| Tenant Name | ✅ `name` | |
| Phone | ✅ `phone` | |
| Email | ✅ `email` | |
| **I.D / Passport Nu** | ❌ missing | Add `id_document_type` enum (`national_id` / `passport` / `driver_license` / `other`) and `id_document_number` (string). |
| Property Rented / Unit Nu | ➗ derived via `Lease` → `Listing` (unit_name) | OK |
| Property Address | ➗ via property | OK |
| Annual Rent | ➗ `Lease.rent_amount` | OK |
| **S.Deposit Status** | ❌ missing (belongs on Lease, not Tenant) | Add `security_deposit_amount` (decimal 15,2) and `security_deposit_status` enum (`pending` / `paid` / `partial` / `refunded` / `forfeited`) on `leases`. |
| Date Lease starts | ➗ `Lease.start_date` | OK |
| Property Owner | ➗ via `Property.owner` | OK |

## 3. `Due date & Inspt` → `leases` table + a new inspections concept

| Excel column | Status | Notes |
|---|---|---|
| Tenant Name / Property Description / Address | ➗ derived | OK |
| Rent Start Date | ✅ `Lease.start_date` | |
| **Vld.** | ⚠️ ambiguous | Possible meanings: lease validity (covered by `end_date` + `status`), or tenant ID/passport expiry. **Needs clarification from Revolest.** |
| **Rent Due Date** | ❌ missing | No recurring-due-date concept. Add `rent_cycle` enum (`monthly` / `quarterly` / `annually`) and `next_rent_due_at` (date, auto-advanced when a rent Payment is created). |
| Days Left | ➗ derivable | Computed accessor: `$lease->next_rent_due_at?->diffInDays(now())` |
| **Inspection Status** | ❌ missing | New `inspections` table — see plan. Cache `last_inspection_at` / `last_inspection_status` on `leases` for fast list views. |
| **Next Inspection Date** | ❌ missing | Either `next_inspection_at` on `leases` or first row of future `inspections`. |
| **Notes / Action** | ❌ missing | Add `notes` text on `leases` (or use `lease_activities` audit log for history — see plan). |
| Property Owner | ➗ via `Property.owner` | OK |

## 4. `Payments_Transactions` → `payments` table

| Excel column | Status | Notes |
|---|---|---|
| Tenant Name / Unit Occupied Nu / Address | ➗ derived | OK |
| **Duration** | ❌ missing | What period the payment covers. Add `period_start` (date), `period_end` (date), and `period_label` (string, optional). Examples: "Jan 2026", "Q1 2026", "Jan–Dec 2026 (annual)". |
| Amount Paid | ✅ `amount` | |
| **Purpose** | ❌ missing | Add `purpose` enum: `rent` / `security_deposit` / `agent_fee` / `commission` / `late_fee` / `other`. |
| Payment date | ✅ `payment_date` | |
| **Receipt Nu** | ⚠️ partial | `receipts` table exists but no human-readable receipt number. Add `receipt_number` (string, unique, generated like `RCV-2026-00001`) on `receipts`. |
| Payment Method | ✅ `method` | |
| **Outstanding Balance** | ❌ missing | Either add `expected_amount` (decimal) on payment, or derive from `lease.rent_amount` × period − sum(paid). Prefer storing `expected_amount` so historical balances don't drift. |
| **Paid by** | ❌ missing | Actual payer — sometimes employer, parent, guarantor. RentalConsultation has `payer_*` fields; that concept needs to reach Payments. Add `paid_by_name` (string). |
| **Received by** | ❌ missing | Which staff member processed the payment. Add `received_by_user_id` foreign key to `users`. |
| **CMS. EARN** | ❌ missing | Revolest's commission earned on this payment. Add `commission_amount` (decimal 15,2). Auto-calculate from `amount × owner.commission_percent` but allow manual override. |
| Payment Status | ⚠️ exists but enum needs adjustment | Current default is `pending`. Should be `pending` / `complete` / `incomplete` / `failed`. Excel uses "complete/uncomplete". |
| **Remarks** | ❌ missing | Add `notes` text on `payments`. |
| Property Owner | ➗ already `owner_id` | OK |

## 5. `Client Request-25` + `Rqst-lst` → existing lead models

**Decision:** keep the 5 separate lead models (`LandPurchaseLead`, `LandSaleLead`, `RentalConsultation`, `BuiltPropertyListingLead`, `PurchaseBuildPropertyLead`) and add the missing columns to each, rather than collapsing into a single `ClientRequest` model.

The Excel uses one flat table for all request types — that mismatch with the normalized lead models means most unified columns are missing on most lead models. Standardizing the *column names* across all 5 lead models is a goal.

### Columns to add across all 5 lead models

| Excel column | Why it's missing | Recommended column |
|---|---|---|
| **Budget (Min)** + **Budget (Max)** | Existing `budget` is single string; populated sheet shows ranges (`D400,000 / D425,000 / D450,000`) and "Budget constrain" is the #1 status reason in Notes | `budget_min` (decimal 15,2), `budget_max` (decimal 15,2). Keep existing `budget` string for raw input, but require structured min/max where possible. |
| **Bathrooms** | Only RentalConsultation captures bedrooms; bathrooms absent everywhere | `bathrooms` (unsigned tiny int) |
| **Property Condition** | Not captured | `property_condition` enum (`new` / `existing` / `needs_renovation`) |
| **Intended Use** | Not captured | `intended_use` enum (`residential` / `commercial` / `investment` / `mixed`) |
| **Referred by** | Only RentalConsultation has `referral_source` + `referral_name`. Populated sheet shows **every** row has a referrer (CEO, AJ, Penda, Mr. Marenah, Staff, Self). | `referred_by_name` (string) on every lead model + on `inquiries`. Optional `referred_by_user_id` if the referrer is a system user. |

### Columns to standardize (already exist on some, missing on others)

| Column | Where it exists | Where to add |
|---|---|---|
| `plot_size` (string) | LandPurchaseLead, LandSaleLead | RentalConsultation, BuiltPropertyListingLead, PurchaseBuildPropertyLead |
| `preferred_locations` / `preferred_areas` | LandPurchaseLead, RentalConsultation | Standardize to `preferred_locations` everywhere; rename RentalConsultation's `preferred_areas` to `preferred_locations`. |
| `furnished` (boolean) | RentalConsultation | BuiltPropertyListingLead |
| `bedrooms` | RentalConsultation | BuiltPropertyListingLead, PurchaseBuildPropertyLead |

### Notes / follow-up history

The populated `Client Request-25` uses its `Notes` column as a status journal — `Searching`, `Budget constrain`, `Rented`, `Document verification issue`, `found a buyer`. A single text field loses this history when status changes.

**Recommended:** add a polymorphic `lead_activities` table (`subject_type`, `subject_id`, `user_id`, `body`, `created_at`) for an append-only follow-up timeline on every lead. Keeps the audit trail intact.

---

## Resolved decisions

These six product-level questions weren't documented in the Excel sheets directly. Pragmatic defaults were chosen and shipped; each can be re-opened by changing one column or one constant if Revolest pushes back later.

1. **"Vld."** in the `Due date & Inspt` sheet → **interpreted as lease validity.** Covered by the existing `Lease.end_date` and `Lease.status` columns. If it turns out to mean ID/passport expiry, we'd add `Tenant.id_document_expires_at` later (nullable column, no schema risk).
2. **"% charge"** → **per-owner default with per-lease override.** `Owner.commission_percent` is the standard rate; `Lease.commission_percent_override` wins when set. Use `$owner->commissionRateFor($lease)` so the override is honoured automatically.
3. **"CMS. EARN"** → **auto-calculated, manually overridable.** `Payment::booted()` `creating` hook fills `commission_amount` from `amount × owner.commissionRateFor(lease)` whenever the field is blank. Admins can edit the value in the Filament form for one-off waivers or special deals.
4. **Rent cycle** → **per-lease (monthly / quarterly / annually), default annually.** `Lease.rent_cycle` enum drives `next_rent_due_at` advancement; matches the sheet's `Annual Rent` header but doesn't lock everyone in.
5. **Receipt numbering** → **`RCV-{YEAR}-{6-digit-sequence}`**, generated in `Receipt::booted()` `creating`. Sequence is global within a year and counts soft-deleted rows so numbers are never reused.
6. **Inspection cadence** → **`Lease.inspection_cycle_months`, default 6, overridable per lease.** `Inspection::booted()` recomputes `next_inspection_at` from `inspected_at + cycle` on every inspection create/update.

---

## Files that change

The plan in `excel-operations-plan.md` will turn this audit into concrete migrations, model updates, Filament resource edits, and form updates.
