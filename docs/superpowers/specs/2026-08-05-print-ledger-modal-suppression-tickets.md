# Tickets: Remove Modal Artifacts from Printed General Ledger

**Date:** 2026-08-05  
**Status:** Proposed  
**Context:** `src/frontend/app/pages/project/[id].vue`  

## Objective

Ensure that when printing the Project General Ledger Statement from the preview modal, the modal overlay background, modal card border, modal header, and action buttons are completely removed from the printed output (paper/PDF), leaving only the formatted `#printable-balance-sheet` document.

## Tracer-Bullet Tickets

### Ticket 1: Decouple Printable Document Container from Modal Teleport Tree
- **Blocked by:** None
- **What it delivers:** Move `#printable-balance-sheet` outside of the `<Modal>` Teleport tree in `[id].vue` so the document structure is independent of modal card wrappers, borders, and backdrop containers.

### Ticket 2: Isolate Print Media CSS Rules
- **Blocked by:** Ticket 1
- **What it delivers:** Configure `@media print` CSS rules in `[id].vue` to force-hide all modal overlay elements (`.fixed`, modal header, backdrop blur, `bg-[var(--bg-modal)]`) and render `#printable-balance-sheet` cleanly at page origin (`top: 0; left: 0`).

### Ticket 3: End-to-End Verification
- **Blocked by:** Ticket 2
- **What it delivers:** Verify clean build with `vue-tsc` and ensure modal preview operates properly on screen while physical/PDF print output is completely free of modal frames.
