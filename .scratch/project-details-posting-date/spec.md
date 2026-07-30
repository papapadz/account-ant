# Feature Specification: Standardize Posting Date in Project Details Page (`project/[id].vue`)

## Problem Statement
In `project/[id].vue`, dates across transaction tables, modal forms, and header statements are displayed raw or inconsistently. The page needs to strictly use the posting date ISO value (`YYYY-MM-DD`) using `useDate().formatISODate(...)` for date rendering in data tables, form inputs, and detail modals.

## Solution
Integrate `const dateStore = useDate()` in `src/frontend/app/pages/project/[id].vue` and ensure:
1. Transaction table date cell `#cell-date` renders `dateStore.formatISODate(item.date)` (posting date).
2. Posting date input in `Post Journal Entry` modal uses `dateStore.formatISODate(...)`.
3. Fund Source table date received cell renders `dateStore.formatISODate(item.date_received)`.
4. Transaction detail modal displays posting date formatted consistently.

## User Stories
1. As an accountant, I want to see the exact posting date (`YYYY-MM-DD`) for all journal entries on the project details page, so that financial records align with accounting ledger dates.
2. As a user, I want form fields for date input to default to today's posting date in `YYYY-MM-DD` format using `dateStore.formatISODate()`.

## Implementation Decisions
- **Module**: `src/frontend/app/pages/project/[id].vue`.
- **Composable**: `const dateStore = useDate()`.
- **Date Formatting**: Use `dateStore.formatISODate(val)` for posting dates.

## Testing Decisions
- **Testing Seam**: Vue component rendering seam on `/project/:id`.
- **Verification**: Inspect transaction list `#cell-date` and modal forms to verify posting date strings.
