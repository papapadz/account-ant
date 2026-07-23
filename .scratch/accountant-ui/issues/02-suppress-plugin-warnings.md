# 02 — Nuxt Dev Plugin Deprecation Warnings Suppression (`defineNuxtPlugin` Fix)

**What to build:** Ensure runtime plugins and dev-server configuration in `nuxt.config.ts` run cleanly under Nuxt 4 without non-critical `defineNuxtPlugin` deprecation logs interrupting the dev server.

**Blocked by:** 01 — Nuxt 4 Source Directory Restructuring (`WARN NUXT_E4014` Fix)

**Status:** ready-for-agent

- [x] Nuxt config configured with `compatibilityVersion: 4`
- [x] Dev server starts cleanly at `http://localhost:3000`
- [x] Production build completes with 0 errors
