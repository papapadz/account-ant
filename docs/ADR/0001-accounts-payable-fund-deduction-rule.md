# ADR-0001: Accounts Payable Fund vs Project Balance Rules

## Status
Accepted

## Context
When posting a journal entry in a project, entries categorized as **Accounts Payable** represent liabilities/commitments incurred for project costs before actual cash disbursement occurs. 

Previously, all debit entries immediately deducted cash from both the **Project Balance** and the backing **FundAccount**. However, unpaid Accounts Payable entries should reflect as project expenses (deducted from remaining project budget), but should **not** deduct cash from the backing `FundAccount` balance until cash is actually paid out (`is_paid` becomes `true`).

## Decision
1. **`is_paid` Column**: Add a boolean column `is_paid` (default `true`) to `ledger_account_items`.
2. **Category-Based Auto-Tagging**: If a journal entry is posted with a Ledger Account or Account Item categorized as "Accounts Payable" (or contains "Accounts Payable" / "AP"), `is_paid` is set to `false` upon creation.
3. **FundAccount Balance Calculation**:
   - `getFundSourceSpent()` and `getFundAccountSpent()` filter out debit entries where `is_paid === false`. Unpaid AP entries do **not** decrease cash balance in `FundAccount`.
4. **Project Balance Calculation**:
   - `getProjectTotalSpent()` includes **all** debit transactions (both `is_paid === true` and `is_paid === false`), ensuring project budget utilization and remaining balance accurately reflect accrued Accounts Payable expenses.
5. **Settling Accounts Payable**:
   - Updating `is_paid` to `true` on an Accounts Payable entry instantly transitions it to paid status, whereupon it begins deducting from the backing `FundAccount` remaining cash balance.

## Consequences
- Financial metrics accurately distinguish accrued liabilities (AP) from cash disbursements.
- `FundAccount` cash balance reflects liquid cash availability.
- `Project` remaining budget reflects overall financial exposure including unpaid commitments.
