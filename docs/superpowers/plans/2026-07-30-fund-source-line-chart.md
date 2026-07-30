# Fund Source Monthly Line Chart Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build and integrate a new interactive SVG Line Chart component (`FundSourceLineChart.vue`) in `src/frontend/app/components/dashboard/` and include it on the dashboard page (`src/frontend/app/pages/index.vue`). The chart displays Fund Source Account amount changes from January to December with filter controls per Fund Source Account and per year.

**Architecture:** Create a reusableVue component `components/dashboard/FundSourceLineChart.vue` using Vue 3 Composition API, SVG path rendering, computed monthly aggregations from `useAccounting` / `useProjects`, and UI filters. Integrate the component in `pages/index.vue` within the visual analytics row.

**Tech Stack:** Vue 3 Composition API, Nuxt 3/4, Tailwind CSS v4, SVG path rendering.

## Global Constraints

- Native SVG path rendering for line chart with responsive viewBox and smooth curve / polyline points.
- No external chart libraries required; follow existing `FundExpensePieChart.vue` and `ProjectMonthlyBarChart.vue` patterns.
- Fully reactive filter per Fund Source Account and per Year.

---

### Task 1: Create `FundSourceLineChart.vue` component

**Files:**
- Create: `src/frontend/app/components/dashboard/FundSourceLineChart.vue`

**Interfaces:**
- Consumes: `useAccounting()`, `useProjects()`, `useCurrency()`, `useDate()`
- Produces: `<DashboardFundSourceLineChart />` auto-imported component in Nuxt

- [x] **Step 1: Create `FundSourceLineChart.vue` component file**
  Implement template with top header filters (Fund Account dropdown, Year dropdown), SVG line chart graphic with smooth gradient area fill, interactive month nodes with hover tooltips, and monthly breakdown legend.

- [x] **Step 2: Add reactivity and monthly calculation logic**
  Compute monthly fund account amount changes (Jan–Dec) filtered by selected fund account ID and year. Calculate SVG path coordinates (`polyline` or SVG bezier curve `d="..."`), Y-axis scales, and peak month metrics.

- [x] **Step 3: Verify component template compilation**

### Task 2: Integrate `FundSourceLineChart.vue` on Dashboard Page (`index.vue`)

**Files:**
- Modify: `src/frontend/app/pages/index.vue:90-101`

- [x] **Step 1: Embed `<DashboardFundSourceLineChart />` into `index.vue`**
  Place the new chart component alongside existing charts inside the `Visual Analytics Row`.

- [x] **Step 2: Verify page layout and rendering**

---
