# 01 — Fix progress bar value display on Projects index page

**What to build:** Ensure the project budget utilization progress bar on the Projects index page correctly displays its progress fill width and value according to project data.

**Blocked by:** None — can start immediately

**Status:** completed

- [x] `UiProgressBar` supports both `percentage` and `value` props (with `value` acting as an alias for `percentage` if `percentage` is not explicitly provided).
- [x] `UiProgressBar` supports optional `variant` (`rose`, `amber`, `emerald`) and `size` (`sm`, `md`, `lg`) props without breaking existing usages.
- [x] Budget utilization progress bar in `projects/index.vue` accurately reflects budget percentage visual width.
