# ADR 0020: Removal of Initial Balance from Printed Ledger and Budget Allocation on Fund Sources

- **Status:** Proposed
- **Date:** 2026-07-31
- **Context:** `AccountAnt` Accounting System - Project General Ledger Printing & Fund Source Management

## Context & Problem Statement

Previously (per ADR 0017), the printed General Ledger Statement automatically prepended an artificial line item titled "Initial Project Budget Allocation" into the statement, and adding a fund source to a project recorded a budget allocation (`initial_amount`).

However, for clean ledger accounting, accuracy, and auditability:
1. The printed ledger statement must reflect actual recorded journal transactions (inflows & outflows) without inserting synthetic opening balance lines.
2. Adding/linking a fund source account to a project should establish the relationship and authorization of that fund account for the project without posting a budget allocation.

## Proposed Decisions

1. **Printable Ledger Statement (`project/[id].vue`):**
   - Remove the `Initial Project Budget Allocation` row from `chronologicalStatement`.
   - Compute running balance calculations directly from the net movement of actual journal transactions.
   - Adjust Total Inflow & Net Position in the printed statement footer to summarize transaction credits and debits.

2. **Project Fund Sources & Budget Allocation (`ProjectController.php`, `useProjects.ts`, `useAccounting.ts`):**
   - When adding or linking a fund source to a project via `addFundSource` / `/projects/{id}/funds`, do not post a budget allocation (`initial_amount` set/defaulted to 0).
   - `getFundSourceRemaining` and `getFundSourceUsagePercentage` evaluate backing `FundAccount` capacity from `useAccounting` while tracking project expenditures against actual journal transactions.

## Consequences

- **Ledger Auditability:** Printed statements match physical journal entries line-for-line without synthetic opening balances.
- **Decoupled Fund Linking:** Linking a fund account to a project is purely an association step, avoiding unintended budget allocation postings.
