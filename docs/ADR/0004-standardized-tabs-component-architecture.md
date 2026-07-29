# ADR 0004: Standardized Tabs Component Architecture

## Status
Accepted

## Context
The AccountAnt frontend application features tabbed navigation across multiple screens (Dashboard status filters, Project detail tabs, Journal type filters, Settings section tabs, and Mobile bottom navigation).
Previously, tabs were implemented via ad-hoc button groups with inline conditional Tailwind classes. This created several issues:
1. **Visual Inconsistency**: Tab padding, active indicators, hover states, and font sizes varied across pages.
2. **Mobile Overflow & Scrolling**: On smaller mobile screens (e.g. 375px), wide tab bars overflowed awkwardly or wrapped onto multiple lines.
3. **Accessibility & Keyboard Navigation**: Ad-hoc button groups lacked keyboard focus management (`ArrowLeft`/`ArrowRight`), ARIA role attributes (`role="tablist"`, `role="tab"`), and reactive `v-model` binding.

Ionic Vue provides `<ion-segment>`, but as established in [ADR 0003](file:///c:/laragon/www/account-ant/docs/ADR/0003-standardized-button-component-architecture.md) and `nuxt-ionic-custom-ui`, Ionic visual controls carry Shadow DOM rules and Material/iOS mode switching that collide with AccountAnt's custom dark/light theme tokens.

## Decision
We decide to implement the **Standardized Tabs Component (`Tabs.vue`)** in `src/frontend/app/components/ui/Tabs.vue` as a custom Vue 3 SFC:
1. **Semantic & ARIA Foundation**: Render an accessible `role="tablist"` container with `role="tab"` items, supporting keyboard navigation (`ArrowLeft`, `ArrowRight`, `Home`, `End`).
2. **Three Visual Variants**:
   - `segmented`: Dark background track with elevated active pill, ideal for status and type filters.
   - `pills`: Floating button pills with active primary fill, ideal for view switchers.
   - `underline`: Clean border-bottom active accent line, ideal for multi-section detail pages (`settings.vue`, `project/[id].vue`).
3. **Horizontal Panning**: Scroll container uses `.no-scrollbar` with smooth touch panning (`overflow-x-auto`) to guarantee zero layout breakage on narrow mobile screens.
4. **Badge Integration**: Support scannable count badges (e.g. live transaction counts, item numbers).
5. **Responsive Bottom Nav**: Restrict mobile bottom navigation in `default.vue` to `sm:hidden`, avoiding UI clutter on desktop.

## Consequences
### Positive
- Unified tab visual identity across the entire application.
- 100% responsive across mobile, tablet, and desktop viewports with zero horizontal scrollbar clutter.
- Accessible keyboard navigation and proper ARIA semantics.

### Negative
- Requires passing an array of tab items (`TabItem[]`) or slots to `<Tabs>`.
