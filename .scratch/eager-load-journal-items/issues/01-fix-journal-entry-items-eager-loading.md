# 01 — Fix Journal Entry Line Items Eager Loading & Modal Display

**What to build:**
Eager load `journalEntries.items` in `ProjectController.php` on the backend so `GET /projects` includes itemized line items, and ensure `useProjects.ts` maps `items` into `Transaction.items` so itemized entries correctly display in the breakdown modal upon creation and row click.

**Blocked by:** None — can start immediately.

**Status:** completed

- [x] `index()` and `show()` in `ProjectController.php` eager load `'journalEntries.items'`.
- [x] `fetchProjects()` in `useProjects.ts` maps `je.items` into `Transaction.items`.
- [x] Posting a new itemized journal entry in `[id].vue` preserves line items in state and API responses.
- [x] Clicking any itemized transaction row displays line items in the modal.
- [x] Frontend build succeeds.
