# Feature Specification: Use `posting_date` as Primary Date in `useProjects.ts` & `project/[id].vue`

## Problem Statement
The accounting system now records a dedicated `posting_date` field in backend transactions. `useProjects.ts` and `project/[id].vue` need to consistently map, filter, sort, and render transactions using `posting_date` (falling back to `date` or `created_at` if missing).

## Solution
1. Update `Transaction` interface in `useProjects.ts` to include `posting_date?: string`.
2. Update transaction mapping in `useProjects.ts` (`fetchProjects()`, `addTransaction()`, `getProjectMonthlyExpenses()`) to use `posting_date`.
3. Update `project/[id].vue` transaction table column key to `'posting_date'` (or support both `'posting_date'` & `'date'`), set default sort key to `'posting_date'`, enable search across `posting_date`, and render `dateStore.formatISODate(item.posting_date || item.date)`.

## User Stories
1. As an accountant, I want transactions in the project details table to be sorted, filtered, and displayed by their explicit posting date (`posting_date`), so that financial reports match ledger posting records.
2. As a user, I want searching by date on the transactions table to match the transaction's posting date.

## Implementation Decisions
- **Composable**: `src/frontend/app/composables/useProjects.ts`.
- **Page Component**: `src/frontend/app/pages/project/[id].vue`.
- **Primary Field**: `posting_date` (ISO `YYYY-MM-DD` string, fallback to `date`).

## Testing Decisions
- **Testing Seam**: Vue component data table seam on `/project/:id`.
- **Verification**: Inspect table headers, default sorting, search filters, and monthly expense calculations to confirm `posting_date` usage.
