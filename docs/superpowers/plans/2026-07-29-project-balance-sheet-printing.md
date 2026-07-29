# Project Balance Sheet Printing Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add a formal, printable project balance sheet feature to `project/[id].vue` including company name header, address secondary line, project metadata, and all transaction line items listed in chronological order with print-optimized CSS.

**Architecture:** Add a "Print Balance Sheet" action button and print view modal in `project/[id].vue`. Source company header details from `useAuth().currentCompany` (with fallback). Compute chronological transaction line items sorted by date ascending with running balance calculations. Add CSS print styles (`@media print`) for clean paper printing.

**Tech Stack:** Vue 3 / Nuxt 3, TypeScript, Tailwind CSS, `@media print` styling.

## Global Constraints

- Header must contain Company Name (Primary), Address (Secondary line), and Project Name.
- All transaction line items for the project must be listed in chronological order (oldest to newest).
- Must include print-specific CSS so triggering `window.print()` outputs a pristine, print-ready document without UI clutter (navbars, buttons, floating switchers).

---

### Task 1: Create Balance Sheet Data Aggregation & Print State in `project/[id].vue`

**Files:**
- Modify: `src/frontend/app/pages/project/[id].vue`

**Interfaces:**
- Consumes: `useAuth()`, `useProjects()`, `useAccounting()`, `useCurrency()`
- Produces: Chronological line items array and printable balance sheet data structures

- [ ] **Step 1: Define `isPrintModalOpen` ref and `chronologicalTransactions` computed property**

```typescript
const isPrintModalOpen = ref(false)

const companyHeader = computed(() => {
  const comp = authStore.currentCompany.value
  const name = comp?.business_name || 'AccountAnt Enterprise Financial Systems'
  const street = comp?.street || '100 Financial Center Blvd'
  const barangay = comp?.barangay || 'Central Business District'
  const zip = comp?.zip || '1000'
  const address = `${street}, ${barangay} (ZIP ${zip})`
  return { name, address }
})

const chronologicalTransactions = computed(() => {
  const rawTxs = [...projectTransactions.value]
  // Sort date ascending (chronological order)
  rawTxs.sort((a, b) => new Date(a.date).getTime() - new Date(b.date).getTime())

  let runningBalance = projectsStore.getProjectTotalFunds(projectId.value)

  return rawTxs.map(tx => {
    if (tx.type === 'debit') {
      runningBalance -= Number(tx.amount)
    } else {
      runningBalance += Number(tx.amount)
    }
    return {
      ...tx,
      runningBalance,
    }
  })
})
```

- [ ] **Step 2: Add `handlePrintBalanceSheet()` helper function**

```typescript
const handlePrintBalanceSheet = () => {
  isPrintModalOpen.value = true
  setTimeout(() => {
    window.print()
  }, 300)
}
```

---

### Task 2: Build Printable Balance Sheet Modal & Printable Report View

**Files:**
- Modify: `src/frontend/app/pages/project/[id].vue`

**Interfaces:**
- Consumes: `companyHeader`, `chronologicalTransactions`, `project`
- Produces: Modal preview and `@media print` hidden/visible print document

- [ ] **Step 1: Add "Print Balance Sheet" button to header actions in `project/[id].vue`**

Add button alongside "Post Journal Entry" and "Add Fund Source":
```html
<UiButton type="button" variant="outline" size="sm" @click="isPrintModalOpen = true">
  <template #icon-left>
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
    </svg>
  </template>
  Print Balance Sheet
</UiButton>
```

- [ ] **Step 2: Add Print Preview Modal & Printable Container**

Render a dedicated printable container `#printable-balance-sheet` with:
- Primary header: Company Name (`companyHeader.name`)
- Secondary line: Company Address (`companyHeader.address`)
- Project Subheader: Project Name, Client Name, Project Address, Date Generated
- Chronological Transactions Table: Date, Ref ID, Item Category / Line Item, Fund Source, Note, Debit ($), Credit ($), Running Balance
- Summary Totals: Initial Allocation, Total Debits, Total Credits, Net Ending Balance

---

### Task 3: Implement `@media print` Styles & Verification

**Files:**
- Modify: `src/frontend/app/assets/css/main.css` or scoped `<style>` in `project/[id].vue`

- [ ] **Step 1: Add print CSS rules**

```css
@media print {
  body * {
    visibility: hidden;
  }
  #printable-balance-sheet, #printable-balance-sheet * {
    visibility: visible;
  }
  #printable-balance-sheet {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    color: #000 !important;
    background: #fff !important;
    padding: 0 !important;
    margin: 0 !important;
  }
  /* Hide modals backdrop, headers, floating switcher, buttons during print */
  .fixed, header, nav, .glass-card button, .no-print {
    display: none !important;
  }
}
```

---

## Verification Plan

### Automated Verification
- Run `npm run build` in `src/frontend` to verify Vue template & CSS compilation.

### Manual Verification
1. Open `/project/1`.
2. Click **Print Balance Sheet**.
3. Verify that the Balance Sheet modal opens, displaying:
   - Primary Header: Company Name
   - Secondary Line: Company Address
   - Project Name & Details
   - Chronological table of all transaction line items (oldest to newest) with running balances.
4. Click **Print Document** button inside modal or press `Ctrl+P`.
5. Confirm print preview shows a clean paper report without UI controls or floating switchers.
