# Fetch Ledger Accounts & Account Items on Post Journal Modal Open Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Fetch and sync ledger accounts and account items from backend API when opening MODAL: Post Journal Entry in `src/frontend/app/pages/project/[id].vue`, and filter line items per selected ledger account.

**Architecture:** Implement a watcher on `isPostJournalModalOpen` in `project/[id].vue` to trigger `accountingStore.fetchLedgerAccounts()` and `accountingStore.fetchAccountItems()` / `projectsStore.fetchProjects()`, and refine `filteredAccountItems` computed property.

**Tech Stack:** Vue 3 Composition API, Nuxt 3/4, TypeScript.

## Global Constraints

- Asynchronously fetch ledger accounts & account items when modal opens (`isPostJournalModalOpen === true`).
- Keep smooth fallback behavior if offline or API is loading.

---

### Task 1: Add watcher for `isPostJournalModalOpen` and update modal dropdowns in `project/[id].vue`

**Files:**
- Modify: `src/frontend/app/pages/project/[id].vue:330-375,950-1000,1130-1165`

- [x] **Step 1: Add `watch(isPostJournalModalOpen)` in `project/[id].vue`**
  When `isPostJournalModalOpen` is set to `true`, call `accountingStore.fetchLedgerAccounts()`, `accountingStore.fetchAccountItems()`, and `projectsStore.fetchProjects()`.

- [x] **Step 2: Update Ledger Account `<select>` in template**
  Display item counts or linked accounts cleanly in the option label: e.g., `[{{ acc.account_code }}] {{ acc.account_name }}`.

- [x] **Step 3: Update `filteredAccountItems` computed property**
  Ensure account items filter accurately by `ledger_account_id`.

---
