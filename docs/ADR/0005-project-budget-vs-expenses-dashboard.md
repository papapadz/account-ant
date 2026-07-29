# ADR 0005: Project Budget Entry & Dashboard Budget vs Expenses Visualization

## Status
Proposed / Prototyping

## Context
Projects in AccountAnt require mandatory initial budget definitions upon creation, as well as real-time tracking of budget vs net expenses (debits - credits) on project dashboards.

## Decision Drivers
1. **Clear Budget Capture**: Users must specify the project budget when creating a project record.
2. **Visual Hierarchy & Ergonomics**: Executives and accountants need clear visual feedback on how much of a project's budget has been consumed by posted journal entries.
3. **Over-Budget Warning**: Proactively alert users when net expenses exceed allocated budget.

## Prototyped UI Options

### Variant A: Modern KPI Grid & Progress Meter
- Compact grid of project cards.
- Displays total budget, net expenses, remaining balance, and a single progress bar indicator.
- Ideal for dense overviews with standard metrics.

### Variant B: Dual Split Comparison Gauge & Burn-Rate Analytics
- Split side-by-side financial breakdown per project.
- Visual side-by-side comparison between Allocated Budget vs Net Expenses, featuring a budget utilization badge and surplus meter.
- Best for detailed financial reporting and tracking spend velocity.

### Variant C: Executive Financial Matrix & Interactive Budget Action Bar
- Tabular list view card with rich sector tagging (Government vs Private).
- Shows currency metrics inline with status indicators and direct navigation actions.
- Optimal for executive summaries and multi-project filtering.

## Data Model Integration
- Model: `App\Models\Accounting\Project`
- Column: `budget` (`decimal:2`)
- Computed Attributes:
  - `total_expenses`: Total Debits - Total Credits
  - `running_balance`: Total Allocated Funds - Total Expenses
  - `budget_utilized_percentage`: (`total_expenses` / `budget`) * 100
  - `is_over_budget`: `total_expenses` > `budget`
