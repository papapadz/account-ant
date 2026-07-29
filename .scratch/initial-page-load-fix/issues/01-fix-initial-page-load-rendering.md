# 01 — Fix initial page load rendering by replacing IonPage in layouts

**What to build:** Fix pages not rendering on initial load by replacing `<IonPage>` in `app/layouts/default.vue` and `app/layouts/auth.vue` with standard container elements (`<div id="main-content" class="ion-page">`), removing the missing `IonRouterOutlet` lifecycle event dependency.

**Blocked by:** None — can start immediately

**Status:** completed

- [x] Replace `<IonPage id="main-content">` with `<div id="main-content" class="ion-page flex flex-col w-full h-full">` in `app/layouts/default.vue`.
- [x] Replace `<IonPage>` with `<div class="ion-page flex flex-col w-full h-full min-h-screen">` in `app/layouts/auth.vue`.
- [x] Verify initial load at `/`, `/login`, `/register`, `/settings`, and `/management/*` renders page content instantly without blank screens.
