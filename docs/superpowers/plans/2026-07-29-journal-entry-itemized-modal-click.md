# Journal Entry Itemized Modal Row Click Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Allow users to click any journal entry row in `src/frontend/app/pages/project/[id].vue` to open the itemized breakdown modal when line items exist for that transaction.

**Architecture:** Bind `@row-click="handleTransactionRowClick"` on `<UiDataTable>` in `[id].vue`. Implement `handleTransactionRowClick` to check if `item.items` contains line items, opening `openItemDetailsModal(item)`. Enhance the modal design with refined UI cues and visual badges.

**Tech Stack:** Vue 3, Nuxt 3, TypeScript, Tailwind CSS.

## Global Constraints

- Do not alter existing stores or API structures.
- Ensure smooth row interactions without breaking sorting or pagination.

---

### Task 1: Bind Row Click Handler & Open Itemized Modal in `[id].vue`

**Files:**
- Modify: `src/frontend/app/pages/project/[id].vue:152-165`
- Modify: `src/frontend/app/pages/project/[id].vue:1010-1017`

**Interfaces:**
- Consumes: `UiDataTable` `@row-click` event emitting `item: any`.
- Produces: `handleTransactionRowClick(item: any)` handler opening `isViewItemsModalOpen`.

- [ ] **Step 1: Define `handleTransactionRowClick` in `<script setup>`**

In `src/frontend/app/pages/project/[id].vue`:
```ts
const handleTransactionRowClick = (item: any) => {
  if (item && item.items && item.items.length > 0) {
    openItemDetailsModal(item)
  }
}
```

- [ ] **Step 2: Bind `@row-click="handleTransactionRowClick"` on `<UiDataTable>`**

In `src/frontend/app/pages/project/[id].vue`:
```html
      <UiDataTable
        :items="projectTransactions"
        :columns="transactionColumns"
        :searchable="true"
        search-placeholder="Search note, ledger account, or item name..."
        :search-fields="['note', 'date']"
        default-sort-key="date"
        default-sort-order="desc"
        :default-page-size="5"
        :custom-sort-value="getTransactionSortValue"
        @row-click="handleTransactionRowClick"
      >
```

- [ ] **Step 3: Refine Itemized Modal UI**

Ensure modal header displays an itemized badge, subtotal calculations, and item counts clearly.

- [ ] **Step 4: Verify Frontend Build**

Run: `cd src/frontend && npm run build`
Expected: Build passes with zero compilation errors.

- [ ] **Step 5: Commit changes**

```bash
git add src/frontend/app/pages/project/\[id\].vue docs/superpowers/plans/2026-07-29-journal-entry-itemized-modal-click.md
git commit -m "feat: open itemized breakdown modal when clicking journal entry row in project details"
```
