# SPEC: Wiring Frontend to Backend & Laravel Sanctum Token-Based Login

## Problem Statement

The AccountAnt application currently runs on the frontend with hardcoded mock data for projects, fund accounts, ledger accounts, account items, and journal transactions. Consequently, the user is unable to perform real operations (creating projects, posting journal entries, allocating funds) that persist in the Laravel SQLite backend database. Additionally, there is no active session management, meaning the application defaults to being logged in as a mock user without validating credentials or verifying tokens against the database.

## Solution

We will fully integrate the Nuxt 4/Ionic frontend with the Laravel backend using Laravel Sanctum for secure token-based authentication. This involves:
1. Enabling and running all Laravel seeders (`AccountingSeeder`, `ProjectSeeder`, `LedgerItemSeeder`) to populate the SQLite database.
2. Restructuring frontend stores (`useAuth`, `useAccounting`, `useProjects`) to fetch from and persist to their respective REST API endpoints.
3. Securing all business ledger, project, and settings endpoints behind the `auth:sanctum` API middleware group on the Laravel backend.
4. Implementing global Nuxt router middleware that checks for the Sanctum plain text token cookie, fetches user details from the backend `/api/auth/user` endpoint, and handles redirects to the login screen for unauthenticated users.
5. Harmonizing form submissions and database relationships to ensure seamless writes (e.g. creating general or project-specific ledger account items).

## User Stories

1. As a corporate finance officer, I want to sign in with my credentials, so that I can securely receive an API token and access the financial ledger system.
2. As a manager, I want the system to redirect me to the login page if I am not authenticated or if my token is invalid, so that unauthorized users cannot access financial details.
3. As a finance director, I want to see the dashboard initialized with real project data and statistics from the database, so that I have an accurate overview of current budgets.
4. As an accountant, I want to add new projects with their street, city, and zip code Site details, so that site specifications are saved on the backend.
5. As a project manager, I want to allocate new or existing fund accounts to a project, so that the project has a valid funding source.
6. As a senior accountant, I want to post a ledger transaction inside a project, so that it is recorded in the double-entry journal under the project's specific fund account.
7. As an internal auditor, I want general ledger transactions (such as payroll or SaaS revenue) and project transactions to be unified in the company's ledger report, so that I can inspect the company's total financial health.
8. As an administrator, I want to update company details and personal profile fields, so that the company information remains up to date.

## Implementation Decisions

- **Laravel Sanctum Auth**: We will enable the `HasApiTokens` trait on the `User` model and secure business routes using the `auth:sanctum` middleware in `routes/api.php`. The `/api/auth/login` endpoint will return a real token issued via `$user->createToken(...)` upon password verification.
- **Unified Journal Table**: We will use the single `/journal-entries` API endpoint for both project-specific transactions and general ledger postings. When a project-specific transaction is saved, it will contain a non-null `project_id`. When retrieved, both the project page and the general journal will fetch their respective lists from the database, ensuring synchronous updates.
- **Dynamic Fund Allocation**: When adding a fund source to a project in the frontend, if the user types a fund name that does not exist in the general fund accounts, the system will dynamically create the fund account first via `POST /fund-accounts`, and then link it to the project using `POST /projects/{id}/funds`.
- **Global Auth Middleware**: We will create a Nuxt-native route middleware `auth.global.ts` to manage auth checks, session verification, and redirection.
- **Address Mapping**: Since the frontend uses a nested `address` object and the backend projects table stores columns flatly (`street`, `barangay`, `zip`, `city_id`), we will map the nested object before sending it to the backend and reconstruct it upon retrieval.

## Testing Decisions

- **Route Access Protection**: Write automated integration tests verifying that requests to `/api/projects` without a valid Sanctum token are rejected with a 401 Unauthorized status.
- **Behavioral Testing**: We will verify that data successfully posts and reads from the database.
- **Unit/Integration Tests**: Run the Laravel phpunit test suite to verify controller validation rules.
- **Manual End-to-End Test**: Log in with seeded admin credentials, execute key user actions (create project, allocate funds, post debit/credit journal entries), and verify database persistence using SQLite command line or UI checks.

## Out of Scope

- Implementing third-party OAuth provider login.
- Real-time WebSockets synchronization between multiple concurrent active sessions.
- Multi-currency conversions (all calculations will assume a uniform base currency).

## Further Notes

- In case the `gh` command line tool becomes available later, this PRD can be published as a GitHub issue. For now, it is saved locally as documentation.
