<template>
  <div class="glass-card p-5 rounded-2xl border border-[var(--border-color)] flex flex-col justify-between space-y-4 h-full">
    <!-- Header & Filters -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-3 border-b border-[var(--border-color)]">
      <div class="w-full sm:w-auto">
        <h3 class="text-sm font-bold text-[var(--text-main)] tracking-tight flex items-center gap-2">
          <svg class="w-4.5 h-4.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
          </svg>
          Accounts Payable Distribution
        </h3>
        <p class="text-[11px] text-[var(--text-muted)] mt-0.5">
          {{ selectedAccountId === null ? 'Unpaid payables mapped per ledger account' : 'Unpaid payables by creditor / vendor' }}
        </p>
      </div>

      <!-- Filters: Ledger Account & Year -->
      <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto">
        <!-- Ledger Account Dropdown Filter -->
        <select
          v-model="selectedAccountId"
          class="input-field text-xs !py-1.5 !px-2.5 w-full sm:w-auto sm:max-w-[180px] font-medium"
        >
          <option :value="null">All Ledger Accounts</option>
          <option v-for="acc in accounting.ledgerAccounts.value" :key="acc.id" :value="acc.id">
            {{ acc.account_code }} - {{ acc.account_name }}
          </option>
        </select>

        <!-- Year Dropdown Filter -->
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
            :key="slice.id"
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
          <span class="text-xs sm:text-sm font-bold font-mono text-rose-400 truncate max-w-full">
            {{ centerLabelValue }}
          </span>
          <span v-if="centerLabelSub" class="text-[10px] font-semibold text-[var(--text-muted)] font-mono">
            {{ centerLabelSub }}
          </span>
        </div>
      </div>

      <!-- Legend & Account / Creditor Breakdown List -->
      <div class="space-y-2 w-full flex-1 max-h-56 overflow-y-auto pr-1">
        <div
          v-for="(item, idx) in chartSlices"
          :key="item.id"
          class="flex items-center justify-between p-2.5 rounded-xl bg-[var(--bg-surface)] border transition-all cursor-pointer hover:border-rose-500/40"
          :class="hoveredIndex === idx ? 'border-rose-500/40 bg-[var(--bg-surface)]/80 scale-[1.02]' : 'border-[var(--border-color)]'"
          @mouseenter="hoveredIndex = idx"
          @mouseleave="hoveredIndex = null"
        >
          <div class="flex items-center gap-2 overflow-hidden flex-1 min-w-0 mr-2">
            <span class="w-3.5 h-3.5 rounded-full shrink-0 border border-black/10" :style="{ backgroundColor: item.color }"></span>
            <div class="truncate min-w-0">
              <div class="font-semibold text-[var(--text-main)] truncate text-[11px] sm:text-xs" :title="item.label">
                {{ item.label }}
              </div>
              <div v-if="item.subLabel" class="text-[9px] font-mono text-[var(--text-muted)] mt-0.5">
                {{ item.subLabel }}
              </div>
            </div>
          </div>

          <div class="flex items-center gap-2 shrink-0">
            <span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">
              {{ item.percentage }}%
            </span>
            <span class="font-mono text-xs font-bold text-[var(--text-main)]">
              {{ currencyStore.formatCurrency(item.amount) }}
            </span>
          </div>
        </div>

        <div v-if="chartSlices.length === 0" class="text-center py-8 text-xs text-[var(--text-muted)] italic">
          No unpaid accounts payable for the selected filters.
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const currencyStore = useCurrency()
const accounting = useAccounting()

const selectedAccountId = ref<number | null>(null)
const selectedYear = ref<number>(2026)
const hoveredIndex = ref<number | null>(null)

const palette = ['#F43F5E', '#FB923C', '#F59E0B', '#A855F7', '#EC4899', '#3B82F6', '#10B981']

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

