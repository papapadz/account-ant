# 01 — Fix Nuxt 3 Dynamic Project Routing & Workbench Page

**What to build:** Fix the Nuxt 3 dynamic file routing by naming the project detail route `pages/management/projects/[id].vue` instead of Nuxt 2 style `_id_.vue`. This enables users to seamlessly navigate to `/management/projects/:id` to manage project-specific fund allocations and ledger transactions.

**Blocked by:** None — can start immediately

**Status:** ready-for-agent

- [x] Create Nuxt 3 compliant `pages/management/projects/[id].vue` route file
- [x] Verify project parameter extraction (`useRoute().params.id`) and real-time state metrics rendering
- [x] Test navigation from Projects List table to individual project workbench
