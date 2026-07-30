# 01 — Dead-Center Responsive Login Card Layout

**What to build:**
Lock the authentication layout (`auth.vue`) to the exact dead center of the viewport using `fixed inset-0 w-screen h-screen grid place-items-center p-4`. This overrides any parent container vertical offsets, `min-h-screen` viewport scrollbar gaps, or Ionic `.ion-page` `space-between` CSS rules, placing the login card at the absolute mathematical dead center of the screen on all device viewports (mobile, tablet, desktop, ultra-wide).

**Blocked by:** None — can start immediately.

**Status:** completed

- [x] `auth.vue` root uses `fixed inset-0 w-screen h-screen grid place-items-center p-4 z-50 overflow-y-auto`.
- [x] Login card sits at the absolute dead center of the screen on 375px mobile, 768px tablet, 1080p, 1440p, and 4K screens.
- [x] Short viewport screens (landscape mobile) support vertical scrolling without clipping or breaking dead-center alignment when tall.
- [x] Frontend build succeeds (`npm run build`).
