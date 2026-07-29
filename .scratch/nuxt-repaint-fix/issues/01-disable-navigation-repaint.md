# 01 — Disable experimental navigationRepaint in Nuxt config

**What to build:**
Disable the experimental `navigationRepaint` feature in Nuxt to prevent the app initialization crash on localhost:3000, allowing the Ionic-based UI to render successfully.

**Blocked by:** None — can start immediately.

**Status:** ready-for-agent

- [ ] Add `experimental: { navigationRepaint: false }` to `src/frontend/nuxt.config.ts`.
- [ ] Verify that `localhost:3000` loads the UI without `TypeError` console crashes.