const rawPayables = computed(() => {
  const targetYear = selectedYear.value
  const targetAccountId = selectedAccountId.value

  const filtered = accounting.journalEntries.value.filter(entry => {
    // Only unpaid entries (is_paid === false)
    if (entry.is_paid !== false) return false
    
    // Filter by year
    const dateStr = entry.posting_date || entry.created_at
    if (!dateStr) return false
    const entryYear = new Date(dateStr).getFullYear()
    if (entryYear !== targetYear) return false
    
    // Filter by ledger account if selected
    if (targetAccountId !== null && entry.ledger_account_id !== targetAccountId) return false
    
    return true
  })

  // Case A: All Ledger Accounts selected -> Group by Ledger Account ID
  if (targetAccountId === null) {
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
        id: `acc-${accId}`,
        label: acc ? acc.account_name : `Account #${accId}`,
        subLabel: acc ? acc.account_code : `ACC-${accId}`,
        amount,
      }
    }).sort((a, b) => b.amount - a.amount)
  }

  // Case B: Specific Ledger Account selected -> Group by AP Creditor Name / Vendor
  const creditorMap = new Map<string, number>()
  for (const entry of filtered) {
    const name = entry.accounts_payable?.name || entry.accounts_payable_name || entry.description || 'Unspecified Creditor'
    const current = creditorMap.get(name) || 0
    creditorMap.set(name, current + Number(entry.amount))
  }

  const selectedAccObj = accounting.ledgerAccounts.value.find(a => a.id === targetAccountId)
  const accCodeStr = selectedAccObj ? selectedAccObj.account_code : `ACC-#${targetAccountId}`

  return Array.from(creditorMap.entries()).map(([name, amount], idx) => {
    return {
      id: `creditor-${idx}-${name}`,
      label: name,
      subLabel: accCodeStr,
      amount,
    }
  }).sort((a, b) => b.amount - a.amount)
})

const processedPayables = computed(() => {
  const payables = rawPayables.value
  if (payables.length <= 6) return payables

  const top5 = payables.slice(0, 5)
  const rest = payables.slice(5)
  const othersAmount = rest.reduce((sum, item) => sum + item.amount, 0)

  return [
    ...top5,
    {
      id: 'other-items',
      label: 'Other Items',
      subLabel: 'MISC',
      amount: othersAmount,
    }
  ]
})

const totalPayablesAmount = computed(() => {
  return rawPayables.value.reduce((sum, item) => sum + item.amount, 0)
})

const chartSlices = computed(() => {
  const total = totalPayablesAmount.value
  if (total <= 0) return []

  let accumulatedDashOffset = 0
  return processedPayables.value.map((item, idx) => {
    const fraction = item.amount / total
    const dashLength = fraction * 100
    const dashOffset = accumulatedDashOffset
    accumulatedDashOffset += dashLength

    return {
      ...item,
      percentage: (fraction * 100).toFixed(1),
      color: palette[idx % palette.length],
      dashLength,
      dashOffset,
    }
  })
})

const centerLabelTitle = computed(() => {
  if (hoveredIndex.value !== null && chartSlices.value[hoveredIndex.value]) {
    return chartSlices.value[hoveredIndex.value].subLabel || chartSlices.value[hoveredIndex.value].label
  }
  return selectedAccountId.value !== null ? 'Account Total AP' : 'Total Unpaid AP'
})

const centerLabelValue = computed(() => {
  if (hoveredIndex.value !== null && chartSlices.value[hoveredIndex.value]) {
    return currencyStore.formatCurrency(chartSlices.value[hoveredIndex.value].amount)
  }
  return currencyStore.formatCurrency(totalPayablesAmount.value)
})

const centerLabelSub = computed(() => {
  if (hoveredIndex.value !== null && chartSlices.value[hoveredIndex.value]) {
    return `${chartSlices.value[hoveredIndex.value].percentage}%`
  }
  return `${chartSlices.value.length} slices`
})
</script>
