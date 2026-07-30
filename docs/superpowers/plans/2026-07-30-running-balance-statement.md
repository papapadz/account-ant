# Correct Running Balance Computation & Display Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Correctly compute and display chronological running balances using `posting_date` in `src/frontend/app/pages/project/[id].vue`.

**Architecture:** Update `chronologicalStatement` computed property in `project/[id].vue` to store raw ISO posting dates, sort by `rawDate` ascending, accumulate running balance (`balance += credit - debit`), and format displayed date cell with `dateStore.formatISODate(item.rawDate)`.

**Tech Stack:** Vue 3 Composition API, Nuxt 3/4, TypeScript.

## Global Constraints

- Preserve chronological order (oldest posting date first).
- Correctly compute running balance: `+ Credit Amount - Debit Amount`.

---

### Task 1: Update `chronologicalStatement` computed property and table template in `project/[id].vue`

**Files:**
- Modify: `src/frontend/app/pages/project/[id].vue:828-847,1370-1435`

- [x] **Step 1: Store `rawDate` in `chronologicalStatement` items**
  Set `rawDate = f.date_received || ...` for allocations and `rawDate = t.posting_date || t.date` for journal entries.

- [x] **Step 2: Sort items by `rawDate` ascending**
  Sort by `new Date(a.rawDate).getTime() - new Date(b.rawDate).getTime()`.

- [x] **Step 3: Accumulate running balance chronologically**
  Calculate `balance += item.credit_amount - item.debit_amount`.

- [x] **Step 4: Update statement table date cell in template**
  Render `dateStore.formatISODate(item.rawDate || item.date)` in table row.

---
