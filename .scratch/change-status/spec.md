# Feature Specification: Change Status for Ledger Accounts, Account Items, and Project Journal Entries

## Problem Statement

Currently, the three main management and project pages have no way to activate, deactivate, archive, or otherwise change the lifecycle status of their entities:

- **Ledger Accounts** (`management/accounts.vue`): Cards always show a hardcoded "Active" badge with no ability to change it.
- **Account Items** (`management/items.vue`): Items have no status indicator or lifecycle management.
- **Project Journal Entries / Project** (`project/[id].vue`): Individual journal entries have no status, and the project as a whole has no PATCH endpoint for status changes.

Without entity status management, accountants cannot: retire obsolete ledger accounts, deactivate superseded catalog items, or void/reconcile individual journal entries — creating data integrity and governance risks.

## Solution

Add a persisted `status` column (`enum`) to the `ledger_accounts`, `account_items`, and optionally `ledger_account_items` (journal entries) database tables. Expose a PATCH status update endpoint for each entity. On the frontend, surface a status badge with an inline dropdown/context menu on each card/row, allowing the authorized user to transition the entity's status. Filter active-only records by default across the UI.

### Status Values Per Entity

| Entity | Statuses |
|---|---|
| Ledger Account | `active`, `inactive`, `archived` |
| Account Item | `active`, `inactive`, `archived` |
| Journal Entry | `posted`, `void`, `reconciled` |
| Project | `active`, `on-hold`, `completed` (already displayed via `UiBadge`, needs PATCH endpoint + UI picker) |

## User Stories

1. As an accountant, I want to see the current status of each ledger account on its card, so that I can quickly identify active vs inactive accounts.
2. As an accountant, I want to change the status of a ledger account (Active → Inactive → Archived), so that I can retire obsolete accounts without deleting them.
3. As an accountant, I want the management accounts grid to show only "active" accounts by default, with a toggle to show all statuses, so that inactive/archived accounts don't clutter the view.
4. As an accountant, I want to see the current status of each account catalog item, so that I can identify which items are available for use in journal entries.
5. As an accountant, I want to change the status of an account catalog item (Active → Inactive → Archived), so that I can retire superseded items.
6. As an accountant, I want the items grid to show only "active" items by default, with a filter to show archived/inactive items, so that the active catalog remains clean.
7. As an accountant, I want to void a posted journal entry on the project ledger, so that I can correct posting errors without deleting audit history.
8. As an accountant, I want to mark a journal entry as "reconciled" after it has been verified, so that I can track audit completion per transaction.
9. As an accountant, I want each journal entry row in the project ledger table to show its current status as a colored badge, so that I can visually identify posted, void, and reconciled entries.
10. As an accountant, I want to change the status of the project itself (Active / On Hold / Completed), so that the project lifecycle is accurately tracked.
11. As an accountant, I want the status change UI to provide visual confirmation (color-coded badge flash/toast) after a successful status update, so that I know the change was saved.
12. As an accountant, I want status transitions to be validated (e.g., cannot move a reconciled entry back to posted), so that data integrity is enforced.

## Implementation Decisions

- **Backend Models**:
  - `LedgerAccount`: Add `status` enum column (`active`, `inactive`, `archived`), default `active`.
  - `AccountItem`: Add `status` enum column (`active`, `inactive`, `archived`), default `active`.
  - `LedgerAccountItem` (journal entries): Add `status` enum column (`posted`, `void`, `reconciled`), default `posted`.
  - `Project`: No DB change needed — `status` column already exists. Add a dedicated PATCH route and controller method.

- **Backend Migrations**:
  - `2026_07_30_000003_add_status_to_ledger_accounts_table.php`
  - `2026_07_30_000004_add_status_to_account_items_table.php`
  - `2026_07_30_000005_add_status_to_ledger_account_items_table.php`

  > SQLite uses `string` columns for enums (via `->string('status')->default('active')`).

- **Backend Controllers**:
  - `LedgerAccountController`: Add `updateStatus(Request $request, $id)` → PATCH `/ledger-accounts/{id}/status`.
  - `AccountItemController`: Add `updateStatus(Request $request, $id)` → PATCH `/account-items/{id}/status`.
  - `LedgerAccountItemController`: Add `updateStatus(Request $request, $id)` → PATCH `/journal-entries/{id}/status`.
  - `ProjectController`: Add `updateStatus(Request $request, $id)` → PATCH `/projects/{id}/status`.

- **Backend Routes**: `routes/api.php` gets four new `Route::patch()` entries.

- **Frontend Composables**:
  - `useAccounting.ts`: Add `status` field to `LedgerAccount` and `AccountItem` interfaces; add `updateLedgerAccountStatus()` and `updateAccountItemStatus()` methods.
  - `useProjects.ts`: Add `updateProjectStatus()` method and `updateJournalEntryStatus()` method.

- **Frontend UI — Status Badge Component**:
  - Reuse/extend existing `UiBadge` to accept ledger/item status colors, or create a shared `StatusPill.vue` component with color map: `active → emerald`, `inactive → amber`, `archived → slate/red`, `posted → blue`, `void → rose`, `reconciled → emerald`.
  - Each badge is an inline `<button>` that opens a minimal dropdown with available transitions.

- **Frontend Pages**:
  - `management/accounts.vue`: Add status pill on each card footer; replace hardcoded "Active" text; add status filter chips (All / Active / Inactive / Archived) above the grid.
  - `management/items.vue`: Add status pill on each card footer; add status filter chips.
  - `project/[id].vue`: Add status badge on each journal entry row in the DataTable; add a "Change Project Status" button in the header action row; the project status badge (`UiBadge`) becomes a clickable dropdown.

- **Design Aesthetics** (ui-ux-pro-max fintech SaaS guidelines):
  - Status dropdowns: glassmorphic floating panel, `backdrop-blur-sm`, `rounded-xl`, `shadow-xl`.
  - Transitions: color-change `transition-colors duration-200`, no layout shift.
  - Status pills: pill-shaped (`rounded-full`), subtle bg + border + text tint, `text-[10px] font-bold uppercase tracking-wider`.
  - Hover on clickable badge: slight scale (`scale-105`) or border brightening.
  - Success toast: 2-second fade-out notification confirming status change.

## Testing Decisions

- **What makes a good test**: Test that the correct status value is persisted to the database after calling the PATCH endpoint; test that filtering returns only the correct subset.
- **Modules to test**:
  - Backend: `LedgerAccountController::updateStatus`, `AccountItemController::updateStatus`, `LedgerAccountItemController::updateStatus`, `ProjectController::updateStatus`.
  - Frontend: Status filter computed properties in `management/accounts.vue` and `management/items.vue`.
- **Verification**: Manually change a ledger account status via the UI dropdown and confirm the badge updates; check the network tab for the PATCH request; confirm re-fetch returns updated status.

## Out of Scope

- Role-based access control on status transitions (e.g., only admins can archive).
- Status history / audit trail log.
- Automated status transitions (e.g., auto-close project when end_date passes).
- Bulk status changes.

## Further Notes

- The `projects` table already has a `status` column in the migration (check `Project` model `$fillable`). The `project/[id].vue` page already renders `<UiBadge :status="project.status" />`. We need to add a PATCH endpoint and a dropdown trigger on the badge.
- SQLite does not support native enum columns. Use `->string('status', 20)->default('active')` with controller-side validation via `in:active,inactive,archived`.
