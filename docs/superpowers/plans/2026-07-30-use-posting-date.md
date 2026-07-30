# Use `posting_date` as Primary Date in `useProjects.ts` & `project/[id].vue` Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Integrate `posting_date` as the primary date field across `useProjects.ts` composable and `project/[id].vue` data table, search, sorting, and monthly calculations.

**Architecture:** Update `Transaction` interface in `useProjects.ts`, map `je.posting_date` in `fetchProjects()` and `addTransaction()`, and update `transactionColumns`, search fields, default sort key, and `#cell-posting_date` / `#cell-date` templates in `project/[id].vue`.

**Tech Stack:** Vue 3 Composition API, Nuxt 3/4, TypeScript.

## Global Constraints

- Use `posting_date` as the primary date attribute, falling back to `date` or `created_at`.
- Keep table backward compatibility with both `posting_date` and `date`.

---

### Task 1: Update `useProjects.ts` to map and use `posting_date`

**Files:**
- Modify: `src/frontend/app/composables/useProjects.ts:66-77,185-200,350-368,490-505`

- [x] **Step 1: Add `posting_date?: string` to `Transaction` interface**
  Update `Transaction` interface in `useProjects.ts`.

- [x] **Step 2: Map `posting_date` in `fetchProjects()`**
  Set `posting_date: je.posting_date || je.created_at?.split(' ')[0] || je.created_at?.split('T')[0]` when mapping backend journal entries.

- [x] **Step 3: Map `posting_date` in `addTransaction()`**
  Set `posting_date: tx.date` when creating local optimistic transaction record.

- [x] **Step 4: Update `getProjectMonthlyExpenses()`**
  Use `tx.posting_date || tx.date` when aggregating monthly expenses.

---

### Task 2: Update `project/[id].vue` to display, search, and sort by `posting_date`

**Files:**
- Modify: `src/frontend/app/pages/project/[id].vue:150-168,1085-1095,1190-1200`

- [x] **Step 1: Update `transactionColumns` definition**
  Set key `'posting_date'` with label `'Posting Date'`.

- [x] **Step 2: Update `<UiDataTable>` props**
  Set `:search-fields="['note', 'posting_date', 'date']"` and `default-sort-key="posting_date"`.

- [x] **Step 3: Update `#cell-posting_date` and `#cell-date` templates**
  Format displayed date string using `dateStore.formatISODate(item.posting_date || item.date || value)`.

- [x] **Step 4: Update `getTransactionSortValue` helper**
  Handle `'posting_date'` and `'date'` keys to return `item.posting_date || item.date`.

---
