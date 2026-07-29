---
name: nuxt-ionic-custom-ui
description: Build mobile app UI in Nuxt + Ionic (@ionic/vue) projects using hand-built Vue 3 components styled with Tailwind CSS instead of default Ionic components. Use this skill whenever the user is working in a Nuxt/Ionic/Capacitor app and asks to build, style, or fix a button, card, input, form, modal, tab bar, list, toggle, badge, or any other UI piece — even if they don't say "custom" or "Tailwind" explicitly. Also use it when the user complains that their app "looks too much like default Ionic," asks how to theme an Ionic app, asks to replace ion-button/ion-card/ion-input/ion-item/etc. with something custom, or wants a component library / design system set up on top of Ionic. Covers which Ionic primitives to keep for native behavior (IonPage, IonContent, IonRouterOutlet, overlay controllers) versus which visual components to rebuild from scratch, plus Tailwind/Ionic CSS integration so utility classes actually win.
---

# Nuxt + Ionic + Vue + Tailwind: Custom Component UI

## Why this skill exists

Ionic ships a full set of pre-styled components (`ion-button`, `ion-card`, `ion-input`, `ion-item`, `ion-toggle`, etc.). They work, but they carry Ionic's visual identity (Material/iOS mode switching, Ionic's shadow DOM styling, Ionic's CSS variables) which is hard to fully override and tends to make every app look like every other Ionic app. The goal here is a hybrid: **keep Ionic for the things it's genuinely hard to reimplement** (native-feeling page transitions, gestures, safe-area handling, overlay portals, routing), and **hand-build everything visual** as plain Vue 3 SFCs styled with Tailwind, so the app has its own design language.

Don't reach for an `ion-*` visual component out of habit. When the user asks for "a button" or "a card," the default should be a custom component in `components/ui/`, not `<ion-button>`.

## Decision rule: keep Ionic, or build custom?

| Keep as Ionic (structural / native behavior) | Rebuild custom with Tailwind |
|---|---|
| `IonApp`, `IonRouterOutlet`, `IonPage` | `IonButton` → `<Button>` |
| `IonHeader` / `IonContent` (scroll physics, pull-to-refresh, safe-area) | `IonCard` → `<Card>` |
| `IonModal`, `IonActionSheet`, `IonAlert`, `IonToast` **as portal/animation engines only** — style their inner content, don't use their default look | `IonInput`, `IonTextarea`, `IonSelect` → native `<input>`/`<textarea>`/custom `<Select>` |
| `IonRefresher` (pull-to-refresh physics) | `IonItem`, `IonList` → `<ListItem>`, `<List>` |
| `IonTabs` gesture/routing logic if the app relies on swipe-between-tabs | `IonTabBar`/`IonTabButton` → `<TabBar>` with custom markup |
| `useBackButton`, `useIonRouter`, platform detection (`isPlatform`), Capacitor plugins (haptics, status bar, keyboard) | `IonToggle`, `IonCheckbox`, `IonRadio`, `IonBadge`, `IonChip`, `IonAvatar`, `IonProgressBar`, `IonSkeletonText`, `IonFab`, `IonSpinner` → all custom |
| `ion-icon` for icon glyphs (it's just an SVG sprite, not a styled component) | keep as-is, style with Tailwind text/size utilities |

Read `references/ionic-retained.md` for the reasoning behind each row and code for wrapping overlay controllers (`useModal`-style composables) with custom content.

The rule of thumb: **if the Ionic component's job is animation/gesture/native-platform physics, keep it and only style its slot content. If its job is visual presentation of a form control or piece of content, rebuild it.**

## Setting up Tailwind to actually win against Ionic's CSS

Ionic injects its own base stylesheets with fairly high specificity (shadow-DOM-scoped parts, `:host` rules). Without care, Tailwind utility classes on Ionic elements get silently overridden. Read `references/setup.md` before touching `nuxt.config.ts` or `tailwind.config.ts` — it covers:
- CSS import order (Ionic base CSS must load *before* Tailwind's utilities layer)
- Disabling Ionic's default color/typography CSS variables so custom components don't inherit them
- Nuxt module setup for `@ionic/vue` + `@nuxtjs/tailwindcss`
- Mapping design tokens (colors, radii, spacing) into `tailwind.config.ts` instead of Ionic's `--ion-color-*` variables

## Building a custom component

Every custom UI component follows the same shape. Put them in `components/ui/` (Nuxt auto-imports from there, so no manual registration needed).

1. **Vue 3 `<script setup lang="ts">`** with a typed `defineProps` (this was confirmed as the project's convention — TypeScript throughout).
2. **Variant props** (`variant`, `size`, etc.) mapped to Tailwind class strings via a plain lookup object or a small `cva`-style helper — don't sprawl ternaries inline in the template.
3. **Native semantic elements underneath**: a custom `<Checkbox>` renders a real `<input type="checkbox">` (visually hidden or styled directly), a custom `<Button>` renders a real `<button>`. This preserves accessibility, keyboard behavior, and form semantics that Ionic's components handle for you and that a `<div onClick>` does not.
4. **Respect safe-area and platform state where relevant** — use `env(safe-area-inset-*)` via Tailwind arbitrary values (`pb-[env(safe-area-inset-bottom)]`) for anything pinned to a screen edge, and `isPlatform('ios' | 'android')` from `@ionic/vue` when a component genuinely needs platform-specific behavior (e.g., haptic feedback timing, back-gesture affordance). Don't add platform branching for looks alone — the whole point of this design system is a consistent custom look across platforms.
5. **Slots over prop-explosion** for content composition (e.g., `<Card>` takes a default slot plus optional `#header`/`#footer` slots, rather than a dozen content props).

Read `references/components.md` for full working examples: `Button.vue`, `Card.vue`, `Input.vue`, `ListItem.vue`, `Toggle.vue`, `TabBar.vue`, and a modal-content wrapper pattern that pairs a custom-styled inner component with Ionic's `IonModal`/`modalController` for the actual presentation animation.

## When generating a new component, always check the project first

Before writing a new custom component, look for `components/ui/` in the project — if `Button.vue` or similar already exists, match its existing prop naming, variant names, and class-composition style rather than introducing a second pattern. Consistency across the design system matters more than any single component being "ideal." If nothing exists yet, use the patterns in `references/components.md` as the starting convention and mention to the user that this establishes the pattern for future components.

## Defaults when no design system is specified

If the user hasn't given brand colors, spacing scale, or a component look, don't ask before proceeding — pick sensible modern mobile-app defaults (rounded-xl corners, a neutral gray/slate palette with one accent color, generous touch targets ≥44px, subtle shadows over hard borders, system font stack) and say briefly what you chose so it's easy to redirect. This matches common modern mobile UI conventions and gives the user something concrete to react to rather than a blank page.
