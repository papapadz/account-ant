# ADR 0011: Account Item Transaction Types & Ledger Account Reverse Selection

- **Status:** Proposed
- **Date:** 2026-07-29
- **Context:** `AccountAnt` Accounting System

## Context & Problem Statement

Currently, `AccountItem` catalog entries lack an explicit classification of whether they represent debit (expense) or credit (income/refund) entries. Consequently, when posting a project journal entry, users must manually choose both the transaction type (`debit` vs `credit`) and the line item category. Furthermore, ADR-0006 linked `AccountItem` to `LedgerAccount`, but the UI required picking the line item to resolve the ledger account.

To streamline data entry and prevent invalid ledger postings, we need to enforce transaction types at the catalog level (`AccountItem`) and invert the selection workflow in project journal entries (selecting a `LedgerAccount` auto-fills and filters its associated `AccountItem` and transaction type).

## Decision Drivers

1. **Standardized Accounting Direction:** Pre-defining `transaction_type` (`debit` | `credit`) on catalog items guarantees consistent financial reporting across all project transactions.
2. **Simplified Journal Entry Workflow:** Removing manual transaction type selection reduces user friction and human error during project journal posting.
3. **Ledger-First Selection Flow:** In project journal entries, selecting a `LedgerAccount` automatically scopes and fills the corresponding `AccountItem` and transaction type.

## Proposed Decision

1. Extend `account_items` schema with a mandatory/defaulted `transaction_type` enum (`debit`, `credit`).
2. Update `/management/items` create item modal to require selecting `debit` or `credit` when creating catalog items.
3. In `project/[id].vue` journal entry modal:
   - Remove the manual Transaction Type selection toggle.
   - When a `LedgerAccount` is selected, auto-populate the line item (`AccountItem`) and infer its `transaction_type` (`debit` or `credit`).

## Consequences

- **Schema:** Adds `account_items.transaction_type` enum (`debit`, `credit`) defaulting to `debit`.
- **Glossary:** Updates `AccountItem` domain definition in `docs/glossary.md` to include default transaction direction (`debit` | `credit`).
- **UI UX:** Project journal posting requires one fewer input step and enforces catalog-driven debit/credit classification.
