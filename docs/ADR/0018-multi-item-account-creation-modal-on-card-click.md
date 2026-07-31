# 0018. Multi-Item Account Item Creation Modal on Card Click

## Status
Accepted

## Context
In the Ledger Accounts Management view (`accounts.vue`), users needed a direct and quick way to add Account Catalog Items associated with a specific Ledger Account. Previously, adding items required navigating to the separate Account Items page or manually picking the ledger account from a dropdown.

## Decision
1. **Interactive Card Trigger**: Clicking anywhere on a Ledger Account card in `accounts.vue` triggers an "Add Account Items" modal scoped to that specific Ledger Account, with an explicit "+ Add Item" badge on the card as a visual indicator.
2. **Multi-Item Entry**: The modal supports adding multiple account items in a single submission session, complete with dynamic row additions, auto-generated code suggestions (`<ACCOUNT_CODE>-01`), and row deletion.
3. **Clear Transaction Terminology**: Transaction types in the modal use explicit labels **Outflow** (debit) and **Inflow** (credit).

## Consequences
- Streamlines catalog item creation directly from the ledger management workflow.
- Reduces repetitive modal opens when creating multiple items for the same ledger account.
- Keeps UI consistent across management pages.
