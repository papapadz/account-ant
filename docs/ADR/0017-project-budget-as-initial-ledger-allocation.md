# ADR 0017: Project Budget as Initial Allocation for Project Ledger & Running Balance

- **Status:** Approved
- **Date:** 2026-07-31
- **Context:** `AccountAnt` Accounting System - Project Financial Calculations & General Ledger Printing

## Context & Problem Statement

Previously, project ledger statements and net project running balances were calculated by summing individual allocated fund account amounts (`project_funds.initial_amount`). However, project financial accounting requires using the **Project Budget** as the single authoritative initial capital allocation base for project-level running balance calculations and printable ledger statements. Individual fund sources represent underlying liquid backing accounts rather than the project's primary opening allocation.

## Decision Drivers

1. **Authoritative Initial Allocation Base:** The Project Budget defines the top-line capital limit allocated to a project.
2. **Unified General Ledger Printing:** In the printable Project Accounting Ledger statement, opening capital allocation must show a single entry titled **Initial Project Budget Allocation** (code `1000-BUDGET`, fund `BUDGET`), replacing separate fund source allocation lines.
3. **Clear Distinction Between Project Ledger & Fund Balances:**
   - Project Net Ledger Balance: `Project Budget + Total Credits - Total Debits`
   - Fund Source Balances: Retained as separate helper calculations (`getFundSourceRemaining` / `getProjectTotalFundAccountsBalance`) to track underlying fund source liquidity.

## Proposed Decision

1. **Backend Model (`App\Models\Accounting\Project`):**
   - Update `getRunningBalanceAttribute()` to calculate: `budget - total_expenses` (where `total_expenses` = `total_debits - total_credits`).
2. **Frontend Composables (`useProjects.ts` & `useAccounting.ts`):**
   - Update `getProjectTotalFunds(projectId)` to return `getProjectBudget(projectId)`.
   - Update `getProjectNetLedgerBalance(projectId)` to calculate `(Project Budget + Total Credits) - Total Debits`.
   - Add explicit helper `getProjectTotalFundAccountsBalance(projectId)` for summing active fund account balances.
3. **Printable Ledger View (`project/[id].vue`):**
   - In `chronologicalStatement`, replace iterating over individual fund accounts with a single `Initial Project Budget Allocation` item (`id: BUDGET-{id}`, code `1000-BUDGET`, fund `BUDGET`, amount `project.budget`).
   - Calculate running balance chronologically starting from the initial project budget allocation.

## Consequences

- **Domain Model:** Project budget becomes the official baseline for project ledger accounting.
- **Reporting:** Project Accounting Ledger statements render clean, single-source opening capital allocations.
- **Store API:** Provides decoupled functions for project ledger balance vs fund account liquidity.
