<template>
  <div class="glass-card p-5 rounded-2xl border border-[var(--border-color)] flex flex-col justify-between space-y-4 h-full">
    <!-- Header & Dropdowns -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-3 border-b border-[var(--border-color)]">
      <div class="w-full sm:w-auto">
        <h3 class="text-sm font-bold text-[var(--text-main)] tracking-tight flex items-center gap-2">
          <svg class="w-4.5 h-4.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
          </svg>
          Ledger Expenses
        </h3>
        <p class="text-[11px] text-[var(--text-muted)] mt-0.5">Total outflows mapped by ledger account</p>
      </div>

      <!-- Filters: Project & Year -->
      <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto">
        <select
          v-model="selectedProjectId"
          class="input-field text-xs !py-1.5 !px-2.5 w-full sm:w-auto sm:max-w-[150px] font-medium animate-fade-in"
        >
          <option :value="null">All Projects</option>
          <option v-for="proj in projectsStore.projects.value" :key="proj.id" :value="proj.id">
            {{ proj.name || (proj as any).project_name }}
          </option>
        </select>

        <select
          v-model="selectedYear"
          class="input-field text-xs !py-1.5 !px-2.5 w-full sm:w-auto sm:max-w-[90px] font-mono font-medium animate-fade-in"
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
            :key="slice.accountId"
            cx="50"
            cy="50"
            r="38"
            fill="transparent"
            :stroke="slice.color"
            stroke-width="14"
            pathLength="100"
            :stroke-dasharray="`${slice.dashLength} ${100 - slice.dashLength}`"
            :stroke-dashoffset="-slice.dashOffset"
            class="transition-all duration-500 cursor-pointer"
            :class="hoveredIndex === idx ? 'opacity-100 stroke-[16px]' : 'opacity-85'"
            @mouseenter="hoveredIndex = idx"
            @mouseleave="hoveredIndex = null"
          />
        </svg>

        <!-- Donut Center Label -->
        <div class="absolute inset-0 flex flex-col items-center justify-center text-center pointer-events-none px-4 py-2">
          <span class="text-[9px] uppercase font-bold text-[var(--text-muted)] tracking-wider truncate max-w-full">
            {{ centerLabelTitle }}
          </span>
          <span class="text-xs sm:text-sm font-bold font-mono text-emerald-400 truncate max-w-full">
            {{ centerLabelValue }}
          </span>
          <span v-if="centerLabelSub" class="text-[10px] font-semibold text-[var(--text-muted)] font-mono">
            {{ centerLabelSub }}
          </span>
        </div>
      </div>

      <!-- Legend & Account Breakdown List -->
      <div class="space-y-2 w-full flex-1 max-h-56 overflow-y-auto pr-1">
        <div
          v-for="(item, idx) in chartSlices"
          :key="item.accountId"
          class="flex items-center justify-between p-2.5 rounded-xl bg-[var(--bg-surface)] border transition-all cursor-pointer hover:border-emerald-500/40"
          :class="hoveredIndex === idx ? 'border-emerald-500/40 bg-[var(--bg-surface)]/80 scale-[1.02]' : 'border-[var(--border-color)]'"
          @mouseenter="hoveredIndex = idx"
          @mouseleave="hoveredIndex = null"
        >
          <div class="flex items-center gap-2 overflow-hidden flex-1 min-w-0 mr-2">
            <span class="w-3.5 h-3.5 rounded-full shrink-0 border border-black/10" :style="{ backgroundColor: item.color }"></span>
            <div class="truncate min-w-0">
              <div class="font-semibold text-[var(--text-main)] truncate text-[11px] sm:text-xs" :title="item.accountName">
                {{ item.accountName }}
              </div>
              <div class="text-[9px] font-mono text-[var(--text-muted)] mt-0.5">
                {{ item.accountCode }}
              </div>
            </div>
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
          No expenses found matching the selected filters.
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const currencyStore = useCurrency()
const accounting = useAccounting()
const projectsStore = useProjects()

