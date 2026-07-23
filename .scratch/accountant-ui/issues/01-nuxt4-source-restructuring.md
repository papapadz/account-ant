# 01 — Nuxt 4 Source Directory Restructuring (`WARN NUXT_E4014` Fix)

**What to build:** Move and consolidate all Nuxt pages (`pages/`), layouts (`layouts/`), composables (`composables/`), and components (`components/`) inside the `src/frontend/app/` directory so Nuxt 4 source directory resolution (`srcDir: 'app'`) detects all page routes (`/`, `/login`, `/register`, `/management/*`, `/settings`) cleanly at `http://localhost:3000/`.

**Blocked by:** None — can start immediately.

**Status:** ready-for-agent

- [x] All page components reside in `src/frontend/app/pages/`
- [x] Layouts reside in `src/frontend/app/layouts/`
- [x] Composables reside in `src/frontend/app/composables/`
- [x] Components reside in `src/frontend/app/components/`
- [x] `npx nuxt prepare` & `npm run build` detect all routes with zero `NUXT_E4014` warnings
