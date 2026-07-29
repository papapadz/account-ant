# 16. Backend Itemized Journal Entry Database & Controller Architecture

Date: 2026-07-29

## Status

Accepted

## Context

Frontend support for itemized journal entries was implemented in the Nuxt UI layer. To ensure persistent, audited financial tracking, the Laravel backend (`src/backend`) requires a structured relational schema (`journal_entry_items`), API validation, atomic transaction handling, and model relationships.

## Decision

1. **Relational Table & SoftDeletes**:
   - Create `journal_entry_items` table linked to `ledger_account_items(id)` via foreign key constraint (`onDelete('cascade')`).
   - Include `SoftDeletes` (`deleted_at` column) on `JournalEntryItem` model to match `LedgerAccountItem` soft-delete capabilities.

2. **Server-Side Financial Calculation**:
   - Server strictly computes line item subtotal ($ \text{subtotal} = \text{round}(\text{quantity} \times \text{price}, 2) $) to guarantee math integrity across all API inputs.

3. **Atomic Database Transactions (`DB::transaction`)**:
   - Wrap parent `LedgerAccountItem::create` and child `$entry->items()->createMany(...)` inside `DB::transaction()` so any validation or insertion error rolls back the entire entry atomically.

4. **Eager Loading**:
   - Always eager-load `items` in `LedgerAccountItemController@index` query (`->with(['ledgerAccount', 'fundAccount', 'accountItem', 'project', 'user', 'items'])`).

## Consequences

- Prevents corrupted or partial journal entry records in the database.
- Guarantees exact subtotal precision ($ \text{quantity} \times \text{price} $) server-side.
- Matches existing Laravel soft-delete patterns for audit compliance.
