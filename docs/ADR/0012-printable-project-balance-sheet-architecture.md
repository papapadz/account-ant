# ADR 0012: Printable Project Balance Sheet & Financial Statement Architecture

- **Status:** Proposed
- **Date:** 2026-07-29
- **Context:** `AccountAnt` Accounting System

## Context & Problem Statement

Users require formal, paper-ready financial balance sheets for individual projects to present to stakeholders, clients, and corporate auditors. The document must display official corporate header branding (Company Name, Address secondary line), project metadata, and a complete chronological ledger statement of all project debits and credits with running balance calculations.

## Decision Drivers

1. **Official Corporate Branding:** Reports must include the corporate header (`business_name`) and secondary line (`address`) from `useAuth().currentCompany` with standardized fallback values.
2. **Chronological Statement Integrity:** Line items must be ordered chronologically (oldest to newest date) to reflect accurate sequential running balance accumulation.
3. **Pristine Print Formatting:** Triggering `window.print()` must output a clean, monochrome-friendly financial statement without web navigation UI, buttons, modal overlays, or floating prototype controls.

## Proposed Decision

1. Add a **Print Balance Sheet** action button and modal preview in `project/[id].vue`.
2. Compute `chronologicalTransactions` sorted by `date` ascending (oldest to newest) with running balance calculation initialized from initial fund allocations.
3. Embed `@media print` CSS rules targeting `#printable-balance-sheet` to ensure isolated, print-perfect paper output.

## Consequences

- **UI/UX:** Users can inspect the balance sheet in a print preview modal or export to physical paper / PDF cleanly.
- **Domain:** Establishes `Project Balance Sheet` in the domain glossary as a chronological financial summary report.
