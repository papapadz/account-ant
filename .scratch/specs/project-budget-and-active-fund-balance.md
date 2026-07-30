# Spec — Project Budget Card & Realtime Active Fund Accounts Balance Calculation

## Problem Statement

In the project details overview page (`src/frontend/app/pages/project/[id].vue`), the primary KPI card currently labeled "Net Ledger Balance" should be updated to "Project Budget" displaying the allocated project budget. Additionally, the "Active Fund Accounts Balance" KPI card must compute and display real-time fund liquidity calculated via `getFundSourceRemaining(fund.id)` for all allocated fund sources.

## Solution

1. Rename the first KPI card in `[id].vue` from **"Net Ledger Balance"** to **"Project Budget"**, updating its display value to `project.value?.budget` (or total allocated project funds as fallback).
2. Update `getProjectActiveFundBalance(projectId)` in `useProjects.ts` and `activeFundAccountsBalance` in `[id].vue` to compute the real-time sum of `getFundSourceRemaining(f.id)` for all active project fund sources.
3. Ensure that when new journal debits or credits are posted, both the line item table and the KPI cards reactively recalculate real-time remaining balances using `getFundSourceRemaining`.

## User Stories

1. As a project manager, I want the first financial summary card to display the **Project Budget**, so that I can immediately track target allocations.
2. As a finance officer, I want the **Active Fund Accounts Balance** card to reflect the exact real-time remaining liquidity calculated by `getFundSourceRemaining`, so that I know how much capital remains available across all allocated funds.
3. As an auditor, I want transaction postings (debits and credits) to dynamically update fund source balances in real time across the UI.

## Implementation Decisions

- **KPI Card Title Update**: Change `Net Ledger Balance` to `Project Budget` in `[id].vue`.
- **Project Budget Computed**: Define `projectBudget = computed(() => Number(project.value?.budget) || totalProjectFunds.value)`.
- **Real-Time Active Fund Balance**: Update `getProjectActiveFundBalance` in `useProjects.ts` to `fundSources.value.filter(f => f.project_id === projectId).reduce((sum, f) => sum + getFundSourceRemaining(f.id), 0)`.
- **Reactive Re-calculation**: Ensure `activeFundAccountsBalance` in `[id].vue` uses `getFundSourceRemaining(f.id)` reactively.

## Testing Decisions

- Build verification via `npm run build` in `src/frontend`.
- Calculation checks verifying `Initial Fund Amount + Credits - Debits = Remaining Balance`.

## Out of Scope

- Altering database migration schemas for Laravel backend models.

---
