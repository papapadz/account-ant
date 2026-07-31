# 0019. Disable Already Selected Fund Sources in Project Modal

## Status
Accepted

## Context
When adding a fund source to a specific project in `project/[id].vue`, users could see all fund accounts in the dropdown. Selecting a fund source that has already been added to the project could cause duplicate allocations or confusion.

## Decision
1. **Disable Selected Options**: When rendering available fund accounts in the "Add Project Fund Source" modal dropdowns (across all variant views), options corresponding to fund accounts already associated with the active project are marked `:disabled="isFundAlreadySelected(fund.id)"`.
2. **Visual Label Feedback**: Appended `(Already added to project)` to disabled options in the select list to clearly inform users why the option cannot be selected.
3. **Exact ID Matching**: Matching between project fund sources and available fund accounts is evaluated strictly by exact account ID (`pf.id === fund.id || pf.fund_account_id === fund.id`).

## Consequences
- Prevents accidental duplicate linking of fund sources to the same project.
- Provides clear visual feedback in the UI regarding existing project fund allocations.
