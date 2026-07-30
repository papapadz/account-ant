# Use `posting_date` in `FundSourceLineChart.vue` Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Update `FundSourceLineChart.vue` to calculate available fiscal years and monthly transaction amounts using `posting_date`.

**Architecture:** Modify `availableYears` and `monthlyData` computed properties in `FundSourceLineChart.vue` script section to reference `t.posting_date || t.date`.

**Tech Stack:** Vue 3 Composition API, Nuxt 3/4, TypeScript.

## Global Constraints

- Use `t.posting_date || t.date` as transaction date string.

---

### Task 1: Update `FundSourceLineChart.vue` to use `posting_date`

**Files:**
- Modify: `src/frontend/app/components/dashboard/FundSourceLineChart.vue:158-210`

- [x] **Step 1: Update `availableYears` computed property**
  Use `t.posting_date || t.date` when collecting transaction years.

- [x] **Step 2: Update `monthlyData` computed property**
  Use `tx.posting_date || tx.date` when aggregating monthly amounts.

---
