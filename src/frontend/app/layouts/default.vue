<template>
  <div v-if="!showNavigation" class="w-full h-full min-h-screen bg-[var(--bg-app)] text-[var(--text-main)] font-sans antialiased">
    <slot />
  </div>

  <div v-else class="flex h-screen w-screen overflow-hidden bg-[var(--bg-app)] text-[var(--text-main)] font-sans antialiased">
    <!-- Mobile Menu Backdrop -->
    <div
      v-if="isMobileMenuOpen"
      class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-40 lg:hidden transition-opacity"
      @click="isMobileMenuOpen = false"
    />

    <!-- ─── Side Navigation Drawer (Fixed Desktop / Overlay Mobile) ────── -->
    <aside
      class="fixed lg:static inset-y-0 left-0 z-50 w-72 bg-[var(--bg-sidebar)] border-r border-[var(--border-color)] flex flex-col justify-between transition-transform duration-300 ease-in-out lg:translate-x-0"
      :class="isMobileMenuOpen ? 'translate-x-0 shadow-2xl' : '-translate-x-full'"
    >
      <!-- Top Brand Header -->
      <div>
        <div class="flex items-center justify-between px-5 py-4 border-b border-[var(--border-color)]">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl overflow-hidden shrink-0 shadow-lg shadow-emerald-500/20 border border-[var(--border-color)] bg-[var(--bg-surface)] flex items-center justify-center p-1">
              <img src="~/assets/img/logo.png" alt="AccountAnt Logo" class="w-full h-full object-contain" />
            </div>
            <div>
              <h1 class="font-bold text-[var(--text-main)] tracking-tight text-base flex items-center gap-1.5 leading-tight">
                AccountAnt
                <span class="text-[10px] font-semibold bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 px-1.5 py-0.5 rounded-full uppercase tracking-wider">v1.0</span>
              </h1>
              <p class="text-[11px] text-[var(--text-muted)]">Automated Ledger System</p>
            </div>
          </div>
          <!-- Close Mobile Menu Button -->
          <button
            class="p-1 rounded-lg text-[var(--text-muted)] hover:text-[var(--text-main)] lg:hidden"
            @click="isMobileMenuOpen = false"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Company Scope Badge -->
        <div class="p-4 border-b border-[var(--border-color)]">
          <ClientOnly>
            <div class="bg-[var(--bg-surface)] border border-[var(--border-color)] rounded-lg p-2.5 flex items-center gap-2.5">
              <div class="w-7 h-7 rounded bg-blue-500/10 border border-blue-500/20 text-blue-500 flex items-center justify-center font-bold text-xs shrink-0">
                {{ companyInitials }}
              </div>
              <div class="truncate">
                <div class="text-xs font-semibold text-[var(--text-main)] truncate">{{ companyName }}</div>
                <div class="text-[10px] text-[var(--text-muted)] capitalize">{{ companyScope }} Scope</div>
              </div>
            </div>
            <template #fallback>
              <div class="bg-[var(--bg-surface)] border border-[var(--border-color)] rounded-lg p-2.5 opacity-50">
                <div class="text-xs text-[var(--text-muted)]">Loading...</div>
              </div>
            </template>
          </ClientOnly>
        </div>

        <!-- Main Navigation Links -->
        <nav class="p-3 space-y-0.5 overflow-y-auto max-h-[calc(100vh-220px)]">
          <div class="px-3 py-2 text-[10px] font-bold text-[var(--text-muted)] uppercase tracking-wider">Main Ledger</div>

          <NuxtLink
            v-for="link in mainLinks"
            :key="link.to"
            :to="link.to"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-medium transition-colors cursor-pointer"
            :class="route.path === link.to
              ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 font-semibold'
              : 'text-[var(--text-muted)] hover:text-[var(--text-main)] hover:bg-[var(--bg-surface)]'"
            @click="isMobileMenuOpen = false"
          >
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="link.icon" />
            </svg>
            {{ link.label }}
          </NuxtLink>

          <div class="px-3 pt-4 pb-2 text-[10px] font-bold text-[var(--text-muted)] uppercase tracking-wider">Management &amp; Chart</div>

          <NuxtLink
            v-for="link in managementLinks"
            :key="link.to"
            :to="link.to"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-medium transition-colors cursor-pointer"
            :class="route.path === link.to
              ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 font-semibold'
              : 'text-[var(--text-muted)] hover:text-[var(--text-main)] hover:bg-[var(--bg-surface)]'"
            @click="isMobileMenuOpen = false"
          >
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="link.icon" />
            </svg>
            {{ link.label }}
          </NuxtLink>

          <div class="px-3 pt-4 pb-2 text-[10px] font-bold text-[var(--text-muted)] uppercase tracking-wider">System</div>

          <NuxtLink
            to="/settings"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-medium transition-colors cursor-pointer"
            :class="route.path === '/settings'
              ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 font-semibold'
              : 'text-[var(--text-muted)] hover:text-[var(--text-main)] hover:bg-[var(--bg-surface)]'"
            @click="isMobileMenuOpen = false"
          >
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Settings &amp; Profile
          </NuxtLink>
        </nav>
      </div>

      <!-- User Account Footer -->
      <div class="p-3 border-t border-[var(--border-color)] bg-[var(--bg-sidebar)]">
        <ClientOnly>
          <div class="flex items-center justify-between p-2 rounded-lg bg-[var(--bg-surface)] border border-[var(--border-color)]">
            <div class="flex items-center gap-2.5 overflow-hidden">
              <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-400 to-blue-500 flex items-center justify-center text-slate-950 font-bold text-xs shrink-0">
                {{ userInitials }}
              </div>
              <div class="truncate">
                <div class="text-xs font-medium text-[var(--text-main)] truncate">{{ userName }}</div>
                <div class="text-[10px] text-[var(--text-muted)] truncate">{{ userTitle }}</div>
              </div>
            </div>
            <UiButton
              variant="ghost"
              size="sm"
              class="!p-1.5 text-[var(--text-muted)] hover:text-rose-400"
              title="Sign Out"
              @click="auth.logout()"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
              </svg>
            </UiButton>
          </div>
          <template #fallback>
            <div class="h-10 rounded-lg bg-[var(--bg-surface)] border border-[var(--border-color)] opacity-50" />
          </template>
        </ClientOnly>
      </div>
    </aside>

    <!-- ─── Main Content Container ────────────────────────────────────── -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
      <!-- Top Header Toolbar -->
      <header class="h-14 bg-[var(--bg-sidebar)] border-b border-[var(--border-color)] flex items-center justify-between px-4 shrink-0">
        <div class="flex items-center gap-3">
          <button
            class="p-1.5 rounded-lg text-[var(--text-muted)] hover:text-[var(--text-main)] hover:bg-[var(--bg-surface)] lg:hidden"
            title="Open Menu"
            @click="isMobileMenuOpen = true"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>

          <div class="flex flex-col items-start leading-tight">
            <span class="text-[10px] text-[var(--text-muted)] font-medium tracking-wide uppercase">{{ currentPageGroup }}</span>
            <span class="text-sm font-bold text-[var(--text-main)] truncate">{{ pageTitle }}</span>
          </div>
        </div>

        <!-- Right Side Actions -->
        <div class="flex items-center gap-2">
          <ClientOnly>
            <div class="hidden sm:flex items-center gap-1.5 bg-[var(--bg-surface)] border border-[var(--border-color)] px-2.5 py-1 rounded-full text-xs">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse shrink-0" />
              <span class="font-mono font-bold text-emerald-400">{{ currencyStore.formatCurrency(netBalance) }}</span>
            </div>
          </ClientOnly>

          <ClientOnly>
            <UiButton
              variant="secondary"
              size="sm"
              class="!py-1.5 !px-2"
              :title="isDarkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
              @click="theme.toggleTheme()"
            >
              <svg v-if="isDarkMode" class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
              <svg v-else class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
              </svg>
            </UiButton>
          </ClientOnly>

          <NuxtLink
            to="/management/journal"
            class="btn-primary !py-1.5 !px-2.5 !text-xs"
          >
            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
            <span class="hidden sm:inline">Post</span>
          </NuxtLink>
        </div>
      </header>

      <!-- Scrollable Main Viewport -->
      <main class="flex-1 overflow-y-auto p-4 sm:p-6 pb-20 sm:pb-6">
        <slot />
      </main>

      <!-- Bottom Navigation Bar for Mobile -->
      <footer class="border-t border-[var(--border-color)] bg-[var(--bg-sidebar)]/90 backdrop-blur-xl sm:hidden shrink-0">
        <nav class="flex items-center justify-around h-14 px-2 max-w-md mx-auto">
          <NuxtLink
            v-for="tab in tabs"
            :key="tab.href"
            :to="tab.href"
            class="relative flex flex-col items-center justify-center flex-1 h-full py-1 gap-1 text-[11px] font-medium transition-all cursor-pointer select-none active:scale-95"
            :class="isTabActive(tab.href)
              ? 'text-emerald-400 font-bold'
              : 'text-[var(--text-muted)] hover:text-[var(--text-main)]'"
          >
            <span
              v-if="isTabActive(tab.href)"
              class="absolute top-1 w-8 h-7 bg-emerald-500/10 rounded-full border border-emerald-500/20 -z-0"
            />
            <svg class="w-5 h-5 shrink-0 z-10 transition-transform" :class="isTabActive(tab.href) ? 'scale-110' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="tab.icon" />
            </svg>
            <span class="truncate leading-none z-10">{{ tab.label }}</span>
          </NuxtLink>
        </nav>
      </footer>
    </div>
  </div>
