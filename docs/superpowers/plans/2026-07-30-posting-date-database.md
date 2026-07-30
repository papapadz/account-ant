# `posting_date` Field in `LedgerAccountItem` Model & Database Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add `posting_date` column to `ledger_account_items` database table and Eloquent model, and update frontend project details page to persist posting date to database.

**Architecture:** Create a Laravel migration adding `posting_date` to `ledger_account_items`, update `LedgerAccountItem` model fillable attributes, update `LedgerAccountItemController` validation and store logic, and update frontend composables & `project/[id].vue` form submission payload.

**Tech Stack:** Laravel 11/12 (PHP 8.2+), Vue 3 Composition API, Nuxt 3/4, TypeScript.

## Global Constraints

- `posting_date` stored as ISO date string (`YYYY-MM-DD`).
- Fallback to current date `now()->toDateString()` if `posting_date` or `date` is omitted in API request.

---

### Task 1: Add Migration & Update Backend `LedgerAccountItem` Model & Controller

**Files:**
- Create: `src/backend/database/migrations/2026_07_30_000001_add_posting_date_to_ledger_account_items_table.php`
- Modify: `src/backend/app/Models/Accounting/LedgerAccountItem.php`
- Modify: `src/backend/app/Http/Controllers/Api/LedgerAccountItemController.php`

- [x] **Step 1: Create migration file for `posting_date`**
  Add `$table->date('posting_date')->nullable()->after('description')` in migration `up()` method.

- [x] **Step 2: Update `LedgerAccountItem` model**
  Add `'posting_date'` to `$fillable` array and `$casts` in `LedgerAccountItem.php`.

- [x] **Step 3: Update `LedgerAccountItemController` store logic**
  Validate `'posting_date' => 'nullable|date'` and `'date' => 'nullable|date'` in `store()`, and save `$validated['posting_date'] ?? $request->input('date') ?? now()->toDateString()` to model record.

---

### Task 2: Update Frontend Composables & `project/[id].vue` to Send `posting_date` Payload

**Files:**
- Modify: `src/frontend/app/composables/useAccounting.ts`
- Modify: `src/frontend/app/composables/useProjects.ts`
- Modify: `src/frontend/app/pages/project/[id].vue`

- [x] **Step 1: Update `LedgerAccountItem` interface in `useAccounting.ts`**
  Add `posting_date?: string` property to `LedgerAccountItem` interface.

- [x] **Step 2: Update `addTransaction` in `useProjects.ts`**
  Pass `posting_date: tx.date` in API payload to `/journal-entries`.

- [x] **Step 3: Update transaction submission handler in `project/[id].vue`**
  Ensure `journalForm.date` is passed in `addTransaction` payload.

---