const selectedProjectId = ref<number | null>(null)
const selectedYear = ref<number>(2026)
const hoveredIndex = ref<number | null>(null)

const palette = ['#10B981', '#3B82F6', '#F59E0B', '#8B5CF6', '#EC4899']

const availableYears = computed(() => {
  const yearsSet = new Set<number>([2026, 2025, 2024])
  for (const entry of accounting.journalEntries.value) {
    const dateStr = entry.posting_date || entry.created_at
    if (dateStr) {
      const yr = new Date(dateStr).getFullYear()
      if (!isNaN(yr)) yearsSet.add(yr)
    }
  }
  return Array.from(yearsSet).sort((a, b) => b - a)
})

const rawExpenses = computed(() => {
  const targetYear = selectedYear.value
  const targetProjectId = selectedProjectId.value

  const filtered = accounting.journalEntries.value.filter(entry => {
    // Only debit transaction type represents expense/outflow
    if (entry.transaction_type !== 'debit') return false
    
    // Filter by year
    const dateStr = entry.posting_date || entry.created_at
    if (!dateStr) return false
    const entryYear = new Date(dateStr).getFullYear()
    if (entryYear !== targetYear) return false
    
    // Filter by project
    if (targetProjectId !== null && entry.project_id !== targetProjectId) return false
    
    return true
  })

  const accountMap = new Map<number, number>()
  for (const entry of filtered) {
    const accId = entry.ledger_account_id
    if (!accId) continue
    const current = accountMap.get(accId) || 0
    accountMap.set(accId, current + Number(entry.amount))
  }

  return Array.from(accountMap.entries()).map(([accId, amount]) => {
    const acc = accounting.ledgerAccounts.value.find(a => a.id === accId)
    return {
      accountId: accId,
      accountCode: acc ? acc.account_code : `ACC-${accId}`,
      accountName: acc ? acc.account_name : `Account #${accId}`,
      amount,
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
      accountId: -1,
      accountCode: 'OTHERS',
      accountName: 'Others',
      amount: othersAmount,
      isOthers: true
    }
  ]
})

const totalExpense = computed(() => {
  return rawExpenses.value.reduce((sum, item) => sum + item.amount, 0)
})

const chartSlices = computed(() => {
  const total = totalExpense.value
  let currentOffset = 0

  return processedExpenses.value.map((item: any, idx: number) => {
    const fraction = total > 0 ? item.amount / total : 0
    const dashLength = fraction * 100
    const dashOffset = currentOffset
    currentOffset += dashLength

    return {
      ...item,
      color: item.accountId === -1 ? '#64748B' : palette[idx % palette.length],
      dashLength,
      dashOffset,
      percentage: total > 0 ? Math.round(fraction * 100) : 0,
    }
  })
})

const centerLabelTitle = computed(() => {
  if (hoveredIndex.value !== null && chartSlices.value[hoveredIndex.value]) {
    return chartSlices.value[hoveredIndex.value].accountCode
  }
  return 'Total Expense'
})

const centerLabelValue = computed(() => {
  if (hoveredIndex.value !== null && chartSlices.value[hoveredIndex.value]) {
    return currencyStore.formatCurrency(chartSlices.value[hoveredIndex.value].amount)
  }
  return currencyStore.formatCurrency(totalExpense.value)
})

const centerLabelSub = computed(() => {
  if (hoveredIndex.value !== null && chartSlices.value[hoveredIndex.value]) {
    return `${chartSlices.value[hoveredIndex.value].percentage}%`
  }
  return ''
})

onMounted(async () => {
  if (accounting.ledgerAccounts.value.length === 0) {
    await accounting.fetchLedgerAccounts()
  }
  if (accounting.journalEntries.value.length === 0) {
    await accounting.fetchJournalEntries()
  }
  if (projectsStore.projects.value.length === 0) {
    await projectsStore.fetchProjects()
  }
})
</script>
