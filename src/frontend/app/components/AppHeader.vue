<template>
  <header class="h-16 bg-[#0B1120]/80 dark:bg-[#0B1120]/80 light:bg-white/80 backdrop-blur-md border-b border-[#1E293B] light:border-slate-200 px-6 flex items-center justify-between sticky top-0 z-20 transition-colors">
    <!-- Title & Breadcrumb -->
    <div>
      <div class="text-[11px] text-slate-400 light:text-slate-500 flex items-center gap-1.5 font-medium">
        <span>AccountAnt</span>
        <span class="text-slate-600 light:text-slate-300">/</span>
        <span class="text-emerald-500 font-semibold capitalize">{{ currentPageGroup }}</span>
      </div>
      <h2 class="text-sm font-bold text-slate-100 light:text-slate-900 tracking-tight">{{ pageTitle }}</h2>
    </div>

    <!-- Actions, Theme Switcher & Health Status -->
    <div class="flex items-center gap-3 sm:gap-4">
      <!-- Real-time Net Ledger Balance Pill wrapped in ClientOnly -->
      <ClientOnly>
        <div class="hidden sm:flex items-center gap-2 bg-[#0F172A] light:bg-slate-100 border border-[#1E293B] light:border-slate-200 px-3 py-1.5 rounded-full text-xs">
          <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
          <span class="text-slate-400 light:text-slate-600 font-medium">Net Ledger Balance:</span>
          <span class="font-mono font-bold text-emerald-400 light:text-emerald-600">
            ${{ formatCurrency(accounting.netLedgerBalance.value) }}
          </span>
        </div>
        <template #fallback>
          <div class="hidden sm:flex items-center gap-2 bg-[#0F172A] light:bg-slate-100 border border-[#1E293B] light:border-slate-200 px-3 py-1.5 rounded-full text-xs opacity-50">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            <span class="text-slate-400 font-medium">Net Ledger Balance:</span>
            <span class="font-mono font-bold text-emerald-400">$0.00</span>
          </div>
        </template>
      </ClientOnly>

      <!-- Light / Dark Mode Toggle Button -->
      <ClientOnly>
        <button
          @click="theme.toggleTheme()"
          class="btn-secondary py-1.5 px-2.5 text-xs flex items-center gap-1.5"
          :title="theme.isDark.value ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
        >
          <svg v-if="theme.isDark.value" class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
          </svg>
          <svg v-else class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
          </svg>
          <span class="hidden md:inline text-[11px] font-medium">{{ theme.isDark.value ? 'Dark' : 'Light' }}</span>
        </button>
      </ClientOnly>

      <!-- API Online Badge -->
      <div class="flex items-center gap-1.5 bg-blue-500/10 text-blue-400 light:bg-blue-50 light:text-blue-700 border border-blue-500/20 light:border-blue-200 text-[11px] font-semibold px-2.5 py-1 rounded-md">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7" />
        </svg>
        <span class="hidden sm:inline">API Connected</span>
      </div>

      <!-- Quick Action: Create Entry -->
      <NuxtLink
        to="/management/journal"
        class="btn-primary py-1.5 px-3 text-xs shadow-sm hover:glow-emerald"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
        </svg>
        <span>+ Post Transaction</span>
      </NuxtLink>
    </div>
  </header>
</template>

<script setup lang="ts">
const route = useRoute()
const accounting = useAccounting()
const theme = useTheme()

onMounted(() => {
  theme.initTheme()
})

const pageTitle = computed(() => {
  switch (route.path) {
    case '/': return 'Ledger Overview & KPI Dashboard'
    case '/management/journal': return 'Journal Transactions Ledger'
    case '/management/funds': return 'Fund Accounts Management'
    case '/management/accounts': return 'Ledger Accounts (Chart of Accounts)'
    case '/management/items': return 'Master Account Items Catalog'
    case '/settings': return 'System Settings & Profile'
    default: return 'AccountAnt Accounting System'
  }
})

const currentPageGroup = computed(() => {
  if (route.path.startsWith('/management')) return 'Management'
  if (route.path === '/settings') return 'Configuration'
  return 'Dashboard'
})

const formatCurrency = (val: number) => {
  const num = typeof val === 'number' && !isNaN(val) ? val : 0
  return num.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',')
}
</script>
