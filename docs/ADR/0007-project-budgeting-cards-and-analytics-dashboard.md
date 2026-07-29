# ADR 0007: Project Budgeting, Card View Default & Interactive Analytics Dashboard

- **Status:** Approved
- **Date:** 2026-07-28
- **Context:** `AccountAnt` Project Management & Accounting System

## Context & Problem Statement

Projects previously focused on tracking allocated funds and expenses, but lacked an explicit user-defined max budget limit during project creation. Additionally, while tabular views were available, users requested card-grid view as the default interface with live search and status filtering, alongside visual analytics (fund expense pie charts and monthly project expense bar charts).

## Decision Drivers

1. **Explicit Budget Control:** Adding a `budget` (max amount) property on project creation provides a clear baseline for tracking budget utilization rates and over-budget warnings.
2. **Card-First User Experience:** Card grid layout offers superior scannability for construction and enterprise projects, with search and status tabs improving navigation speed.
3. **Visual Financial Analytics:** Interactive charts (Fund Expense Pie Chart & Monthly Project Expense Bar Chart) provide immediate insight into capital allocation and monthly burn rates.

## Decision Outcome

**Chosen Option:**
1. Extend `Project` model and creation modal to capture `budget` (max amount).
2. Set Grid Cards view as default for `projects/index.vue` and `index.vue` with search & status filters.
3. Add a dedicated Visual Analytics section below project cards featuring interactive SVG Pie & Bar charts.
