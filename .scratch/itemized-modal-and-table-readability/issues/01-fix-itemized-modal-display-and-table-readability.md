# 01 — Fix Itemized Journal Entry Modal Display & Enhance Table Readability

**What to build:**
Ensure itemized line items are preserved when fetching project journal entries from the backend API in `useProjects.ts`, allow clicking any transaction row to show itemized details (or transaction details), and increase table row height, typography sizes, badges, and font contrast across `DataTable.vue` and `[id].vue` to make the transactions table significantly larger and more readable.

**Blocked by:** None — can start immediately.

**Status:** completed

- [x] `fetchProjects()` in `useProjects.ts` maps `je.items` and `je.journal_entry_items` into `Transaction.items`.
- [x] Clicking any transaction row in `[id].vue` opens the modal showing itemized breakdown if available.
- [x] Table header and row text sizes in `DataTable.vue` and cell slots in `[id].vue` are upgraded from `text-xs`/`text-[10px]` to `text-sm`/`text-base` with improved spacing and contrast.
- [x] Itemized modal displays all line items, quantities, units, prices, and subtotals with high-contrast formatting.
- [x] Frontend build succeeds without errors.
