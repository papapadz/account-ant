# 01 — Fix Horizontal Centering for Login Card

**What to build:**
Ensure the login card is perfectly centered horizontally across all viewport widths. Replace `w-screen` with `w-full` on `auth.vue` layout to prevent window scrollbar width offsets, enforce `fixed inset-0 w-full h-full flex items-center justify-center p-4 m-0`, and add `mx-auto` to the card wrapper in `login.vue`.

**Blocked by:** None — can start immediately.

**Status:** completed

- [x] `auth.vue` root replaces `w-screen` with `w-full h-full flex items-center justify-center m-0 p-4`.
- [x] `login.vue` root card uses `w-full max-w-md mx-auto my-auto`.
- [x] Card rests at the exact horizontal midpoint of the viewport without scrollbar offset or asymmetric margins.
- [x] Frontend build succeeds (`npm run build`).
