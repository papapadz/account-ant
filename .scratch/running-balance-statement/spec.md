# Feature Specification: Correct Running Balance Computation & Display in `project/[id].vue`

## Problem Statement
In `src/frontend/app/pages/project/[id].vue`, the chronological statement table (lines 828–847) displays running balances for project financial transactions. To ensure accurate financial statement rendering:
1. Transaction records must use their `posting_date` (`t.posting_date || t.date`) as the primary sorting date `rawDate`.
2. Items must be sorted chronologically by `rawDate` ISO value before computing running balances.
3. The running balance must correctly accumulate: `Running Balance = Running Balance (prev) + Credit Amount - Debit Amount`.
4. The date column in the table should render using `dateStore.formatISODate(item.rawDate)`.

## Solution
1. In `chronologicalStatement` computed property:
   - For fund allocations, set `rawDate = f.date_received || project.value?.start_date || new Date().toISOString().split('T')[0]`.
   - For journal entries, set `rawDate = t.posting_date || t.date || new Date().toISOString().split('T')[0]`.
   - Sort `items` by `new Date(a.rawDate).getTime() - new Date(b.rawDate).getTime()`.
   - Accumulate `balance += item.credit_amount - item.debit_amount`.
2. In the statement table template:
   - Render `dateStore.formatISODate(item.rawDate)` for date display.
   - Format `running_balance` via `currencyStore.formatCurrency(item.running_balance)`.

## User Stories
1. As an accountant viewing the Printable Project Statement, I want to see transactions sorted by their posting date with a running balance, so that each row shows the cumulative available balance.

## Implementation Decisions
- **Page Component**: `src/frontend/app/pages/project/[id].vue`.
- **Date Key**: `rawDate` (ISO `YYYY-MM-DD`).

## Testing Decisions
- **Testing Seam**: Vue computed property seam (`chronologicalStatement`).
- **Verification**: Inspect statement table rows, date formatting, and running balance progression across debits and credits.
