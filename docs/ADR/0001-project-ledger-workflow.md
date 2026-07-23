# ADR 0001: Project Ledger Posting & Budget Tracking Workflow

## Status
Proposed (Under Grilling Review)

## Context
AccountAnt requires project-level financial tracking, enabling users to:
1. Create Projects with complete address models, budget, dates, client name, and government indicator.
2. Select fund accounts backing the project.
3. Define initial fund amounts allocated to each project fund source.
4. Post double-entry journal items (`LedgerAccountItem`) tagged to projects and restricted to allocated project funds.
5. Track running balance vs project budget in real-time.

## Key Architectural Questions Under Review

### 1. Address Schema Strategy
- **Option A (Recommended)**: Embed address fields (`city_id`, `house_number`, `street`, `village`, `barangay`, `zip`) directly on `projects` table, linking directly to the existing `cities` table model.
- **Option B**: Create a dedicated `project_addresses` pivot table.

### 2. Fund Running Balance Formula & Debit/Credit Convention
- **Option A (Recommended)**: `Fund Running Balance = Initial Amount - Total Debits + Total Credits`.
- **Option B**: `Fund Running Balance = Initial Amount - Total Debits`.

### 3. Budget Overflow Handling
- **Option A (Recommended)**: Soft Warning — Allow posting but display prominent visual `[OVER BUDGET]` badges and warnings.
- **Option B**: Hard Block — Reject transaction via API validation error if entry exceeds remaining budget/fund balance.

### 4. Initial Fund Allocation Accounting Mechanism
- **Option A (Recommended)**: Initial fund allocation is stored in `project_funds` pivot table as the starting capital budget limit for that fund source.
- **Option B**: Auto-generate an opening Debit/Credit journal entry when an initial fund amount is assigned to a project.
