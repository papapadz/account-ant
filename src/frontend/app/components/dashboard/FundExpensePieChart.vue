<template>
  <div class="glass-card p-5 rounded-2xl border border-[var(--border-color)] flex flex-col justify-between space-y-4 h-full">
    <!-- Header & Fund Selector -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-3 border-b border-[var(--border-color)]">
      <div class="w-full sm:w-auto">
        <h3 class="text-sm font-bold text-[var(--text-main)] tracking-tight flex items-center gap-2">
          <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
          </svg>
          Fund Project Expenses
        </h3>
        <p class="text-[11px] text-[var(--text-muted)] mt-0.5">Project expense breakdown for selected fund source</p>
      </div>

      <!-- Filters: Fund Source & Year -->
      <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto">
        <select
          v-model="selectedFundId"
          class="input-field text-xs !py-1.5 !px-2.5 w-full sm:w-auto sm:max-w-xs font-medium"
        >
          <option value="all">All Fund Sources</option>
          <option v-for="fund in accounting.fundAccounts.value" :key="fund.id" :value="fund.id">
            {{ fund.fund_code }} - {{ fund.fund_name }}
          </option>
        </select>

        <select
          v-model="selectedYear"
          class="input-field text-xs !py-1.5 !px-2.5 w-full sm:w-auto sm:max-w-[90px] font-mono font-medium"
        >
          <option v-for="yr in availableYears" :key="yr" :value="yr">
            {{ yr }}
          </option>
        </select>
      </div>
    </div>

    <!-- Donut Chart & Legend -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2 gap-6 items-center flex-1 w-full">
      <!-- SVG Donut Graphic -->
      <div class="relative flex items-center justify-center p-2 w-full h-full min-h-[180px]">
        <svg viewBox="0 0 100 100" class="w-full max-w-[180px] h-auto transform -rotate-90">
          <!-- Background Ring -->
          <circle
            cx="50"
            cy="50"
            r="38"
            fill="transparent"
            stroke="var(--border-color)"
            stroke-width="14"
            pathLength="100"
          />

          <!-- Donut Slices -->
          <circle
            v-for="(slice, idx) in chartSlices"
            :key="slice.projectId"
            cx="50"
            cy="50"
            r="38"
            fill="transparent"
            :stroke="slice.color"
            stroke-width="14"
            pathLength="100"
            :stroke-dasharray="`${slice.dashLength} ${100 - slice.dashLength}`"
            :stroke-dashoffset="-slice.dashOffset"
            class="transition-all duration-500 hover:opacity-85 cursor-pointer"
          />
        </svg>

        <!-- Donut Center Label -->
        <div class="absolute inset-0 flex flex-col items-center justify-center text-center pointer-events-none p-4">
          <span class="text-[9px] uppercase font-bold text-[var(--text-muted)] tracking-wider">Total Spent</span>
          <span class="text-xs sm:text-sm font-bold font-mono text-emerald-400 truncate max-w-full">
            {{ currencyStore.formatCurrency(totalFundExpense) }}
          </span>
        </div>
      </div>

      <!-- Legend & Project Breakdown List -->
      <div class="space-y-2 w-full flex-1 max-h-56 overflow-y-auto pr-1">
        <div
          v-for="(item, idx) in chartSlices"
          :key="item.projectId"
          class="flex items-center justify-between p-2.5 rounded-xl bg-[var(--bg-surface)] border border-[var(--border-color)] hover:border-emerald-500/30 transition-all text-xs"
        >
          <div class="flex items-center gap-2 overflow-hidden flex-1 min-w-0 mr-2">
            <span class="w-3 h-3 rounded-full shrink-0 border border-black/10" :style="{ backgroundColor: item.color }"></span>
            <span class="font-semibold text-[var(--text-main)] truncate text-[11px] sm:text-xs" :title="item.projectName">
              {{ item.projectName }}
            </span>
          </div>

          <div class="flex items-center gap-2 shrink-0">
            <span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
              {{ item.percentage }}%
            </span>
            <span class="font-mono text-xs font-bold text-[var(--text-main)]">
              {{ currencyStore.formatCurrency(item.amount) }}
            </span>
          </div>
        </div>

        <div v-if="chartSlices.length === 0" class="text-center py-8 text-xs text-[var(--text-muted)] italic">
          No posted expenses found for this fund source.
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const currencyStore = useCurrency()
const accounting = useAccounting()
const projectsStore = useProjects()

