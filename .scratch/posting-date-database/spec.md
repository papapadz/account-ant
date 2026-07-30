# Feature Specification: `posting_date` Field in `LedgerAccountItem` Model & Database Persistence

## Problem Statement
Currently, `LedgerAccountItem` records in the backend database track `created_at` timestamps, but lack a dedicated `posting_date` column. When users post a journal entry on `project/[id].vue` with a specific posting date, that date must be sent to the API and saved into the database on the `ledger_account_items` table.

## Solution
1. Add a `posting_date` column (`nullable()`, `date` type) to `ledger_account_items` table via migration.
2. Add `'posting_date'` to `$fillable` and `$casts` in `App\Models\Accounting\LedgerAccountItem`.
3. Update `LedgerAccountItemController` to validate `posting_date` (or `date`) in requests and persist it when creating entries.
4. Update frontend `useAccounting` / `useProjects` composables and `project/[id].vue` to send `posting_date` in API request payloads and store it in database.

## User Stories
1. As an accountant, I want to specify the exact posting date when recording a transaction on the project details page, so that it is persisted accurately in the database.
2. As a system, I want `posting_date` stored as a date column on `ledger_account_items`, so that reports can filter by actual accounting dates.

## Implementation Decisions
- **Backend Model**: `App\Models\Accounting\LedgerAccountItem`.
- **Database Table**: `ledger_account_items` (`posting_date` DATE column).
- **Migration**: `2026_07_30_000001_add_posting_date_to_ledger_account_items_table.php`.
- **Backend Controller**: `App\Http\Controllers\Api\LedgerAccountItemController`.
- **Frontend Composables**: `useAccounting.ts`, `useProjects.ts`.
- **Frontend UI**: `src/frontend/app/pages/project/[id].vue`.

## Testing Decisions
- **Testing Seam**: Backend API integration test (`POST /api/journal-entries`) and frontend posting date form payload test.
- **Scenarios**: Verify `posting_date` is validated, saved to database, returned in API JSON responses, and displayed on `project/[id].vue`.
