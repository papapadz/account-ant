# 01 — Fix [NUXT_E1005] error during app initialization

**What to build:** Fix the `[NUXT_E1005] Error caught during app initialization` by wrapping `ionic.client.ts` plugin execution and composable client-side initialization (`useTheme`, `useAuth`, etc.) in defensive try-catch guards.

**Blocked by:** None — can start immediately

**Status:** completed

- [x] Wrap `IonicVue` plugin installation and component loop in `app/plugins/ionic.client.ts` with error handling to prevent unhandled plugin initialization errors.
- [x] Add safe guards in composable initialization (`useTheme`, `useAuth`, etc.) for client-side storage and window state.
- [x] Verify `npm run dev` boots cleanly with zero `[NUXT_E1005]` initialization errors.
