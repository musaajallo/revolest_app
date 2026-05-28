# Questions for Revolest — schema sign-off

These came out of mapping the two Excel workbooks (`docs/xls/`) onto the app.
Each one was a product decision the spreadsheets didn't spell out. We shipped a
**sensible default** so the build could proceed, but **each default is an
assumption that needs Revolest to confirm or correct.** Every one is reversible
with a one-column or one-constant change.

Please mark each row **Confirmed** or give the correct answer.

| # | Question | What we assumed (shipped default) | Revolest's answer |
|---|----------|-----------------------------------|-------------------|
| 1 | **"Vld."** column on the *Due date & Inspt* sheet — what does it mean? | Lease validity (covered by lease end date + status). If it's actually the tenant's ID/passport expiry, we add one date field. | ☐ |
| 2 | **"% charge"** (commission) — one fixed rate per landlord, or can it differ per property? | Default rate per owner, with an optional per-lease override. | ☐ |
| 3 | **"CMS. EARN"** on the payments sheet — typed in by hand, or calculated? | Auto-calculated as `amount × owner's %`, but an admin can override the number on any payment. | ☐ |
| 4 | **Rent cycle** — is rent always annual, or do some tenants pay monthly / quarterly? | Per-lease setting; default annual; monthly & quarterly available. | ☐ |
| 5 | **Receipt number format** — does Revolest already use a receipt-number scheme (e.g. printed books)? | Auto-generated as `RCV-{year}-{6 digits}`, e.g. `RCV-2026-000001`. | ☐ |
| 6 | **Inspection cadence** — how often are properties inspected? | Per-lease setting; default every 6 months. | ☐ |

## Why these matter

- **#2 + #3** drive the **commission Revolest earns** on every rent payment. If
  the rate model or calculation is wrong, the "CMS Earnings" dashboard figures
  will be wrong.
- **#4** drives the **"Rent Due"** reminders. If a monthly tenant is recorded as
  annual, their due-date reminders won't fire on time.
- **#5** affects what tenants see on receipts — worth matching any existing
  printed format.
- **#1 + #6** are lower-risk; current defaults are safe placeholders.

## If an answer changes a default

| Answer changes | What we'd touch |
|----------------|-----------------|
| #1 is ID expiry, not lease validity | add `tenants.id_document_expires_at` (one nullable date) |
| #2 needs per-property rates | add `properties.commission_percent` to the override chain |
| #3 should be manual only | drop the auto-calc line in `Payment::booted()` |
| #4 default should be monthly | change one default on the `leases.rent_cycle` column |
| #5 uses an existing format | change `Receipt::generateNumber()` |
| #6 default differs | change the default on `leases.inspection_cycle_months` |

Full technical detail and the column-by-column mapping live in
[excel-operations-audit.md](excel-operations-audit.md).
