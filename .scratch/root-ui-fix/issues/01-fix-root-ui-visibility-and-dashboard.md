# 01 — Fix root / UI visibility and render projects dashboard

**What to build:** Fix the blank root route `/` issue by overriding `@ionic/vue` hidden `.ion-page` CSS styling when `@ionic/vue-router` is bypassed, resolving Vue component auto-imports, rendering the projects dashboard on `/`, and removing the redundant `src/frontend/pages/` directory.

**Blocked by:** None — can start immediately

**Status:** completed

- [x] Override `.ion-page` CSS in `app/assets/css/main.css` so layout and components inside `<IonPage>` render visibly on screen.
- [x] Ensure root route `/` (`app/pages/index.vue`) loads top greeting, KPI cards, visual analytics, projects data table, and creation modal without errors.
- [x] Delete redundant top-level `src/frontend/pages/` folder to align with `srcDir: 'app'` in `nuxt.config.ts`.
