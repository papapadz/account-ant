# 01 — Global Theme CSS Token Architecture & App Shell Adaptation

**What to build:** Establish semantic CSS custom properties (`--bg-app`, `--bg-surface`, `--bg-sidebar`, `--bg-card`, `--border-color`, `--text-main`, `--text-muted`, `--table-header-bg`) in `main.css`. Update `default.vue`, `auth.vue`, `AppSidebar.vue`, `AppHeader.vue`, and `Modal.vue` so the app layout and shell respond dynamically to light and dark theme toggling.

**Blocked by:** None — can start immediately.

**Status:** completed

- [x] Define global CSS tokens in `main.css` for `html.light` and `html.dark`
- [x] Refactor `AppSidebar.vue` to use theme variables
- [x] Refactor `AppHeader.vue` and `Modal.vue` to use theme variables
- [x] Refactor `default.vue` and `auth.vue` layouts