</template>

<script setup lang="ts">
const route = useRoute()
const auth = useAuth()
const accounting = useAccounting()
const theme = useTheme()
const currencyStore = useCurrency()
const isMobileMenuOpen = ref(false)

const isAuthRoute = computed(() => route.path === '/login' || route.path === '/register')
const showNavigation = computed(() => auth.isAuthenticated.value && !isAuthRoute.value)

onMounted(async () => {
  theme.initTheme()
  try {
    const projectsStore = useProjects()
    await auth.fetchUser()
    await accounting.fetchFundAccounts()
    await accounting.fetchLedgerAccounts()
    await accounting.fetchAccountItems()
    await accounting.fetchJournalEntries()
    await projectsStore.fetchCities()
    await projectsStore.fetchProjects()
  } catch (err) {
    console.error('Failed to load initial data:', err)
  }
})

// ── Mobile Navigation Tabs ──────────────────────────────────────────────────
const tabs = [
  { tab: 'dashboard', href: '/',                    icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', label: 'Dashboard' },
  { tab: 'projects',  href: '/projects',             icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m3 0h14M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', label: 'Projects' },
  { tab: 'funds',     href: '/management/funds',    icon: 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', label: 'Funds' },
  { tab: 'accounts',  href: '/management/accounts', icon: 'M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', label: 'Accounts' },
  { tab: 'settings',  href: '/settings',            icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z', label: 'Settings' },
] as const

const isTabActive = (href: string) => route.path === href || (href === '/projects' && (route.path.startsWith('/project/') || route.path.startsWith('/projects/')))

// ── Drawer Nav Links ──────────────────────────────────────────────────────────
const mainLinks = [
  {
    to: '/',
    label: 'Dashboard',
    icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
  },
  {
    to: '/projects',
    label: 'Projects',
    icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m3 0h14M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
  },
]

const managementLinks = [
  {
    to: '/management/funds',
    label: 'Fund Accounts',
    icon: 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
  },
  {
    to: '/management/accounts',
    label: 'Ledger Accounts',
    icon: 'M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
  },
  {
    to: '/management/items',
    label: 'Account Items Catalog',
    icon: 'M7 7h.01M7 11h.01M7 15h.01M11 7h7M11 11h7M11 15h7M4 5h16a1 1 0 011 1v12a1 1 0 01-1 1H4a1 1 0 01-1-1V6a1 1 0 011-1z',
  },
]

// ── Computed Helpers ──────────────────────────────────────────────────────────
const companyName = computed(() => auth.currentCompany.value?.business_name || 'Apex Technologies')
const companyScope = computed(() => auth.currentCompany.value?.business_scope || 'National')
const companyInitials = computed(() => {
  const name = companyName.value
  return name.split(' ').map((n: string) => n[0]).join('').slice(0, 2).toUpperCase()
})

const userName = computed(() => {
  const p = auth.currentPerson.value
  return p ? `${p.first_name || 'Alexander'} ${p.last_name || 'Sterling'}` : 'Alexander Sterling'
})
const userTitle = computed(() => auth.currentPosition.value?.title || 'Accountant')
const userInitials = computed(() => {
  const p = auth.currentPerson.value
  const f = p?.first_name?.[0] || 'A'
  const l = p?.last_name?.[0] || 'S'
  return `${f}${l}`.toUpperCase()
})

const netBalance = computed(() => accounting.netLedgerBalance.value || 0)
const isDarkMode = computed(() => theme.isDark.value)

const pageTitle = computed(() => {
  if (route.path === '/') return 'Dashboard Overview'
  if (route.path === '/projects') return 'Enterprise Projects'
  if (route.path.startsWith('/project/') || route.path.startsWith('/projects/')) return 'Project Management'
  switch (route.path) {
    case '/management/journal':    return 'Journal Transactions'
    case '/management/funds':      return 'Fund Accounts'
    case '/management/accounts':   return 'Ledger Accounts'
    case '/management/items':      return 'Items Catalog'
    case '/settings':              return 'Settings & Profile'
    default:                       return 'AccountAnt'
  }
})

const currentPageGroup = computed(() => {
  if (route.path === '/' || route.path.startsWith('/project') || route.path.startsWith('/projects')) return 'Projects'
  if (route.path.startsWith('/management')) return 'Management'
  if (route.path === '/settings')           return 'Configuration'
  return 'Dashboard'
})
</script>
