# 01 — Fix Vue invalid vnode and Suspense single-root warnings

**What to build:** Fix the Vue console warnings `Invalid vnode type when creating vnode: undefined` and `<Suspense> slots expect a single root node` by filtering out undefined component registrations in `ionic.client.ts` and cleaning top-level comment/node structure in layout and page templates.

**Blocked by:** None — can start immediately

**Status:** completed

- [x] Guard component registration in `app/plugins/ionic.client.ts` with `if (component)` and remove invalid imports so `undefined` components are never registered into Vue.
- [x] Ensure layout templates (`app/layouts/default.vue`, `app/layouts/auth.vue`) pass clean single-root DOM nodes to Nuxt's `<Suspense>` wrapper without top-level comment nodes.
- [x] Verify dev server logs and browser console remain 100% free of Vue runtime warnings.
