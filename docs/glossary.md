# Domain Glossary: AccountAnt Project Ledger System

## Core Glossary Terms

| Term | Definition |
|------|------------|
| **Project** | A specific enterprise or government engagement with defined budget, timeframe, client, and address details. |
| **Fund Account** | A designated corporate or institutional funding pool (e.g., General Operating Fund, CapEx Fund) with an explicit initial capital amount. |
| **Fund Account Initial Amount** | The starting capital balance assigned to a corporate fund account upon creation. |
| **Ledger Account** | A general ledger account in the Chart of Accounts (e.g., Cash, Software Revenue, IT Equipment) independent of fund accounts. |
| **AccountItem** | Standardized catalog item (e.g., Cloud Hosting Infrastructure) assigned a transaction type (`debit` or `credit`) and linked to a default `LedgerAccount` for automatic pre-filling in journal entries. |
| **Project Fund Allocation** | The mapping of a specific `FundAccount` to a `Project`, initialized with a designated `initial_amount`. |
| **Initial Fund Amount** | The starting capital/credit limit assigned to a project from a specific fund account. |
| **Ledger Account Item** | An individual double-entry transaction item (Debit `Dr` or Credit `Cr`) posted against a ledger account and fund source. |
| **Project Journal Entry** | A `LedgerAccountItem` tagged with a `project_id` and restricted to fund sources allocated to that project. |
| **Project Accounting Ledger** | Formal printable general ledger statement for a specific project featuring company header, secondary address line, project details, MM/DD/YY formatted dates, concise fund codes (`FND-101`), and chronological transaction line items with running balances. |
| **Running Balance** | The current available funds remaining for a project fund source (`Initial Amount - Debits + Credits`). |
| **Project Budget** | The total allocated financial limit assigned to a project upon creation. |
| **Budget Utilization Rate** | The calculated percentage of total project budget consumed by net expenses (`(Total Debits - Total Credits) / Budget * 100`). |
| **Over-Budget Warning** | Visual indicator triggered when net expenses posted against a project exceed its total allocated budget. |
| **Button Component (`Button.vue`)** | The canonical Vue component (`src/frontend/app/components/ui/Button.vue`) encapsulating standardized variants (`primary`, `secondary`, `outline`, `danger`, `ghost`), micro-animations, loading spinners, and semantic HTML button behavior across all pages and modals. |
| **Tabs Component (`Tabs.vue`)** | The canonical Vue component (`src/frontend/app/components/ui/Tabs.vue`) providing responsive, touch-friendly tab navigation across 3 visual variants (`segmented`, `pills`, `underline`) with smooth active indicator transitions, badge counters, and keyboard navigation. |
| **Itemized Journal Entry** | A `Project Journal Entry` containing a granular list of individual item line rows (`Itemized Breakdown`), each with description, quantity, unit, and unit price, where entry total is calculated as the sum of line subtotals. |
| **Item Breakdown Modal** | Interactive popover/modal launched by clicking an entry's `(N items)` badge in the transactions data table to view line item details. |
| **`JournalEntryItem` Model** | Eloquent model representing child line items (`journal_entry_items` table) with SoftDeletes support, linked to parent `LedgerAccountItem`. |
| **Atomic Journal Transaction** | Wrapping parent `LedgerAccountItem` creation and child `JournalEntryItem` row insertion inside `DB::transaction()` to ensure all-or-nothing database persistence. |


