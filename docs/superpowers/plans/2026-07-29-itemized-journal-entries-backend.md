# Feature Specification: Itemized Journal Entry Breakdown (Backend Logic & Database)

## Problem Statement

When users post project transactions (debits/credits) for structural materials, equipment rentals, or labor line items, they need to log itemized breakdowns (description, quantity, unit, price, line subtotal) associated with the transaction. Currently, the backend `ledger_account_items` table only persists aggregate single amounts and plain text memos without structured database storage for multi-item breakdowns.

## Solution

Implement database schema migrations, Eloquent model relationships, controller request validation, and API payload responses to persist and retrieve structured itemized breakdown line items (`journal_entry_items` table) linked to parent journal entries (`ledger_account_items`).

## User Stories

1. As a project manager, I want to post a journal entry with multiple itemized line items (e.g., cement bags, rebar pieces), so that every material purchase is individually tracked in the database.
2. As a project accountant, I want to query journal entries via `/api/journal-entries?project_id={id}` and receive full itemized breakdown payloads (`items`), so that audit reports display exact quantities and unit prices.
3. As a database administrator, I want child itemized line items to cascade-delete automatically when a parent journal entry is deleted, so that orphaned records are prevented.
4. As an API client, I want POST `/api/journal-entries` to validate itemized array payloads (ensuring positive quantities, unit prices, and non-empty descriptions), so that corrupt financial records are rejected.

## Implementation Decisions

- **Database Table**: `journal_entry_items` with foreign key `ledger_account_item_id` referencing `ledger_account_items(id)` with `onDelete('cascade')`.
- **Eloquent Models**:
  - `JournalEntryItem` (`description`, `quantity`, `unit`, `price`, `subtotal`).
  - `LedgerAccountItem` hasMany `items` (`JournalEntryItem::class`).
- **Controller Logic**:
  - `LedgerAccountItemController@index`: Eager-loads `items` relationship.
  - `LedgerAccountItemController@store`: Validates optional `items` array and creates child records via `$entry->items()->createMany(...)`.

## Testing Decisions

- **Test Seam**: `tests/Feature/LedgerAccountItemTest.php` using Laravel PHPUnit testing framework.
- **Coverage**: Validates POST payload processing, database assertions in `journal_entry_items`, and GET responses containing `items` relationships.

## Out of Scope

- Editing or patching individual itemized lines after transaction posting (journal entries remain immutable once posted to preserve financial audit trails).
