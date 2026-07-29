# ADR 0003: Standardized Button Component Architecture

## Status
Accepted

## Context
The AccountAnt frontend application requires a unified button component system across all management screens, project pages, authentication forms, and modal dialogs.
Ionic Vue provides `<ion-button>`, which encapsulates Shadow DOM rendering, iOS/Material Design themes, and Ionic-specific CSS variables (`--background`, `--color`). However, in our Nuxt + Tailwind CSS architecture (`nuxt-ionic-custom-ui`), raw Ionic visual components present several issues:
1. **Shadow DOM Encapsulation**: Utility classes like `hover:bg-emerald-600`, `shadow-md`, and custom padding on `<ion-button>` get silently ignored or overridden by Ionic's internal `:host` rules.
2. **Design Language Inconsistency**: Ionic default buttons switch between iOS and Material design identities, colliding with AccountAnt's dark/light financial ledger aesthetic (`--bg-surface`, `--color-primary`, `--border-color`).
3. **Form Integration**: Native HTML form validation and submission behavior (`<form @submit.prevent>`) work most reliably with native semantic `<button type="submit">`.

## Decision
We decide to implement the **Standardized Button Component (`Button.vue`)** in `src/frontend/app/components/ui/Button.vue` as a custom Vue 3 SFC using native HTML `<button>` styled with Tailwind CSS and CSS custom properties:
1. **Semantic Foundation**: Render a native HTML `<button>` tag with full support for `type`, `disabled`, `aria-*`, and `@click` listeners.
2. **Visual Hierarchy**: Standardize on 5 variants (`primary`, `secondary`, `outline`, `danger`, `ghost`) and 3 sizes (`sm`, `md`, `lg`).
3. **Interaction & Micro-animations**: Provide smooth hover/active scaling (`active:scale-[0.98]`), subtle focus rings, and an embedded loading spinner when `loading` is active.
4. **Ionic Compatibility**: Keep Ionic for page/routing and modal portals (`IonPage`, `IonContent`, `IonModal`), while keeping visual form controls like `<Button>` as custom Vue components.

## Consequences
### Positive
- 100% control over button typography, spacing, colors, and dark/light mode transitions without fighting Shadow DOM.
- Seamless compatibility with Tailwind CSS utility classes and design tokens in `main.css`.
- Clean prop-based interface for developers across all pages and components.

### Negative
- Does not automatically include Ionic's Material Design touch ripple unless explicitly implemented via CSS active transitions or micro-animations.
