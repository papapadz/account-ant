# ADR 0006: Decoupling Ledger Accounts from Fund Accounts & Linking Catalog Items

- **Status:** Approved
- **Date:** 2026-07-28
- **Context:** `AccountAnt` Accounting System

## Context & Problem Statement

Previously, `LedgerAccount` entities required a mandatory `fund_account_id` parent link upon creation. This tightly coupled general chart of accounts (COA) entries to specific corporate fund accounts. Additionally, `AccountItem` catalog entries were standalone line items without direct assignment to specific ledger accounts, requiring manual selection of both line item and ledger account during journal entry creation.

## Decision Drivers

1. **Independent Chart of Accounts:** General ledger accounts represent standard financial classifications (Assets, Revenue, Expenses) and should exist independently of specific fund accounts.
2. **Streamlined Journal Posting:** Assigning a default `LedgerAccount` to an `AccountItem` reduces repetitive user input during transaction posting and ensures consistent accounting rules.
3. **Visual Consistency:** Transitioning `accounts.vue` from a tabular layout (`UiDataTable`) to a visual card grid matching `funds.vue` improves UX consistency across management pages while maintaining fast client-side searching.

## Considered Options

1. **Keep tight coupling between LedgerAccount and FundAccount:** Retain mandatory `fund_account_id` on creation.
2. **Decouple LedgerAccount from FundAccount and link AccountItem to LedgerAccount (Chosen):** Make `fund_account_id` nullable/removed on `LedgerAccount`, link `AccountItem` to `LedgerAccount`, and adopt card grid layouts with search.

## Decision Outcome

**Chosen Option:** Option 2.

### Architectural Consequences

- **Schema Changes:**
  - `ledger_accounts.fund_account_id` is made nullable and removed from creation workflows.
  - `account_items.ledger_account_id` is added as a nullable foreign key referencing `ledger_accounts.id`.
- **Journal Entry Integration:** Selecting an `AccountItem` when posting journal entries automatically populates the associated `ledger_account_id`.
- **UI/UX Consistency:** `accounts.vue` and `items.vue` utilize responsive glass-card grids with live search filtering.
