# Spec — Login Card Responsive Centering, System Logo & Input Icon Fixes

## Problem Statement

Users on desktop and mobile screens currently see imperfect vertical alignment or sizing on the login card page, and input boxes with icons (email, password, search, modal inputs) need crisp icon alignment, proper padding (`!pl-10` / `!pr-10`), and consistent system logo branding (`logo.png`).

## Solution

1. Update `auth.vue` layout to use flex centering (`min-h-screen flex flex-col items-center justify-center py-10 px-4 sm:px-6 lg:px-8`) for perfect vertical and horizontal centering across all viewports.
2. Include the system logo (`logo.png`) in the brand header above the card and inside the card header for clear corporate identity.
3. Update `login.vue` input fields using `UiInput` or refined `relative` icon containers with `!pl-10` / `!pr-10` so text never overlaps icons.
4. Ensure responsive card padding (`p-6 sm:p-10`) and max-width scaling (`w-full max-w-md sm:max-w-lg`).

## User Stories

1. As a corporate user, I want the login card to be vertically and horizontally centered on my screen, so that the authentication interface looks balanced and professional.
2. As a mobile user, I want the login card to adjust gracefully to smaller screens without horizontal overflow or clipped borders.
3. As a user typing my credentials, I want input field icons (email, password lock, eye toggle) to be cleanly aligned and separated from typed text.
4. As a user, I want to see the system logo (`logo.png`) clearly displayed on the login page.

## Implementation Decisions

- **Layout Centering (`auth.vue`)**: Use `min-h-screen flex flex-col items-center justify-center` with ambient background gradients.
- **Branding**: Render `~/assets/img/logo.png` inside the brand header with explicit width/height containers (`w-12 h-12`).
- **Input Icons**: Wrap inputs in `relative` containers with absolute positioned SVG icons (`top-1/2 -translate-y-1/2 left-3.5`), adding `!pl-10` and `!pr-10` on `.input-field`.
- **Responsive Sizing**: Use Tailwind breakpoints (`w-full max-w-md sm:max-w-lg p-6 sm:p-10`).

## Testing Decisions

- Build verification using `npm run build` in `src/frontend`.
- Visual alignment checks across desktop (1440px), tablet (768px), and mobile (375px) viewports.

## Out of Scope

- Modifying backend authentication logic or API endpoints.

---
