# Standardize Posting Date in `project/[id].vue` Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Standardize date values in `src/frontend/app/pages/project/[id].vue` to use posting date formatting via `useDate().formatISODate()`.

**Architecture:** Instantiate `const dateStore = useDate()` in `project/[id].vue` and update template cells (`#cell-date`, `#cell-date_received`, transaction detail modals) to format posting dates cleanly.

**Tech Stack:** Vue 3 Composition API, Nuxt 3/4, TypeScript.

## Global Constraints

- Use `dateStore.formatISODate(item.date)` for posting date displays.
- Keep fallback handling for null/undefined values.

---

### Task 1: Instantiate `useDate` and update posting date rendering in `project/[id].vue`

**Files:**
- Modify: `src/frontend/app/pages/project/[id].vue:165-170,910-918,1060-1085`

- [x] **Step 1: Instantiate `const dateStore = useDate()` in `script setup`**
  Import and initialize `useDate()` composable in `project/[id].vue`.

- [x] **Step 2: Update `#cell-date` template to display posting date ISO format**
  Update `<template #cell-date="{ item }">` to use `dateStore.formatISODate(item.date)`.

- [x] **Step 3: Update `#cell-date_received` template for fund sources table**
  Update `<template #cell-date_received="{ item }">` to use `dateStore.formatISODate(item.date_received)`.

- [x] **Step 4: Update form default date initializers to use `dateStore.formatISODate()`**
  Update `journalForm.date` and `fundForm.date_received` initializers.

---
