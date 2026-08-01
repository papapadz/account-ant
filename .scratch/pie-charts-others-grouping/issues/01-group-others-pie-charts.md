# 01 — Group Donut Slices Beyond Top 5 into "Others" and Refresh Colors

**What to build:** Group all pie/donut chart slices beyond the top 5 largest items into a single "Others" slice in both `LedgerExpensePieChart.vue` and `FundExpensePieChart.vue`. This prevents the charts from showing tiny, unreadable pieces. In addition, refresh the colors with a curated, accessible color palette.

**Blocked by:** None — can start immediately.

**Status:** completed

- [x] Group all items beyond the top 5 largest slices into a single "Others" slice for the donut chart.
- [x] The "Others" slice must sum the totals of all grouped items.
- [x] The legend list must show the top 5 items individually, followed by an "Others" item.
- [x] The "Others" slice and legend marker must be colored with a neutral slate color (e.g., `#64748B`).
- [x] Apply a refreshed, WCAG-compliant color palette from the design system recommendations to the top 5 slices.
- [x] Ensure the legend marker colors match the chart slice colors exactly.
