# Fix Total Spent Calculation and Dashboard KPI Metric Display

## Parent
Total Spent KPI card calculation in `src/frontend/app/pages/index.vue`

## What to build
Audit and fix `totalAppSpent` in `useProjects.ts` to calculate net paid spent (`paid debits` minus `paid credits/refunds`). Update `index.vue` Total Spent KPI card to render net paid spent accurately with visual context for gross spent, refunds, and unpaid AP.

## Acceptance criteria
- [x] `totalAppSpent` in `useProjects.ts` computes net paid spent (`sum(paid debits) - sum(paid credits)`).
- [x] Unpaid expenses remain isolated in `totalAppUnpaidBalance`.
- [x] `index.vue` Total Spent KPI card accurately displays the net paid spent figure.
- [x] Visual copy and tooltips clearly indicate net paid outflows and refund deductions.

## Blocked by
None