const selectedFundId = ref<number | 'all'>('all')
const selectedYear = ref<number>(2026)

const palette = ['#10B981', '#3B82F6', '#F59E0B', '#8B5CF6', '#EC4899']

const syncDefaultFund = () => {
  const funds = accounting.fundAccounts.value
  if (selectedFundId.value !== 'all' && funds.length > 0 && !funds.some(f => f.id === selectedFundId.value)) {
    selectedFundId.value = 'all'
  }
}

onMounted(async () => {
  if (accounting.fundAccounts.value.length === 0) {
    await accounting.fetchFundAccounts()
  }
  if (projectsStore.projects.value.length === 0) {
    await projectsStore.fetchProjects()
  }
  syncDefaultFund()
})

watch(() => accounting.fundAccounts.value, () => {
  syncDefaultFund()
}, { deep: true })

const availableYears = computed(() => {
  const yearsSet = new Set<number>([2026, 2025, 2024])
  for (const t of projectsStore.transactions.value) {
    const txDateStr = t.posting_date || t.date
    if (txDateStr) {
      const yr = new Date(txDateStr).getFullYear()
      if (!isNaN(yr)) yearsSet.add(yr)
    }
  }
  return Array.from(yearsSet).sort((a, b) => b - a)
})

const rawExpenses = computed(() => {
  const targetYear = selectedYear.value
  const targetFundId = selectedFundId.value

  const filteredTxs = projectsStore.transactions.value.filter(t => {
    if (targetFundId !== 'all' && t.fund_source_id !== targetFundId) return false
    if (t.type !== 'debit') return false
    const dateStr = t.posting_date || t.date
    if (!dateStr) return false
    const txYear = new Date(dateStr).getFullYear()
    return txYear === targetYear
  })

  const totalSpent = filteredTxs.reduce((sum, t) => sum + Number(t.amount), 0)
  const projectMap = new Map<number, number>()
  for (const tx of filteredTxs) {
    const cur = projectMap.get(tx.project_id) || 0
    projectMap.set(tx.project_id, cur + Number(tx.amount))
  }

  return Array.from(projectMap.entries()).map(([pId, spent]) => {
    const proj = projectsStore.projects.value.find(p => p.id === pId)
    return {
      projectId: pId,
      projectName: proj ? proj.name : `Project #${pId}`,
      amount: spent,
      percentage: totalSpent > 0 ? Math.round((spent / totalSpent) * 100) : 0,
    }
  }).sort((a, b) => b.amount - a.amount)
})

const processedExpenses = computed(() => {
  const expenses = rawExpenses.value
  if (expenses.length <= 6) return expenses

  const top5 = expenses.slice(0, 5)
  const rest = expenses.slice(5)
  const othersAmount = rest.reduce((sum, item) => sum + item.amount, 0)

  return [
    ...top5,
    {
      projectId: -1,
      projectName: 'Others',
      amount: othersAmount,
      isOthers: true
    }
  ]
})

const totalFundExpense = computed(() => {
  return rawExpenses.value.reduce((sum: number, e: any) => sum + e.amount, 0)
})

const chartSlices = computed(() => {
  const total = totalFundExpense.value
  let currentOffset = 0

  return processedExpenses.value.map((item: any, idx: number) => {
    const fraction = total > 0 ? item.amount / total : 0
    const dashLength = fraction * 100
    const dashOffset = currentOffset
    currentOffset += dashLength

    return {
      ...item,
      color: item.projectId === -1 ? '#64748B' : palette[idx % palette.length],
      dashLength,
      dashOffset,
      percentage: total > 0 ? Math.round(fraction * 100) : 0,
    }
  })
})
</script>
