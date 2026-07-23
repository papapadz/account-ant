# 01 — Client-Side State Hydration Synchronization (`<ClientOnly>` Wrappers)

**What to build:** Wrap SSR-differing reactive elements (net balance pill, journal count badge, initial dynamic state) in `<ClientOnly>` tags across `AppHeader.vue`, `AppSidebar.vue`, `KpiCard.vue`, and pages (`index.vue`, `journal.vue`, `settings.vue`) so SSR HTML matches client DOM hydration without node mismatch warnings.

**Blocked by:** None — can start immediately.

**Status:** completed

- [x] Wrap net balance pill in `AppHeader.vue` with `<ClientOnly>`
- [x] Wrap count badges in `AppSidebar.vue` with `<ClientOnly>`
- [x] Wrap KPI card dynamic values with `<ClientOnly>` fallback stubs
