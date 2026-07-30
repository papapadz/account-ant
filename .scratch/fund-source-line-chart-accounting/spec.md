# Feature Specification: Fund Source Monthly Balance & Activity Calculation in `FundSourceLineChart.vue`

## Problem Statement
Previously, `FundSourceLineChart.vue` summed transaction amounts regardless of transaction type (`debit` vs `credit`). The chart should accurately model monthly fund source amount changes: initial allocation upon account creation, plus journal credits (top-ups/refunds), minus journal debits (expenses/disbursements).

## Solution
1. Calculate prior baseline balance for selected fund source account(s) before the target fiscal year (initial amount + prior credits - prior debits).
2. Calculate net monthly activity in the target fiscal year:
   - `+ Initial Amount` (if fund account created in that month)
   - `+ Credit Transactions` (additions / income)
   - `- Debit Transactions` (expenses / disbursements)
3. Compute cumulative monthly balance curve (Jan–Dec) reflecting the actual running fund balance progression over time.

## User Stories
1. As a financial manager, I want the line chart to show running fund source balance changes across months, accounting for initial fund allocations, credits added, and debit expenses subtracted.

## Implementation Decisions
- **Component**: `src/frontend/app/components/dashboard/FundSourceLineChart.vue`.
- **Formula**: `Running Balance(m) = Running Balance(m-1) + Initial Allocation(m) + Credits(m) - Debits(m)`.

## Testing Decisions
- **Testing Seam**: Vue computed property seam (`monthlyData`, `periodTotalAmount`, `peakMonthLabel`).
- **Verification**: Post a debit transaction and credit transaction, and verify line chart points reflect additions vs subtractions accurately.
