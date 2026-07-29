<template>
  <div class="glass-card p-5 rounded-2xl border border-[var(--border-color)] flex flex-col justify-between space-y-4">
    <!-- Header & Fund Selector -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-3 border-b border-[var(--border-color)]">
      <div>
        <h3 class="text-sm font-bold text-[var(--text-main)] tracking-tight flex items-center gap-2">
          <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
          </svg>
          Fund Project Expenses
        </h3>
        <p class="text-[11px] text-[var(--text-muted)] mt-0.5">Project expense breakdown for selected fund source</p>
      </div>

      <select
        v-model="selectedFundId"
        class="input-field text-xs !py-1.5 !px-2.5 max-w-xs font-medium"
      >
        <option v-for="fund in accounting.fundAccounts.value" :key="fund.id" :value="fund.id">
          {{ fund.fund_code }} - {{ fund.fund_name }}
        </option>
      </select>
    </div>

    <!-- Donut Chart & Legend -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
      <!-- SVG Donut Graphic -->
      <div class="relative flex items-center justify-center p-2">
        <svg viewBox="0 0 100 100" class="w-44 h-44 transform -rotate-90">
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
            class="transition-all duration-500 hover:opacity-80 cursor-pointer"
          />
        </svg>

        <!-- Donut Center Label -->
        <div class="absolute inset-0 flex flex-col items-center justify-center text-center pointer-events-none">
          <span class="text-[10px] uppercase font-bold text-[var(--text-muted)] tracking-wider">Total Fund Spent</span>
          <span class="text-sm font-bold font-mono text-emerald-400">{{ currencyStore.formatCurrency(totalFundExpense) }}</span>
        </div>
      </div>

      <!-- Legend & Project Breakdown List -->
      <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
        <div
          v-for="(item, idx) in chartSlices"
          :key="item.projectId"
          class="flex items-center justify-between p-2 rounded-lg bg-[var(--bg-surface)] border border-[var(--border-color)] text-xs"
        >
          <div class="flex items-center gap-2 overflow-hidden mr-2">
            <span class="w-2.5 h-2.5 rounded-full shrink-0" :style="{ backgroundColor: item.color }"></span>
            <span class="font-semibold text-[var(--text-main)] truncate" :title="item.projectName">{{ item.projectName }}</span>
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

        <div v-if="chartSlices.length === 0" class="text-center py-6 text-xs text-[var(--text-muted)] italic">
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

const selectedFundId = ref<number>(1)

const palette = ['#10B981', '#3B82F6', '#F59E0B', '#8B5CF6', '#F43F5E', '#06B6D4', '#EC4899', '#6366F1']

const syncDefaultFund = () => {
  const funds = accounting.fundAccounts.value
  if (funds.length > 0 && !funds.some(f => f.id === selectedFundId.value)) {
    selectedFundId.value = funds[0].id
  }
}

onMounted(async () => {
  if (accounting.fundAccounts.value.length === 0) {
    await accounting.fetchFundAccounts()
  }
  syncDefaultFund()
})

watch(() => accounting.fundAccounts.value, () => {
  syncDefaultFund()
}, { deep: true })

const rawExpenses = computed(() => {
  return projectsStore.getFundProjectExpenses(selectedFundId.value)
})

const totalFundExpense = computed(() => {
  return rawExpenses.value.reduce((sum: number, e: any) => sum + e.amount, 0)
})

const chartSlices = computed(() => {
  const total = totalFundExpense.value
  let currentOffset = 0

  return rawExpenses.value.map((item: any, idx: number) => {
    const fraction = total > 0 ? item.amount / total : 0
    const dashLength = fraction * 100
    const dashOffset = currentOffset
    currentOffset += dashLength

    return {
      ...item,
      color: palette[idx % palette.length],
      dashLength,
      dashOffset,
    }
  })
})
</script>
