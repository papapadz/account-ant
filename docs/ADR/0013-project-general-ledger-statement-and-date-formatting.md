# ADR 0013: Project General Ledger Statement Format & Date Formatting Standards

- **Status:** Proposed
- **Date:** 2026-07-29
- **Context:** `AccountAnt` Accounting System

## Context & Problem Statement

ADR-0012 established printable financial statements for projects. Users specifically requested an **Accounting Ledger Statement** layout rather than a static Balance Sheet. The General Ledger format requires concise fund identification (`fund_code` e.g., `FND-101`) and standardized date formatting (`MM/DD/YY`).

## Decision Drivers

1. **Standardized Ledger Layout:** General ledger reports focus on chronological transaction line items with explicit ledger account codes, concise fund identifiers, and running balance tracking.
2. **Compact Date Representation:** Formatting dates as `MM/DD/YY` optimizes table column width for printable paper statements and matches standard corporate ledger conventions.
3. **Fund Code References:** Referencing fund accounts via `fund_code` (e.g. `FND-101`, `FND-202`) provides unambiguous accounting identification without consuming excess line space.

## Proposed Decision

1. Rename printable report in `project/[id].vue` to **Project Accounting Ledger**.
2. Implement date formatter `formatDateMMDDYY` converting dates to `MM/DD/YY` (e.g. `07/29/26`).
3. Display `fund_code` in the Fund Source column of the ledger statement.

## Consequences

- **Glossary:** Updates `Project Balance Sheet` entry in `docs/glossary.md` to `Project Accounting Ledger`.
- **UI:** Replaces "Print Balance Sheet" button and modal title with "Print Accounting Ledger".
