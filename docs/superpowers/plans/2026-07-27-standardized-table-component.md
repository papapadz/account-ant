# Standardized Table Component Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Standardize all tables across the frontend application using the `<UiDataTable>` component modeled after the reference table in `src/frontend/app/pages/projects/index.vue`.

**Architecture:** `<UiDataTable>` (`DataTable.vue`) serves as the single source of truth for all tabular views. It provides dual-mode rendering: wide high-contrast tables on desktop (`md:table`) with generous cell padding (`py-4.5 px-5 lg:px-6`) and stacked card lists on mobile (`block md:hidden`). All pages (`projects/index.vue`, `projects/[id].vue`, `journal.vue`, `accounts.vue`, `index.vue`) consume `<UiDataTable>` with consistent slot naming, thumbnail icon alignment, mono currency formatting, status badges, and search/filter headers.

**Tech Stack:** Nuxt 4, Vue 3 (Composition API), `@ionic/vue`, Tailwind CSS.

## Global Constraints

- Use `<UiDataTable>` component (`components/ui/DataTable.vue`) for all table representations.
- Maintain responsive dual-mode layout (Desktop table view + Mobile card view).
- Preserve all existing filter, search, sorting, and pagination functionalities.
- All monetary values must use mono font formatting (`font-mono font-bold`) and color coding (`text-emerald-400`, `text-rose-400`, `text-blue-400`, `text-amber-400`).

---

### Task 1: Audit and Refine `<UiDataTable>` Component Interface

**Files:**
- Modify: `src/frontend/app/components/ui/DataTable.vue`

**Interfaces:**
- Consumes: Props `items`, `columns`, `searchable`, `searchPlaceholder`, `searchFields`, `pageSizeOptions`, `defaultPageSize`, `defaultSortKey`, `defaultSortOrder`, `customSortValue`, `responsive`.
- Produces: `<UiDataTable>` component with `#header-actions`, `#cell-[key]`, `#empty` slots.

- [x] **Step 1: Verify `<UiDataTable>` desktop and mobile slot rendering**
Ensure `<UiDataTable>` correctly maps slot templates to desktop `<td>` elements and mobile stacked card fields.

- [x] **Step 2: Verify build and component registration**
Run: `npm run build`
Expected: Output PASS with 0 errors.

---

### Task 2: Standardize Reference Table in `projects/index.vue`

**Files:**
- Modify: `src/frontend/app/pages/projects/index.vue`

**Interfaces:**
- Consumes: `<UiDataTable>` component.
- Produces: Construction Projects data table matching reference pattern (gradient thumbnail icon, client subtitle, status badges, funds in/spent/remaining, usage progress bar, manage action button).

- [x] **Step 1: Apply standardized slot structure to `projects/index.vue`**
Ensure `#cell-name` displays 10x10 gradient avatar with client subtitle, `#cell-funds`/`#cell-spent`/`#cell-remaining` use mono currency styling, `#cell-usage` displays `<UiProgressBar>`.

- [x] **Step 2: Build verification**
Run: `npm run build`
Expected: PASS

---

### Task 3: Standardize Tables in Project Detail (`projects/[id].vue`)

**Files:**
- Modify: `src/frontend/app/pages/projects/[id].vue`

**Interfaces:**
- Consumes: `<UiDataTable>` component.
- Produces: Standardized tables for Fund Sources, Reusable Line Item Library, and Transactions Log.

- [x] **Step 1: Standardize `fundSourceColumns`, `categoryColumns`, and `transactionColumns`**
Ensure column widths and slot templates for amounts, badges, transaction types, and actions match the reference standard.

- [x] **Step 2: Build verification**
Run: `npm run build`
Expected: PASS

---

### Task 4: Standardize Journal Entries Table (`management/journal.vue`)

**Files:**
- Modify: `src/frontend/app/pages/management/journal.vue`

**Interfaces:**
- Consumes: `<UiDataTable>` component.
- Produces: Double-entry debit/credit ledger table with status filter pills in `#header-actions`.

- [x] **Step 1: Standardize `journalColumns` and slot templates**
Ensure Debit (Dr) and Credit (Cr) columns align right with blue and amber font highlights.

- [x] **Step 2: Build verification**
Run: `npm run build`
Expected: PASS

---

### Task 5: Standardize General Ledger Accounts Table (`management/accounts.vue`)

**Files:**
- Modify: `src/frontend/app/pages/management/accounts.vue`

**Interfaces:**
- Consumes: `<UiDataTable>` component.
- Produces: Chart of Accounts table with account codes, fund parent badges, and created dates.

- [x] **Step 1: Standardize `accountColumns` and slot templates**
- [x] **Step 2: Build verification**
Run: `npm run build`
Expected: PASS

---

### Task 6: Standardize Dashboard Recent Transactions Stream (`pages/index.vue`)

**Files:**
- Modify: `src/frontend/app/pages/index.vue`

**Interfaces:**
- Consumes: `<UiDataTable>` component.
- Produces: Recent Journal Transactions stream table on the overview dashboard.

- [x] **Step 1: Standardize `recentColumns` and slot templates**
- [x] **Step 2: Build verification**
Run: `npm run build`
Expected: PASS

---

### Task 7: Final Verification and Build Validation

- [x] **Step 1: Execute full production build**
Run: `npm run build` in `src/frontend`
Expected: Clean build with 0 warnings or errors.
