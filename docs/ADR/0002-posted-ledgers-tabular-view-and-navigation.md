# ADR-0002: Posted Ledgers Tabular View & Multi-Level Filtering

## Status
Accepted

## Context
As the application scales, financial users and accountants need a centralized, company-wide ledger view to inspect, filter, and manage all posted double-entry journal transactions (`LedgerAccountItem`) across all projects, funds, and chart-of-accounts items.

Previously, journal transactions were only viewable within individual project detail pages or the general management journal route. A dedicated, top-level **Posted Ledgers** page in the main navigation sidebar directly below **Projects** was required.

## Decision
1. **Sidebar Navigation**:
   - Add **Posted Ledgers** (`/ledgers`) to `mainLinks` in `layouts/default.vue`, positioned immediately below **Projects** (`/projects`).
2. **Dedicated `/ledgers` Page**:
   - Located at `src/frontend/app/pages/ledgers.vue`.
   - Real-time financial aggregate metrics bar: Total Debits (Dr), Total Credits (Cr), Net Posted Balance, and Total Entries count.
3. **Multi-Level Filtering System**:
   - **Transaction Type Tabs**: All, Debits (Dr), Credits (Cr).
   - **Status Filter**: Default set to `posted`, with options for `all`, `reconciled`, and `void`.
   - **Project Dropdown Filter**: Filter by All Projects or a specific project.
   - **Ledger Account Dropdown Filter**: Filter by All Accounts or a specific ledger account code.
   - **Multi-Field Text Search**: Instant search across transaction memo/description, catalog item name, fund code, account code, and project title.
4. **Interactive Entry Detail & Action Modal**:
   - Clicking any table row opens an Entry Detail Modal displaying full transaction attributes, sub-items breakdown, timestamps, and author.
   - Interactive payment status toggle (`is_paid`: Paid / Unpaid) updating backend via `/journal-entries/{id}/is-paid`.
   - Interactive posting status selector (`posted`, `reconciled`, `void`) updating backend via `/journal-entries/{id}/status`.

## Consequences
- Single source of truth for company-wide ledger inspection and auditing.
- Enhanced filtering UX allows accountants to slice transactions by project, ledger account, payment status, and posting state without leaving the page.
- Direct synchronization with financial composables (`useAccounting`, `useProjects`, `useCurrency`).
