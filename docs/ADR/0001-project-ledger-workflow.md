# ADR 0001: Project Ledger Posting & Budget Tracking Workflow

## Status
Accepted (Grilling Approved by User)

## Context
AccountAnt requires project-level financial tracking, enabling users to:
1. Create Projects with structured address fields (`street`, `city`, `zip_code`), budget, dates, client name, and government indicator.
2. Select fund accounts backing the project.
3. Post double-entry journal items (`LedgerAccountItem`) tagged to projects and restricted to allocated project funds.
4. Track running balance vs project budget in real-time across both project log and company general ledger.

## Confirmed Architectural Decisions

### 1. Address Schema Strategy
- **Decision: Option B (Structured Fields)**: Address is modeled as structured input fields (`street`, `city`, `zip_code`) on the project record for consistent display and location tracking.

### 2. Dual Ledger Synchronization
- **Decision: Option A (Dual Ledger Sync)**: Posting a journal entry from a project dashboard updates both the project-specific transaction log (`useProjects`) and posts a corresponding double-entry item in the company-wide general ledger (`useAccounting`). This keeps global trial balances (`Net Ledger Balance`, `Total Debits`, `Total Credits`) fully reconciled.

### 3. Budget Overflow & Over-Debit Handling
- **Decision: Option A (Soft Warning)**: Allow entries to be posted even if total debits exceed available fund balances, but render prominent `[OVER BUDGET]` visual warnings and negative balance styling.

### 4. Route Structure & Naming Convention
- **Decision: Option B (Strict `/project/[id]` Route)**: Project details and dashboards use the `/project/[id]` route pattern.
