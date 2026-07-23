<template>
  <div class="space-y-6">
    <!-- Top Greeting & Company Banner -->
    <div class="glass-panel p-6 rounded-2xl border border-[var(--border-color)] relative overflow-hidden flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
      <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

      <div>
        <div class="flex items-center gap-2 text-xs text-emerald-500 font-mono font-semibold uppercase tracking-wider mb-1">
          <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
          Real-time Ledger Node Active
        </div>
        <ClientOnly>
          <h1 class="text-2xl font-bold text-[var(--text-main)] tracking-tight">
            Welcome back, {{ auth.currentPerson.value?.first_name || 'Administrator' }} 👋
          </h1>
          <p class="text-xs text-[var(--text-muted)] mt-1 max-w-xl">
            Managing financial ledgers for <span class="text-[var(--text-main)] font-semibold">{{ auth.currentCompany.value?.business_name }}</span>.
            All automated journal postings are reconciled in real time.
          </p>
        </ClientOnly>
      </div>

      <div class="flex items-center gap-3 shrink-0">
        <NuxtLink to="/management/journal" class="btn-primary">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
          </svg>
          Post Journal Entry
        </NuxtLink>
        <NuxtLink to="/management/funds" class="btn-secondary">
          Manage Funds
        </NuxtLink>
      </div>
    </div>

    <!-- Financial KPI Cards Grid -->
    <ClientOnly>
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
          title="Total Debits Posted"
          :value="'$' + formatCurrency(accounting.totalDebits.value)"
          subtitle="Asset & Expense Accumulation"
          type="blue"
        >
          <template #icon>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12" />
            </svg>
          </template>
        </KpiCard>

        <KpiCard
          title="Total Credits Posted"
          :value="'$' + formatCurrency(accounting.totalCredits.value)"
          subtitle="Liability & Revenue Postings"
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
    </ClientOnly>

    <!-- Balance Distribution Visualizer & Recent Transactions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Balance Visualizer Bar -->
      <div class="lg:col-span-1 glass-card p-5 rounded-xl border border-[var(--border-color)] flex flex-col justify-between">
        <ClientOnly>
          <div>
            <h3 class="text-sm font-bold text-[var(--text-main)] uppercase tracking-wider mb-4 flex items-center justify-between">
              <span>Ledger Reconciliation</span>
              <span class="text-[10px] text-emerald-500 bg-emerald-500/10 px-2 py-0.5 rounded border border-emerald-500/20 font-mono font-bold">Balanced</span>
            </h3>

            <div class="space-y-4">
              <div>
                <div class="flex justify-between text-xs mb-1">
                  <span class="text-[var(--text-muted)]">Debit Ratio</span>
                  <span class="font-mono text-blue-500 font-semibold">{{ debitRatio.toFixed(1) }}%</span>
                </div>
                <div class="w-full h-2.5 bg-[var(--bg-app)] rounded-full overflow-hidden p-0.5 border border-[var(--border-color)]">
                  <div class="h-full bg-blue-500 rounded-full transition-all duration-500" :style="{ width: `${debitRatio}%` }"></div>
                </div>
              </div>

              <div>
                <div class="flex justify-between text-xs mb-1">
                  <span class="text-[var(--text-muted)]">Credit Ratio</span>
                  <span class="font-mono text-amber-500 font-semibold">{{ creditRatio.toFixed(1) }}%</span>
                </div>
                <div class="w-full h-2.5 bg-[var(--bg-app)] rounded-full overflow-hidden p-0.5 border border-[var(--border-color)]">
                  <div class="h-full bg-amber-500 rounded-full transition-all duration-500" :style="{ width: `${creditRatio}%` }"></div>
                </div>
              </div>
            </div>

            <div class="mt-6 p-3 rounded-lg bg-[var(--bg-app)] border border-[var(--border-color)] space-y-2">
              <div class="flex justify-between text-xs">
                <span class="text-[var(--text-muted)]">Chart of Accounts:</span>
                <span class="font-mono text-[var(--text-main)] font-semibold">{{ accounting.ledgerAccounts.value.length }} accounts</span>
              </div>
              <div class="flex justify-between text-xs">
                <span class="text-[var(--text-muted)]">Account Items Catalog:</span>
                <span class="font-mono text-[var(--text-main)] font-semibold">{{ accounting.accountItems.value.length }} items</span>
              </div>
              <div class="flex justify-between text-xs">
                <span class="text-[var(--text-muted)]">Journal Entries Recorded:</span>
                <span class="font-mono text-[var(--text-main)] font-semibold">{{ accounting.journalEntries.value.length }} entries</span>
              </div>
            </div>
          </div>
        </ClientOnly>

        <div class="pt-4 mt-4 border-t border-[var(--border-color)]">
          <NuxtLink to="/management/accounts" class="text-xs text-emerald-500 hover:underline flex items-center justify-between font-semibold">
            <span>View Full Chart of Accounts &rarr;</span>
          </NuxtLink>
        </div>
      </div>

      <!-- Recent Journal Postings Stream -->
      <div class="lg:col-span-2 glass-card p-5 rounded-xl border border-[var(--border-color)]">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-sm font-bold text-[var(--text-main)] uppercase tracking-wider">Recent Journal Transactions</h3>
          <NuxtLink to="/management/journal" class="text-xs text-blue-500 hover:underline font-semibold">View All &rarr;</NuxtLink>
        </div>

        <ClientOnly>
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="text-[11px] text-[var(--text-muted)] uppercase tracking-wider border-b border-[var(--border-color)] bg-[var(--bg-table-header)]">
                  <th class="p-2.5 font-semibold">Entry ID</th>
                  <th class="p-2.5 font-semibold">Description</th>
                  <th class="p-2.5 font-semibold">Type</th>
                  <th class="p-2.5 font-semibold text-right">Amount</th>
                  <th class="p-2.5 font-semibold text-right">Date</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-[var(--border-color)] text-xs">
                <tr v-for="entry in recentEntries" :key="entry.id" class="hover:bg-[var(--bg-table-hover)] transition-colors">
                  <td class="p-2.5 font-mono text-[var(--text-muted)]">#{{ entry.id }}</td>
                  <td class="p-2.5 font-medium text-[var(--text-main)] max-w-xs truncate">{{ entry.description || 'Journal Entry' }}</td>
                  <td class="p-2.5">
                    <span
                      class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider"
                      :class="entry.transaction_type === 'debit' ? 'bg-blue-500/10 text-blue-500 border border-blue-500/20' : 'bg-amber-500/10 text-amber-500 border border-amber-500/20'"
                    >
                      {{ entry.transaction_type }}
                    </span>
                  </td>
                  <td class="p-2.5 text-right font-mono font-bold" :class="entry.transaction_type === 'debit' ? 'text-blue-500' : 'text-amber-500'">
                    ${{ formatCurrency(entry.amount) }}
                  </td>
                  <td class="p-2.5 text-right text-[var(--text-muted)] text-[11px] font-mono">{{ entry.created_at.split(' ')[0] }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </ClientOnly>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const auth = useAuth()
const accounting = useAccounting()

const recentEntries = computed(() => accounting.journalEntries.value.slice(0, 5))

const totalVolume = computed(() => accounting.totalDebits.value + accounting.totalCredits.value || 1)
const debitRatio = computed(() => (accounting.totalDebits.value / totalVolume.value) * 100)
const creditRatio = computed(() => (accounting.totalCredits.value / totalVolume.value) * 100)

const formatCurrency = (val: number) => {
  const num = typeof val === 'number' && !isNaN(val) ? val : 0
  return num.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',')
}
</script>
