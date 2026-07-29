# ADR 0009: Ionic Core Web Component Hydration & App Visibility

## Status
Accepted

## Context
The AccountAnt frontend uses Nuxt 4 with `@ionic/vue` registered manually in `app/plugins/ionic.client.ts` to prevent conflicts between `@nuxtjs/ionic`'s embedded `vue-router@4` and Nuxt 4's `vue-router@5`.

By default, `@ionic/core` Web Components hide `<ion-app>` via CSS (`visibility: hidden`) until Stencil completes custom element hydration and applies the `.hydrated` class. Because `@nuxtjs/ionic` was bypassed, `defineCustomElements(window)` from `@ionic/core/loader` was never invoked on client startup, causing `<ion-app>` to remain hidden (`visibility: hidden`), resulting in a blank screen across the entire application on initial load.

## Decision
1. **Explicit Custom Element Hydration**: Call `defineCustomElements(window)` from `@ionic/core/loader` inside `app/plugins/ionic.client.ts` upon client plugin execution.
2. **CSS App Visibility Overrides**: Add explicit CSS overrides for `ion-app` and `.ion-page` in `app/assets/css/main.css` (`visibility: visible !important; opacity: 1 !important; display: flex !important;`) to guarantee layout visibility even prior to Stencil custom element hydration.
3. **Layout Container Alignment**: Replace `<IonPage>` in `app/layouts/default.vue` and `app/layouts/auth.vue` with `<div id="main-content" class="ion-page">` to eliminate reliance on Ionic Vue Router transition events while maintaining full drawer menu and layout support.

## Consequences
- **Positive**: Initial page load on root `/` and all routes renders UI elements immediately without blank screens.
- **Positive**: Standard Vue Router (vue-router v5) handles all navigation without router instance conflicts.
- **Negative**: Manual component loader execution must be maintained when updating Ionic dependencies.
