<template>
  <header class="h-16 bg-[#0B1120]/80 backdrop-blur-md border-b border-[#1E293B] px-6 flex items-center justify-between sticky top-0 z-20">
    <!-- Title & Breadcrumb -->
    <div>
      <div class="text-[11px] text-slate-400 flex items-center gap-1.5 font-medium">
        <span>AccountAnt</span>
        <span class="text-slate-600">/</span>
        <span class="text-emerald-400 capitalize">{{ currentPageGroup }}</span>
      </div>
      <h2 class="text-sm font-bold text-slate-100 tracking-tight">{{ pageTitle }}</h2>
    </div>

    <!-- Actions & Health Status -->
    <div class="flex items-center gap-4">
      <!-- Real-time Net Ledger Balance Pill -->
      <div class="hidden sm:flex items-center gap-2 bg-[#0F172A] border border-[#1E293B] px-3 py-1.5 rounded-full text-xs">
        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
        <span class="text-slate-400 font-medium">Net Ledger Balance:</span>
        <span class="font-mono font-bold text-emerald-400">
          ${{ formatCurrency(accounting.netLedgerBalance.value) }}
        </span>
      </div>

      <!-- API Online Badge -->
      <div class="flex items-center gap-1.5 bg-blue-500/10 text-blue-400 border border-blue-500/20 text-[11px] font-semibold px-2.5 py-1 rounded-md">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7" />
        </svg>
        API Connected
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
  return new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(val)
}
</script>
