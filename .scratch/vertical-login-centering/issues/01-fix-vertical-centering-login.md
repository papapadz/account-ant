# 01 — Fix Vertical Centering for Login Card

**What to build:**
Fix vertical centering on the login page by overriding Ionic's global `.ion-page` `justify-content: space-between` layout rule and using `flex flex-col items-center justify-center min-h-screen h-full w-full my-auto shrink-0` on `auth.vue` and `login.vue`. The login card will sit at the exact vertical midpoint of the screen on all device heights.

**Blocked by:** None — can start immediately.

**Status:** completed

- [x] `.ion-page` in `main.css` allows flex vertical centering (`justify-content: center`).
- [x] `auth.vue` container enforces `flex flex-col items-center justify-center h-full min-h-screen my-auto`.
- [x] `login.vue` card component has `my-auto shrink-0` so it rests in the exact vertical midpoint of the screen.
- [x] Frontend build succeeds (`npm run build`).
