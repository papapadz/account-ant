<template>
  <div class="space-y-6">
    <!-- Top Greeting & Company Banner -->
    <div class="glass-panel p-6 rounded-2xl border border-[#1E293B] relative overflow-hidden flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
      <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

      <div>
        <div class="flex items-center gap-2 text-xs text-emerald-400 font-mono font-semibold uppercase tracking-wider mb-1">
          <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
          Real-time Ledger Node Active
        </div>
        <h1 class="text-2xl font-bold text-slate-100 tracking-tight">
          Welcome back, {{ auth.currentPerson.value?.first_name || 'Administrator' }} 👋
        </h1>
        <p class="text-xs text-slate-400 mt-1 max-w-xl">
          Managing financial ledgers for <span class="text-slate-200 font-semibold">{{ auth.currentCompany.value?.business_name }}</span>.
          All automated journal postings and project running balances are reconciled in real time.
        </p>
      </div>

      <div class="flex items-center gap-3 shrink-0">
        <NuxtLink to="/management/projects" class="btn-primary">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
          </svg>
          Manage Projects Ledger
        </NuxtLink>
        <NuxtLink to="/management/journal" class="btn-secondary">
          Post Journal Entry
        </NuxtLink>
      </div>
    </div>

    <!-- Financial KPI Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <KpiCard
        title="Net Ledger Balance"
        :value="'$' + formatCurrency(accounting.netLedgerBalance.value)"
        subtitle="Calculated Debits minus Credits"
        change="Positive Balance"
        :changeIsPositive="accounting.netLedgerBalance.value >= 0"
        type="emerald"
      >
        <template #icon>
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </template>
      </KpiCard>

      <KpiCard
        title="Total Active Projects"
        :value="projectStore.projects.value.length.toString()"
        subtitle="Government & Private Projects"
        type="blue"
      >
        <template #icon>
          <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m3 0h1m-4-8a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2a1 1 0 01-1-1v-4z" />
          </svg>
        </template>
      </KpiCard>

      <KpiCard
        title="Total Allocated Projects Budget"
        :value="'$' + formatCurrency(totalProjectBudgetSum)"
        subtitle="Capital Project Commitments"
        type="amber"
      >
        <template #icon>
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6" />
          </svg>
        </template>
      </KpiCard>

      <KpiCard
        title="Active Fund Accounts"
        :value="accounting.fundAccounts.value.length.toString()"
        subtitle="Corporate Managed Funds"
        type="emerald"
      >
        <template #icon>
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
          </svg>
        </template>
      </KpiCard>
    </div>

    <!-- Active Projects Quick Overview Panel -->
    <div class="glass-card p-5 rounded-xl border border-[#1E293B] space-y-4">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-sm font-bold text-slate-100 uppercase tracking-wider flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            Active Projects Budget Utilization & Running Balances
          </h3>
          <p class="text-xs text-slate-400 mt-0.5">Real-time budget tracking vs expenses across all active project ledgers</p>
        </div>

        <NuxtLink to="/management/projects" class="text-xs text-emerald-400 hover:underline font-semibold flex items-center gap-1">
          <span>Manage All Projects</span>
          <span>&rarr;</span>
        </NuxtLink>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div
          v-for="proj in projectStore.projects.value"
          :key="proj.id"
          class="bg-[#0F172A] border border-[#1E293B] p-4 rounded-xl space-y-3 hover:border-emerald-500/30 transition-colors"
        >
          <div class="flex items-center justify-between">
            <NuxtLink :to="`/management/projects/${proj.id}`" class="font-bold text-slate-100 text-sm hover:text-emerald-400 transition-colors">
              {{ proj.name }}
            </NuxtLink>
            <span
              class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border"
              :class="proj.is_government ? 'bg-purple-500/10 text-purple-400 border-purple-500/30' : 'bg-blue-500/10 text-blue-400 border-blue-500/30'"
            >
              {{ proj.is_government ? 'Government' : 'Private' }}
            </span>
          </div>

          <div class="flex items-center justify-between text-xs font-mono">
            <span class="text-slate-400">Client: {{ proj.client_name }}</span>
            <span class="text-emerald-400 font-bold">Bal: ${{ formatCurrency(projectStore.getProjectMetrics(proj.id)?.runningBalance || 0) }}</span>
          </div>

          <!-- Progress Bar -->
          <div class="space-y-1">
            <div class="flex items-center justify-between text-[11px] font-mono">
              <span class="text-slate-400">${{ formatCurrency(projectStore.getProjectMetrics(proj.id)?.netExpenses || 0) }} / ${{ formatCurrency(proj.budget) }}</span>
              <span
                class="font-bold"
                :class="(projectStore.getProjectMetrics(proj.id)?.budgetUtilizedPercentage || 0) > 100 ? 'text-rose-400' : 'text-emerald-400'"
              >
                {{ projectStore.getProjectMetrics(proj.id)?.budgetUtilizedPercentage || 0 }}%
              </span>
            </div>
            <div class="w-full bg-slate-800 rounded-full h-2 overflow-hidden">
              <div
                class="h-2 rounded-full transition-all duration-500"
                :class="(projectStore.getProjectMetrics(proj.id)?.budgetUtilizedPercentage || 0) > 100 ? 'bg-rose-500' : 'bg-emerald-500'"
                :style="{ width: Math.min(projectStore.getProjectMetrics(proj.id)?.budgetUtilizedPercentage || 0, 100) + '%' }"
              ></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Balance Distribution Visualizer & Recent Transactions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Balance Visualizer Bar -->
      <div class="lg:col-span-1 glass-card p-5 rounded-xl border border-[#1E293B] flex flex-col justify-between">
        <div>
          <h3 class="text-sm font-bold text-slate-100 uppercase tracking-wider mb-4 flex items-center justify-between">
            <span>Ledger Reconciliation</span>
            <span class="text-[10px] text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded border border-emerald-500/20 font-mono">Balanced</span>
          </h3>

          <div class="space-y-4">
            <div>
              <div class="flex justify-between text-xs mb-1">
                <span class="text-slate-400">Debit Ratio</span>
                <span class="font-mono text-blue-400 font-semibold">{{ debitRatio.toFixed(1) }}%</span>
              </div>
              <div class="w-full h-2.5 bg-[#020617] rounded-full overflow-hidden p-0.5 border border-[#1E293B]">
                <div class="h-full bg-blue-500 rounded-full transition-all duration-500" :style="{ width: `${debitRatio}%` }"></div>
              </div>
            </div>

            <div>
              <div class="flex justify-between text-xs mb-1">
                <span class="text-slate-400">Credit Ratio</span>
                <span class="font-mono text-amber-400 font-semibold">{{ creditRatio.toFixed(1) }}%</span>
              </div>
              <div class="w-full h-2.5 bg-[#020617] rounded-full overflow-hidden p-0.5 border border-[#1E293B]">
                <div class="h-full bg-amber-500 rounded-full transition-all duration-500" :style="{ width: `${creditRatio}%` }"></div>
              </div>
            </div>
          </div>

          <div class="mt-6 p-3 rounded-lg bg-[#020617] border border-[#1E293B] space-y-2">
            <div class="flex justify-between text-xs">
              <span class="text-slate-400">Chart of Accounts:</span>
              <span class="font-mono text-slate-200 font-semibold">{{ accounting.ledgerAccounts.value.length }} accounts</span>
            </div>
            <div class="flex justify-between text-xs">
              <span class="text-slate-400">Account Items Catalog:</span>
              <span class="font-mono text-slate-200 font-semibold">{{ accounting.accountItems.value.length }} items</span>
            </div>
            <div class="flex justify-between text-xs">
              <span class="text-slate-400">Journal Entries Recorded:</span>
              <span class="font-mono text-slate-200 font-semibold">{{ accounting.journalEntries.value.length }} entries</span>
            </div>
          </div>
        </div>

        <div class="pt-4 mt-4 border-t border-[#1E293B]">
          <NuxtLink to="/management/accounts" class="text-xs text-emerald-400 hover:underline flex items-center justify-between">
            <span>View Full Chart of Accounts &rarr;</span>
          </NuxtLink>
        </div>
      </div>

      <!-- Recent Journal Postings Stream -->
      <div class="lg:col-span-2 glass-card p-5 rounded-xl border border-[#1E293B]">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-sm font-bold text-slate-100 uppercase tracking-wider">Recent Journal Transactions</h3>
          <NuxtLink to="/management/journal" class="text-xs text-blue-400 hover:underline">View All &rarr;</NuxtLink>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="text-[11px] text-slate-400 uppercase tracking-wider border-b border-[#1E293B]">
                <th class="pb-2 font-semibold">Entry ID</th>
                <th class="pb-2 font-semibold">Description</th>
                <th class="pb-2 font-semibold">Type</th>
                <th class="pb-2 font-semibold text-right">Amount</th>
                <th class="pb-2 font-semibold text-right">Date</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#1E293B]/60 text-xs">
              <tr v-for="entry in recentEntries" :key="entry.id" class="hover:bg-[#1E293B]/40 transition-colors">
                <td class="py-3 font-mono text-slate-400">#{{ entry.id }}</td>
                <td class="py-3 font-medium text-slate-200 max-w-xs truncate">{{ entry.description || 'Journal Entry' }}</td>
                <td class="py-3">
                  <span
                    class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wider"
                    :class="entry.transaction_type === 'debit' ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20'"
                  >
                    {{ entry.transaction_type }}
                  </span>
                </td>
                <td class="py-3 text-right font-mono font-bold" :class="entry.transaction_type === 'debit' ? 'text-blue-400' : 'text-amber-400'">
                  ${{ formatCurrency(entry.amount) }}
                </td>
                <td class="py-3 text-right text-slate-400 text-[11px] font-mono">{{ entry.created_at.split(' ')[0] }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const auth = useAuth()
const accounting = useAccounting()
const projectStore = useProjects()

onMounted(async () => {
  await projectStore.fetchAll()
})

const recentEntries = computed(() => accounting.journalEntries.value.slice(0, 5))

const totalProjectBudgetSum = computed(() => {
  return projectStore.projects.value.reduce((sum, p) => sum + Number(p.budget), 0)
})

const totalVolume = computed(() => accounting.totalDebits.value + accounting.totalCredits.value || 1)
const debitRatio = computed(() => (accounting.totalDebits.value / totalVolume.value) * 100)
const creditRatio = computed(() => (accounting.totalCredits.value / totalVolume.value) * 100)

const formatCurrency = (val: number) => {
  return new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(val)
}
</script>
