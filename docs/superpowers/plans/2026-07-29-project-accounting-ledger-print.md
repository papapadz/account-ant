# Project Accounting Ledger Print Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Update the printable report in `project/[id].vue` to an Accounting Ledger format (instead of Balance Sheet), format all dates as `MM/DD/YY`, and display Fund Codes (e.g. `FND-101`) for fund sources.

**Architecture:** Update document headers, modal labels, and button texts in `project/[id].vue` from "Balance Sheet" to "Accounting Ledger". Implement date formatting function `formatDateMMDDYY(dateStr)` to convert ISO dates to `MM/DD/YY`. Resolve Fund Source Codes (`fund_code`) from `useAccounting().fundAccounts` for display in the ledger statement.

**Tech Stack:** Vue 3 / Nuxt 3, TypeScript, Tailwind CSS.

## Global Constraints

- Change document titles and action buttons to "Project Accounting Ledger".
- All dates must be formatted strictly as `MM/DD/YY` (e.g., `07/29/26`).
- Fund Source column must display `fund_code` (e.g., `FND-101`, `FND-202`) instead of long names.

---

### Task 1: Update Composables & Helpers in `project/[id].vue`

**Files:**
- Modify: `src/frontend/app/pages/project/[id].vue`

**Interfaces:**
- Consumes: `useAccounting()`, `useProjects()`
- Produces: `formatDateMMDDYY` helper, `getFundSourceCode` helper, and updated `chronologicalStatement`

- [ ] **Step 1: Add `formatDateMMDDYY` date helper**

```typescript
const formatDateMMDDYY = (dateStr: string) => {
  if (!dateStr) return '—'
  const dateObj = new Date(dateStr)
  if (isNaN(dateObj.getTime())) return dateStr
  const month = String(dateObj.getMonth() + 1).padStart(2, '0')
  const day = String(dateObj.getDate()).padStart(2, '0')
  const year = String(dateObj.getFullYear()).slice(-2)
  return `${month}/${day}/${year}`
}
```

- [ ] **Step 2: Add `getFundSourceCode` helper**

```typescript
const getFundSourceCode = (fundId: number) => {
  const fund = accountingStore.fundAccounts.value.find(f => f.id === fundId)
  if (fund && fund.fund_code) return fund.fund_code
  const projectFund = projectsStore.fundSources.value.find(f => f.id === fundId)
  return projectFund ? `FND-${projectFund.id}` : 'FND-GEN'
}
```

- [ ] **Step 3: Update `chronologicalStatement` to map formatted date (`MM/DD/YY`) and `fund_code`**

```typescript
items.push({
  id: `FUND-${f.id}`,
  date: formatDateMMDDYY(f.date_received || project.value?.start_date || new Date().toISOString().split('T')[0]),
  type: 'allocation',
  category_name: 'Opening Capital Allocation',
  ledger_code: '1010-CASH',
  fund_code: getFundSourceCode(f.id),
  description: `Initial fund allocation for ${f.name}`,
  debit_amount: 0,
  credit_amount: Number(f.amount),
  running_balance: 0,
})
```

---

### Task 2: Update UI Labels & Printable Table Layout

**Files:**
- Modify: `src/frontend/app/pages/project/[id].vue`

- [ ] **Step 1: Rename button and modal titles**
  - Action Button: "Print Accounting Ledger"
  - Modal Title: "Project Accounting Ledger Statement"
  - Document Title Badge: "PROJECT GENERAL LEDGER STATEMENT"

- [ ] **Step 2: Update table columns & headers**
  - Columns: `Date (MM/DD/YY)`, `Item / Description`, `Ledger Account`, `Fund Code`, `Debit ($ Dr)`, `Credit ($ Cr)`, `Running Balance`

---

## Verification Plan

### Automated Tests
- Run `npm run build` in `src/frontend` to verify Vue template and TypeScript compilation.

### Manual Verification
1. Navigate to `/project/1`.
2. Click **Print Accounting Ledger**.
3. Verify that:
   - Header title is **PROJECT GENERAL LEDGER STATEMENT**.
   - All transaction dates follow `MM/DD/YY` format (e.g. `07/28/26`).
   - Fund Source column displays Fund Codes (`FND-101`, `FND-202`).
