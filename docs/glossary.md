# Domain Glossary: AccountAnt Project Ledger System

## Core Glossary Terms

| Term | Definition |
|------|------------|
| **Project** | A specific enterprise or government engagement with defined budget, timeframe, client, and address details. |
| **Fund Account** | A designated corporate or institutional funding pool (e.g., General Operating Fund, CapEx Fund). |
| **Project Fund Allocation** | The mapping of a specific `FundAccount` to a `Project`, initialized with a designated `initial_amount`. |
| **Initial Fund Amount** | The starting capital/credit limit assigned to a project from a specific fund account. |
| **Ledger Account Item** | An individual double-entry transaction item (Debit `Dr` or Credit `Cr`) posted against a ledger account and fund source. |
| **Project Journal Entry** | A `LedgerAccountItem` tagged with a `project_id` and restricted to fund sources allocated to that project. |
| **Running Balance** | The current available funds remaining for a project fund source (`Initial Amount - Debits + Credits`). |
| **Budget Utilization** | The percentage of the total project budget consumed by posted transactions (`(Total Spent / Total Budget) * 100`). |
| **Is Government** | Boolean flag indicating whether the project is subject to public sector accounting compliance & auditing rules. |
