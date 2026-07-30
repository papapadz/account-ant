# 01 — Fix Desktop Login Card Responsive Centering

**What to build:**
Ensure the login card is perfectly centered vertically and horizontally across all desktop, tablet, and mobile viewports. The layout container in `auth.vue` and `login.vue` must enforce flex centering (`flex items-center justify-center min-h-screen w-full mx-auto my-auto`), removing any asymmetric margins or flex-start alignments so the login card sits squarely in the optical center of the screen on desktop displays.

**Blocked by:** None — can start immediately.

**Status:** completed

- [x] `auth.vue` container uses `w-full min-h-screen flex items-center justify-center p-4` with `mx-auto my-auto`.
- [x] `login.vue` card component centers cleanly without horizontal offset or margin skewing on 1024px, 1440px, and ultra-wide screens.
- [x] Card scales responsively on mobile (375px) and tablet (768px) screens.
- [x] Frontend build succeeds (`npm run build`).
