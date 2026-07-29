# 01 — Implement and Verify Analytics Helper Functions

**What to build:** Implement the missing database/transaction helper functions in the frontend composable (`useProjects.ts`) to aggregate project expenses by fund source and monthly project expense trends, then verify that the dashboard widgets display the charts correctly.

**Blocked by:** None — can start immediately

**Status:** ready-for-agent

- [ ] Add and export `getFundProjectExpenses(fundAccountId: number)` in [useProjects.ts](file:///c:/laragon/www/account-ant/src/frontend/app/composables/useProjects.ts)
- [ ] Add and export `getProjectMonthlyExpenses(projectId: number)` in [useProjects.ts](file:///c:/laragon/www/account-ant/src/frontend/app/composables/useProjects.ts)
- [ ] Verify that [FundExpensePieChart.vue](file:///c:/laragon/www/account-ant/src/frontend/app/components/dashboard/FundExpensePieChart.vue) displays correctly without console or compilation errors
- [ ] Verify that [ProjectMonthlyBarChart.vue](file:///c:/laragon/www/account-ant/src/frontend/app/components/dashboard/ProjectMonthlyBarChart.vue) displays correctly without console or compilation errors
