# Spec: Database Seeder and Backend-to-Frontend Data Wiring

## Problem Statement

Currently, the AccountAnt web application has a rich set of Laravel migrations and backend controllers/models for financial ledger management (fund accounts, ledger accounts, account items, projects, project fund allocations, and journal entries). However:
1. The backend lacks a complete database seeder to populate realistic seed data across all database tables (Address hierarchy, HR/People, Accounting accounts, Projects, and Ledger items).
2. The frontend composables (`useAccounting.ts`, `useProjects.ts`) rely primarily on fallback in-memory `useState` arrays rather than fetching live data from the Laravel REST API endpoints.
3. The frontend needs seamless integration so that all CRUD operations, project metrics, and ledger summaries reflect persistent backend database state.

## Solution

1. Create modular Laravel database seeders (`AddressSeeder`, `HrSeeder`, `AccountingSeeder`, `ProjectSeeder`, `LedgerItemSeeder`) and update `DatabaseSeeder.php` to populate realistic test data.
2. Add a `/api/cities` API endpoint in Laravel so the frontend can retrieve live location data for project address creation.
3. Update frontend composables (`useAccounting.ts`, `useProjects.ts`) and pages to fetch and persist data using `useApi()`.
4. Ensure fallbacks remain resilient if the backend API is starting or restarting.

## User Stories

1. As a developer, I want to run `php artisan db:seed` to immediately populate the application database with comprehensive financial, HR, geographic, and project ledger test data.
2. As a finance manager, I want the AccountAnt dashboard to display real-time total debits, total credits, net ledger balance, and project running balances fetched from the backend API.
3. As a project administrator, I want to create projects and assign fund allocations, persisting the data directly to the database via API requests.
4. As an accountant, I want to post new journal entries tagged to specific projects and fund sources, updating real-time budget utilization metrics on the frontend.
5. As a system user, I want settings and authentication state to correctly synchronize with the backend profile and company endpoints.

## Implementation Decisions

- **Seeder Modularization**: Break seeding into focused seeders (`AddressSeeder`, `HrSeeder`, `AccountingSeeder`, `ProjectSeeder`, `LedgerItemSeeder`) called in logical sequence from `DatabaseSeeder.php`.
- **API Wiring in Composables**: Enhance `useAccounting.ts` and `useProjects.ts` to call backend API endpoints (`/api/fund-accounts`, `/api/ledger-accounts`, `/api/account-items`, `/api/projects`, `/api/journal-entries`, `/api/cities`).
- **Reactive State with API Synchronization**: Keep Nuxt reactive state (`useState`) in sync with API fetch results while providing clean async fetch handlers (`fetchFundAccounts`, `fetchLedgerAccounts`, `fetchAccountItems`, `fetchProjects`, `fetchJournalEntries`).
- **City API Endpoint**: Add `CityController` in Laravel backend returning active cities with parent state/country relationships.

## Testing Decisions

- Run Laravel database seeders via Artisan: `C:\laragon\bin\php\php-8.2.22-Win32-vs16-x64\php.exe artisan migrate:fresh --seed`
- Verify API response outputs using Artisan route list / HTTP test queries.
- Verify frontend data loading and reactivity across Fund Accounts, Ledger Accounts, Account Items, Journal Entries, and Project Management pages.

## Out of Scope

- Modifying existing core database table schemas (migrations are locked and active).
- Implementing multi-tenant database partitioning.

## Further Notes

- All changes adhere to the Ponytail principle (YAGNI, minimal clean code without unnecessary abstractions).
