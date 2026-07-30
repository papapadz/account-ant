# `date_received` in `ProjectFund` Model & Database Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add `date_received` column to `project_funds` database table, Eloquent model, controller, `useProjects.ts`, and `project/[id].vue`.

**Architecture:** Create Laravel migration, update `ProjectFund` model `$fillable` & `$casts`, update `ProjectController::addFund` validation/store, update `useProjects.ts` mapping and payload, and update `project/[id].vue` modal form submission and statement calculation.

**Tech Stack:** Laravel 11/12 (PHP 8.2+), Vue 3 Composition API, Nuxt 3/4, TypeScript.

## Global Constraints

- Default `date_received` to `now()->toDateString()` if omitted in payload.
- Use `date_received` as primary allocation date in project ledger calculations.

---

### Task 1: Create Migration, Update Backend Model & Controller

**Files:**
- Create: `src/backend/database/migrations/2026_07_30_000002_add_date_received_to_project_funds_table.php`
- Modify: `src/backend/app/Models/Accounting/ProjectFund.php`
- Modify: `src/backend/app/Http/Controllers/Api/ProjectController.php`

- [x] **Step 1: Create migration `2026_07_30_000002_add_date_received_to_project_funds_table.php`**
  Add `$table->date('date_received')->nullable()->after('initial_amount')`.

- [x] **Step 2: Update `ProjectFund` model**
  Add `'date_received'` to `$fillable` and `$casts`.

- [x] **Step 3: Update `ProjectController::addFund`**
  Validate `'date_received' => 'nullable|date'` and save in `updateOrCreate()`.

---

### Task 2: Update Frontend Composable `useProjects.ts` & `project/[id].vue`

**Files:**
- Modify: `src/frontend/app/composables/useProjects.ts`
- Modify: `src/frontend/app/pages/project/[id].vue`

- [x] **Step 1: Update `FundSource` interface in `useProjects.ts`**
  Add `date_received?: string`.

- [x] **Step 2: Update `fetchProjects()` and `addFundSource()` in `useProjects.ts`**
  Map `date_received: pf.date_received` and send `date_received` in POST body payload.

- [x] **Step 3: Update `handleAddFundSource` in `project/[id].vue`**
  Pass `date_received: fundForm.date_received` when adding fund source.

- [x] **Step 4: Update `chronologicalStatement` in `project/[id].vue`**
  Use `f.date_received` as primary opening allocation date.

---
