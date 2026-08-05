# Design: Fund Account Reactivity — KPI Cards Update on Fund Account Creation

**Date:** 2026-08-04  
**Status:** Implemented

## Problem

When a new Fund Account was created on `/management/funds`, the four KPI cards on the dashboard (`/`) did not update reactively. Specifically, "Total Managed Funds" and "Remaining Balance" remained stale until a page refresh.

## Root Cause

`index.vue` sourced `totalAppManagedFunds` from `useProjects().totalAppManagedFunds`, which sums `fundSources` — the **project fund allocation** join table (`project_funds`). A newly created Fund Account is a catalog entry; it isn't in that table until it's explicitly allocated to a project. So adding a Fund Account never touched `fundSources`, and the KPI never updated.

## Solution

Re-point `totalAppManagedFunds` in `index.vue` to sum `useAccounting().fundAccounts` — the global Fund Account catalog. `addFundAccount` already does an optimistic push into `accounting.fundAccounts.value` after the API call succeeds, so the reactive chain is:

```
addFundAccount() → fundAccounts.value.push(created)
                 → totalAppManagedFunds computed re-runs
                 → KPI card re-renders ✅
```

`totalAppRemainingBalance` is updated in the same file to derive from the new `totalAppManagedFunds` instead of `projectsStore.totalAppRemainingBalance`.

## What Was NOT Changed

- **Navbar `netBalance`** (`Debits − Credits` from journal entries) — correct behaviour: it only updates when journal entries are posted, not on fund account creation.
- `useProjects.totalAppManagedFunds` — kept intact for any other consumers that may still need the project-scoped view.
- `addFundAccount` in `useAccounting` — already correct (optimistic push).

## Data Semantics Clarified

| Metric | Source | Updates When |
|---|---|---|
| Total Managed Funds | `accounting.fundAccounts` (global catalog) | Fund Account created |
| Total Spent | `projectsStore.transactions` (project debits) | Journal entry posted |
| Remaining Balance | `Managed − Spent` | Either of the above |
| Navbar Net Balance | `totalDebits − totalCredits` (accounting) | Journal entry posted |
