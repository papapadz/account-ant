# Project Budgeting, Card View & Analytics Dashboard Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Enhance project creation with a `budget` (max amount) field, set grid card view as default with live search and status filters, and embed an interactive visual analytics panel containing a fund expense pie chart and a monthly project expense bar chart in `src/frontend/app/pages/projects/index.vue` and `src/frontend/app/pages/index.vue`.

**Architecture:** Extend Vue 3 / Nuxt 3 composable `useProjects.ts`, create reusable SVG chart components (`FundExpensePieChart.vue`, `ProjectMonthlyBarChart.vue`), update project creation form and state in `projects/index.vue` and `index.vue`, and reference ADR 0007 (`docs/ADR/0007-project-budgeting-cards-and-analytics-dashboard.md`).

**Tech Stack:** Nuxt 3 (Vue 3, TypeScript, Tailwind CSS), SVG graphics.

## Global Constraints

- Retain existing design system color tokens (`emerald-500`, `blue-500`, `amber-500`, `rose-500`, `glass-card`, `border-[var(--border-color)]`).
- Default viewMode set to `'cards'`.
- Calculate pie chart slices from project transactions for the selected fund.
- Calculate bar chart monthly expenses from project transactions across 12 months.

---

### Task 1: Update Composable (`useProjects.ts`) with `budget` & Analytics Helpers

**Files:**
- Modify: `src/frontend/app/composables/useProjects.ts`

- [ ] **Step 1: Update `Project` interface and seed projects with budget values**

In `src/frontend/app/composables/useProjects.ts`:
```typescript
export interface Project {
  id: number
  name: string
  description?: string
  budget?: number
  address?: ProjectAddress | string
  client_name: string
  start_date: string
  status: 'active' | 'on-hold' | 'completed'
  created_at: string
}
```

Set `budget` in seed data:
```typescript
    {
      id: 1,
      name: 'Oakwood Commercial Complex',
      description: 'Multi-story commercial office building with basement parking and solar rooftop array.',
      budget: 500000.00,
      // ...
    },
    {
      id: 2,
      name: 'Riverfront Villa Residence',
      description: 'Luxury 2-story waterfront villa featuring smart home automation and infinity pool.',
      budget: 250000.00,
      // ...
    },
    {
      id: 3,
      name: 'City Center Office Renovation',
      description: 'Interior fit-out, acoustic partitioning, and HVAC infrastructure upgrade for tech office.',
      budget: 120000.00,
      // ...
    },
```

- [ ] **Step 2: Add calculation helpers for fund expense breakdown and monthly expenses**

In `useProjects.ts`:
```typescript
  const getFundProjectExpenses = (fundAccountId: number) => {
    // Aggregates expenses per project for the given fund
    const fund = fundSources.value.find(f => f.id === fundAccountId)
    if (!fund) return []
    const projectTxs = transactions.value.filter(t => t.fund_source_id === fundAccountId && t.type === 'debit')
    const totalSpent = projectTxs.reduce((sum, t) => sum + Number(t.amount), 0)
    
    const projectMap = new Map<number, number>()
    for (const tx of projectTxs) {
      const cur = projectMap.get(tx.project_id) || 0
      projectMap.set(tx.project_id, cur + Number(tx.amount))
    }

    return Array.from(projectMap.entries()).map(([pId, spent]) => {
      const proj = projects.value.find(p => p.id === pId)
      return {
        projectId: pId,
        projectName: proj ? proj.name : `Project #${pId}`,
        amount: spent,
        percentage: totalSpent > 0 ? Math.round((spent / totalSpent) * 100) : 0,
      }
    })
  }

  const getProjectMonthlyExpenses = (projectId: number) => {
    // Array of 12 months for selected project
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
    const monthlyData = months.map((month, idx) => ({ month, monthIndex: idx + 1, amount: 0 }))

    const projTxs = transactions.value.filter(t => t.project_id === projectId && t.type === 'debit')
    for (const tx of projTxs) {
      if (!tx.date) continue
      const dateObj = new Date(tx.date)
      const mIdx = dateObj.getMonth()
      if (mIdx >= 0 && mIdx < 12) {
        monthlyData[mIdx].amount += Number(tx.amount)
      }
    }

    return monthlyData
  }
```

---

### Task 2: Create Visual Analytics Components (Pie & Bar Charts)

**Files:**
- Create: `src/frontend/app/components/dashboard/FundExpensePieChart.vue`
- Create: `src/frontend/app/components/dashboard/ProjectMonthlyBarChart.vue`

- [ ] **Step 1: Create `FundExpensePieChart.vue`**

In `src/frontend/app/components/dashboard/FundExpensePieChart.vue`:
SVG Donut chart with fund selector, percentage arcs, legend, and formatted totals.

- [ ] **Step 2: Create `ProjectMonthlyBarChart.vue`**

In `src/frontend/app/components/dashboard/ProjectMonthlyBarChart.vue`:
SVG/HTML Bar chart showing monthly expenses (Jan - Dec) for a selected project with hover tooltips and dynamic peak scaling.

---

### Task 3: Build Projects Dashboard in `projects/index.vue` and `index.vue`

**Files:**
- Modify: `src/frontend/app/pages/projects/index.vue`
- Modify: `src/frontend/app/pages/index.vue`

- [ ] **Step 1: Add Budget Input to Create New Project Modal**

Add `<UiInput v-model.number="newForm.budget" type="number" step="1000" label="Max Project Budget ($ USD)" placeholder="e.g. 500000" :required="true" />`.

- [ ] **Step 2: Set Card Grid as default view with live search & status filters**

Set `viewMode = ref<'cards' | 'table'>('cards')` and implement search input filtering for cards.

- [ ] **Step 3: Embed Analytics & Charts Panel**

Add a tabbed / section panel below the project grid cards containing `FundExpensePieChart` and `ProjectMonthlyBarChart`.

---

### Task 4: Verification & Testing

- [ ] **Step 1: Run `npm run build` in `src/frontend`**
- [ ] **Step 2: Test project creation modal with budget input**
- [ ] **Step 3: Test card search and status tabs**
- [ ] **Step 4: Test fund pie chart and project bar chart interactions**
