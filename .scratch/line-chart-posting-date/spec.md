# Feature Specification: Use `posting_date` in `FundSourceLineChart.vue`

## Problem Statement
In `FundSourceLineChart.vue`, transaction dates for available year calculations and monthly data aggregations use `t.date` instead of `t.posting_date || t.date`. To ensure visual analytics on the dashboard accurately reflect posting dates across fiscal years, the line chart component must strictly compute year lists and monthly totals using `posting_date`.

## Solution
1. Update `availableYears` computed property in `FundSourceLineChart.vue` to extract transaction years using `t.posting_date || t.date`.
2. Update `monthlyData` computed property to aggregate transactions into months (Jan–Dec) using `tx.posting_date || tx.date`.

## User Stories
1. As a financial analyst, I want the dashboard Fund Source Line Chart to reflect transactions by their actual posting date, so that fiscal year trends line up with accounting entries.

## Implementation Decisions
- **Component**: `src/frontend/app/components/dashboard/FundSourceLineChart.vue`.
- **Primary Field**: `t.posting_date || t.date`.

## Testing Decisions
- **Testing Seam**: Vue component state seam on `<DashboardFundSourceLineChart />`.
- **Verification**: Inspect year dropdown options and monthly trend calculations for transactions with custom posting dates.
