# Feature Specification: `date_received` in `ProjectFund` Model, Migration, Controller, and Frontend

## Problem Statement
`ProjectFund` records (project fund source allocations) currently track `initial_amount` and timestamps, but lack a dedicated `date_received` database column. When adding a fund source to a project in `project/[id].vue`, the `date_received` input must be saved into the `date_received` column in the database and used for project ledger calculations and statement sorting.

## Solution
1. **Database & Migration**: Create migration `2026_07_30_000002_add_date_received_to_project_funds_table.php` adding `$table->date('date_received')->nullable()->after('initial_amount')`.
2. **Eloquent Model**: Add `'date_received'` to `$fillable` array and `'date_received' => 'date:Y-m-d'` to `$casts` in `App\Models\Accounting\ProjectFund`.
3. **Backend Controller**: Update `ProjectController::addFund` to validate and persist `date_received` to `project_funds`.
4. **Frontend Composable**: Update `FundSource` interface and `fetchProjects()` / `addFundSource()` in `useProjects.ts` to send and receive `date_received`.
5. **Frontend UI**: Update `handleAddFundSource` in `project/[id].vue` to send `date_received` and use `date_received` in project ledger statement sorting and fund table cells.

## User Stories
1. As an accountant, when allocating a fund source to a project with a specific date received, I want that date saved in the database so that opening capital allocation dates are accurately recorded on ledger statements.

## Implementation Decisions
- **Backend Model**: `App\Models\Accounting\ProjectFund`.
- **Database Column**: `project_funds.date_received` (`DATE`, `nullable`).
- **Migration**: `2026_07_30_000002_add_date_received_to_project_funds_table.php`.
- **Frontend Composable**: `src/frontend/app/composables/useProjects.ts`.
- **Frontend UI**: `src/frontend/app/pages/project/[id].vue`.

## Testing Decisions
- **Testing Seam**: End-to-end API & Vue state seam.
- **Verification**: Allocate a fund source with a custom date received, inspect the database record and project ledger statement to confirm `date_received` persistence.
