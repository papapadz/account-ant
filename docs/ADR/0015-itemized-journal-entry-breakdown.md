# 15. Itemized Journal Entry Breakdown and Modal UI Architecture

Date: 2026-07-29

## Status

Accepted

## Context

When posting a journal entry for a project, users often purchase multiple physical goods or services (e.g., 100 bags of cement @ $12.50/bag, 50 pcs rebar @ $18.00/pcs) under a single financial entry. Previously, journal entries only accepted a single manual amount and a text memo.

Users required an itemized breakdown option allowing multiple items with quantity, unit of measurement, unit price, and auto-computed line subtotals, where the sum of item subtotals determines the posted entry amount.

## Decision

1. **Itemized Mode Toggle & State Retain**:
   - Provide an optional toggle switch in the Post Journal Entry modal.
   - Retain entered itemized rows in component memory during modal interaction, preserving row input state even if the toggle is switched off and back on.

2. **Unit Input Architecture**:
   - Hybrid Unit Dropdown + Freeform: Provide standard construction units (`pcs`, `bags`, `hrs`, `cu m`, `kg`, `sq m`, `set`, `tons`, `lot`) with freeform custom text entry support.

3. **Backend & Store Payload**:
   - Send itemized items as a JSON payload (`items: [{ description, quantity, unit, price, subtotal }]`) in `addTransaction` requests and store state.

4. **Transaction Table & Statement View**:
   - In the project transactions data table, display a summary count badge (`(N items)`) when an entry is itemized. Clicking the badge opens a quick item breakdown popover/modal.
   - Do NOT render itemized line breakdowns in the printable general ledger balance sheet statement to keep print statements compact and aggregated.

## Consequences

- Improves expense auditability and detail for project managers.
- Automatically prevents manual calculation errors for entry amounts.
- Keeps printable statements clean while supporting interactive granular inspection on screen.
