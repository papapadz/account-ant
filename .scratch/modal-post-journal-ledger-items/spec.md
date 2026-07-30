# Feature Specification: Load Ledger Accounts & Account Items on Post Journal Entry Modal Open (`project/[id].vue`)

## Problem Statement
When opening the **Post Journal Entry** modal in `src/frontend/app/pages/project/[id].vue`, the ledger accounts list and their associated catalog account items should be freshly fetched and synchronized from the backend database via `accountingStore.fetchLedgerAccounts()` and `accountingStore.fetchAccountItems()`. The account items select box must dynamically filter and present items linked to the chosen `ledger_account_id`.

## Solution
1. Add a `watch(isPostJournalModalOpen, async (isOpen) => { ... })` in `src/frontend/app/pages/project/[id].vue` to fetch the latest `ledgerAccounts` and `accountItems` whenever the Post Journal Entry modal opens.
2. Update `filteredAccountItems` computed property to strictly filter active categories/account items by `ledger_account_id` when selected.
3. Show ledger account code & name along with count of linked account items in the dropdown options.

## User Stories
1. As an accountant, when I open the Post Journal Entry modal on a project page, I want the ledger account list and its line items to be up-to-date from the accounting system, so that I select valid accounts and categories.
2. As a user selecting a Ledger Account in the modal, I want the Account Item dropdown to filter to only show items linked to that specific ledger account.

## Implementation Decisions
- **Page Component**: `src/frontend/app/pages/project/[id].vue`.
- **Stores**: `useAccounting()` (`fetchLedgerAccounts`, `fetchAccountItems`) and `useProjects()`.
- **Reactivity**: `watch(isPostJournalModalOpen)` watcher trigger.

## Testing Decisions
- **Testing Seam**: Vue component modal lifecycle seam on `/project/:id`.
- **Verification**: Open Post Journal Entry modal, inspect ledger account select dropdown, select a ledger account, and verify filtered account items update accordingly.
