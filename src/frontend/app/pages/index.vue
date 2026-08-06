<template>
  <div class="space-y-6">
    <!-- Top Greeting & Action Banner -->
    <div class="glass-panel p-6 rounded-2xl border border-[var(--border-color)] relative overflow-hidden flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
      <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

      <div>
        <ClientOnly>
          <h1 class="text-2xl font-bold text-[var(--text-main)] tracking-tight">
            Dashboard
          </h1>
        </ClientOnly>
        <div class="flex items-center gap-2 text-xs text-emerald-500 font-mono font-semibold uppercase tracking-wider mb-1">
          <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
          Managing projects and fund allocations for <span class="text-[var(--text-main)] font-semibold">{{ companyName }}</span>.
        </div>
      </div>

      <div class="flex flex-wrap items-center gap-3 shrink-0">
        <NuxtLink to="/projects">
          <UiButton type="button" variant="secondary">
            View All Projects &rarr;
          </UiButton>
        </NuxtLink>
        <UiButton type="button" variant="primary" @click="isCreateModalOpen = true">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
          </svg>
          Create New Project
        </UiButton>
      </div>
    </div>

    <!-- Overall Financial Summary Cards -->
    <ClientOnly>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <KpiCard
          title="Total Managed Funds"
          :value="`${currencyStore.formatCurrency(totalAppManagedFunds)}`"
          subtitle="Total fund accounts balance"
          type="emerald"
        >
          <template #icon>
            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </template>
        </KpiCard>

      <KpiCard
        title="Total Spent"
        :value="`${currencyStore.formatCurrency(totalAppSpent)}`"
        :subtitle="`Accounts Payable: ${currencyStore.formatCurrency(totalAccountsPayable)}`"
        type="rose"
      >
        <template #icon>
          <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
          </svg>
        </template>
      </KpiCard>

        <KpiCard
          title="Remaining Balance"
          :value="`${currencyStore.formatCurrency(totalAppRemainingBalance)}`"
          subtitle="Unallocated liquidity"
          type="blue"
        >
          <template #icon>
            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h10M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
          </template>
        </KpiCard>

        <KpiCard
          title="Active Projects"
          :value="String(activeProjectsCount)"
          :subtitle="`${totalProjectsCount} total projects registered`"
          type="amber"
        >
          <template #icon>
            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m3 0h14M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
          </template>
        </KpiCard>
      </div>
    </ClientOnly>

    <!-- Visual Analytics Row: Charts -->
    <ClientOnly>
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Fund Source Line Chart -->
        <DashboardFundSourceLineChart />

        <!-- Ledger Expenses Pie Chart -->
        <DashboardLedgerExpensePieChart />

        <!-- Fund Expense Pie Chart -->
        <DashboardFundExpensePieChart />

        <!-- Monthly Project Expense Bar Chart -->
        <DashboardProjectMonthlyBarChart />

        <!-- Accounts Payable Pie Chart -->
        <DashboardAccountsPayablePieChart />

        <!-- Accounts Payable Monthly Line Chart -->
        <DashboardAccountsPayableLineChart />
      </div>
    </ClientOnly>

    <!-- Active Projects Quick Overview Card Grid -->
    <div class="glass-card p-5 rounded-2xl border border-[var(--border-color)] space-y-4">
      <div class="flex items-center justify-between pb-3 border-b border-[var(--border-color)]">
        <div>
          <h2 class="text-base font-bold text-[var(--text-main)] tracking-tight">Active Projects Overview</h2>
          <p class="text-xs text-[var(--text-muted)] mt-0.5">Quick access to key operational projects</p>
        </div>
        <NuxtLink to="/projects" class="text-xs font-bold text-emerald-500 hover:text-emerald-400 flex items-center gap-1 transition-colors">
          <span>Explore All {{ totalProjectsCount }} Projects</span>
          <span>&rarr;</span>
        </NuxtLink>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-1">
        <div
          v-for="project in activeProjectsPreview"
          :key="project.id"
          class="bg-[var(--bg-surface)] p-4 rounded-xl border border-[var(--border-color)] hover:border-emerald-500/40 transition-all cursor-pointer group flex flex-col justify-between"
          @click="router.push(`/project/${project.id}`)"
        >
          <div>
            <div class="flex items-center justify-between mb-2">
              <span class="text-[10px] font-mono text-[var(--text-muted)] font-bold">PRJ-#{{ project.id }}</span>
              <UiBadge variant="emerald" size="sm">Active</UiBadge>
            </div>
            <h3 class="text-sm font-bold text-[var(--text-main)] group-hover:text-emerald-400 transition-colors line-clamp-1">
              {{ project.name }}
            </h3>
            <p class="text-[11px] text-[var(--text-muted)] mt-1 flex items-center gap-1 truncate">
              <svg class="w-3 h-3 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
              </svg>
              <span>{{ formatAddress(project.address) }}</span>
            </p>
          </div>

          <div class="mt-4 pt-3 border-t border-[var(--border-color)] flex items-center justify-between text-xs font-mono">
            <span class="text-[var(--text-muted)]">Budget: {{ currencyStore.formatCurrency(project.budget || 0) }}</span>
            <span class="text-emerald-500 group-hover:translate-x-1 transition-transform">&rarr;</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Create Project Modal with Cascading Dropdowns -->
    <ProjectFormModal
      :isOpen="isCreateModalOpen"
      @close="isCreateModalOpen = false"
      @created="handleProjectCreated"
    />
  </div>
</template>

<script setup lang="ts">
const router = useRouter()
const auth = useAuth()
const projectsStore = useProjects()
const accounting = useAccounting()
const currencyStore = useCurrency()

const isCreateModalOpen = ref(false)

const companyName = computed(() => auth.currentCompany.value?.business_name)
const totalAccountsPayable = computed(() => accounting.totalUnpaidBalance.value)
const totalAppSpent = computed(() => accounting.totalPaidExpenses.value)
const totalAppManagedFunds = computed(() => accounting.totalFundSource.value)
const totalAppRemainingBalance = computed(() => accounting.totalFundAccountsBalance.value)
const totalProjectsCount = computed(() => projectsStore.projects.value.length)
const activeProjectsCount = computed(() => projectsStore.projects.value.filter(p => p.status === 'active').length)


const activeProjectsPreview = computed(() => {
  return projectsStore.projects.value.filter(p => p.status === 'active').slice(0, 3)
})

const formatAddress = (address: any) => {
  if (!address) return 'N/A'
  if (typeof address === 'string') return address
  return `${address.street}, ${address.city}`
}

const handleProjectCreated = (created: any) => {
  if (created?.id) {
    router.push(`/project/${created.id}`)
  }
}
</script>
