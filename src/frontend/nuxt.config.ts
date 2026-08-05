import tailwindcss from '@tailwindcss/vite'

// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  srcDir: 'app',
  devtools: { enabled: true },

  // Ionic web-components are browser-only — disable SSR
  ssr: false,

  // NOTE: @nuxtjs/ionic is intentionally NOT used as a module here.
  // It pulls in @ionic/vue-router which depends on vue-router@4, conflicting
  // with Nuxt 4's vue-router@5. Instead, IonicVue is registered manually
  // via app/plugins/ionic.client.ts without the router integration.

  experimental: {
    navigationRepaint: false,
  },

  css: [
    '@ionic/vue/css/core.css',
    '@ionic/vue/css/normalize.css',
    '@ionic/vue/css/structure.css',
    '@ionic/vue/css/typography.css',
    '~/assets/css/main.css',
  ],

  vite: {
    plugins: [
      tailwindcss(),
    ],
    optimizeDeps: {
      include: ['@ionic/vue', '@ionic/core', 'ionicons'],
    },
  },

  app: {
    head: {
      title: 'AccountAnt - Automated Accounting Ledger',
      meta: [
        { name: 'description', content: 'Automated accounting ledger system for enterprise fund management, chart of accounts, and journal transactions.' },
        // viewport-fit=cover for notched iOS devices; initial-scale=1 prevents zoom
        { name: 'viewport', content: 'width=device-width, initial-scale=1, viewport-fit=cover' },
        // Capacitor / PWA meta
        { name: 'apple-mobile-web-app-capable', content: 'yes' },
        { name: 'apple-mobile-web-app-status-bar-style', content: 'black-translucent' },
        { name: 'mobile-web-app-capable', content: 'yes' },
        { name: 'theme-color', content: '#0B1120' },
      ],
      link: [
        { rel: 'preconnect', href: 'https://fonts.googleapis.com' },
        { rel: 'preconnect', href: 'https://fonts.gstatic.com', crossorigin: '' },
        { rel: 'stylesheet', href: 'https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=JetBrains+Mono:wght@400;500;600&display=swap' },
      ],
    },
  },

  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://localhost:8000/api',
    },
  },
})
