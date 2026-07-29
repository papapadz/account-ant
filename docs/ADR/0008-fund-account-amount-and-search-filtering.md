# ADR 0008: Fund Account Initial Amount Tracking & Search Filtering

- **Status:** Approved
- **Date:** 2026-07-28
- **Context:** `AccountAnt` Accounting System

## Context & Problem Statement

Corporate fund accounts (`FundAccount` model) were previously registered with codes, names, and descriptions, but lacked an explicit initial capital/amount field in the database table and creation modal. Furthermore, as fund accounts scale, users required live search filtering in `funds.vue`.

## Decision Drivers

1. **Explicit Fund Capital Tracking:** Adding `amount` (decimal 15, 2) allows tracking starting fund liquidity and capital reserves directly on `FundAccount`.
2. **Visual Prominence:** Displaying the initial fund amount in a dedicated font-mono emerald balance badge on each fund card improves executive visibility.
3. **Interactive Search:** Providing a client-side live search bar in `funds.vue` enables instant filtering across fund codes, names, and descriptions.

## Decision Outcome

**Chosen Option:**
- Add `amount` column (`decimal(15,2)`) to `fund_accounts` table via migration `2026_07_28_000002_add_amount_to_fund_accounts_table.php`.
- Update `FundAccount` Eloquent model, `FundAccountController`, `AccountingSeeder`, and `useAccounting.ts`.
- Update `funds.vue` with an "Initial Fund Amount ($ USD)" input field in the creation modal, live search filtering input bar, and prominent font-mono balance display badges.
