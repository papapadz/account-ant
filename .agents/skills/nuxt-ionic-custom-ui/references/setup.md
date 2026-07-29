# Tailwind + Ionic setup for Nuxt

## Why order matters

`@ionic/vue` ships CSS (`@ionic/vue/css/core.css` plus optional normalize/structure/typography/palettes files) that sets base styles on real HTML elements and defines a large set of `--ion-*` CSS custom properties. Tailwind's `@tailwind utilities` layer needs to load **after** Ionic's CSS in the cascade, or Ionic's element-level rules (e.g. default button padding/border from `ion-button`'s host styles) can beat Tailwind classes of equal specificity depending on load order.

## nuxt.config.ts

```ts
export default defineNuxtConfig({
  modules: [
    '@nuxtjs/tailwindcss',
  ],
  css: [
    // Ionic core first
    '@ionic/vue/css/core.css',
    '@ionic/vue/css/normalize.css',
    '@ionic/vue/css/structure.css',
    '@ionic/vue/css/typography.css',
    // Only pull in the palette/utility files you actually still rely on.
    // Skip @ionic/vue/css/palettes/dark.system.css etc. if your custom
    // components own dark mode via Tailwind's `dark:` variant instead.

    // App-level Tailwind entry LAST so utilities win
    '~/assets/css/tailwind.css',
  ],
  app: {
    head: {
      viewport: 'width=device-width, initial-scale=1, viewport-fit=cover',
    },
  },
})
```

`viewport-fit=cover` is required for `env(safe-area-inset-*)` to resolve to real values on iOS instead of `0`.

## assets/css/tailwind.css

```css
@tailwind base;
@tailwind components;
@tailwind utilities;

/* Neutralize Ionic's color system so custom components don't
   accidentally inherit ion-color-* driven styles from ancestors. */
:root {
  --ion-color-primary: theme('colors.brand.500');
  /* Prefer setting this to your Tailwind accent color rather than leaving
     Ionic's default blue — anything still using Ionic's color system
     (e.g. a kept IonSpinner or IonRefresher) will then match your palette. */
}
```

## tailwind.config.ts — own your design tokens here, not in Ionic variables

```ts
import type { Config } from 'tailwindcss'

export default <Config>{
  content: [
    './components/**/*.{vue,js,ts}',
    './layouts/**/*.vue',
    './pages/**/*.vue',
    './app.vue',
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          50: '#eef2ff',
          500: '#6366f1',
          600: '#4f46e5',
          700: '#4338ca',
        },
      },
      borderRadius: {
        xl: '1rem',
        '2xl': '1.25rem',
      },
      spacing: {
        'safe-t': 'env(safe-area-inset-top)',
        'safe-b': 'env(safe-area-inset-bottom)',
      },
    },
  },
  plugins: [],
}
```

Using `spacing.safe-t` / `safe-b` lets you write `pt-safe-t` / `pb-safe-b` instead of repeating the arbitrary-value syntax everywhere.

## Component-level override, if needed

If a specific kept Ionic element (e.g. `IonContent` or `IonRefresher`) still shows unwanted default styling that Tailwind classes on the element aren't beating, use Ionic's documented CSS shadow parts (`::part(...)`) or its own CSS variables scoped to that element, rather than fighting specificity with `!important`. Ionic elements are shadow-DOM components — a plain Tailwind class on the host element can't reach into their internals, only `::part()` and Ionic's CSS variables can.
