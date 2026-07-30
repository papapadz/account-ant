# Fund Source Monthly Balance & Activity Calculation Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Refine `monthlyData` computed property in `FundSourceLineChart.vue` to compute initial fund creation allocations and journal debit/credit amount changes per month.

**Architecture:** Calculate baseline balance prior to target year, sum initial allocations and journal credits/debits per month, and accumulate monthly balance from January to December.

**Tech Stack:** Vue 3 Composition API, Nuxt 3/4, TypeScript.

## Global Constraints

- Handle initial fund creation amount as addition (`+`).
- Handle journal transaction type `'credit'` as addition (`+`) and `'debit'` as deduction (`-`).

---

### Task 1: Update `FundSourceLineChart.vue` `monthlyData` calculation logic

**Files:**
- Modify: `src/frontend/app/components/dashboard/FundSourceLineChart.vue:180-225`

- [x] **Step 1: Compute baseline prior balance**
  Sum initial allocations and journal net changes prior to target year for selected fund account(s).

- [x] **Step 2: Calculate monthly net changes for target year**
  Add initial fund allocations and credits, subtract debits for each month $0 \dots 11$.

- [x] **Step 3: Calculate cumulative running balance array**
  Produce monthly running balance progression for SVG path rendering.

---
