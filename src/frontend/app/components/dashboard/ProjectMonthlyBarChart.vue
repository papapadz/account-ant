<template>
  <div class="glass-card p-5 rounded-2xl border border-[var(--border-color)] flex flex-col justify-between space-y-4">
    <!-- Header & Project Selector -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-3 border-b border-[var(--border-color)]">
      <div>
        <h3 class="text-sm font-bold text-[var(--text-main)] tracking-tight flex items-center gap-2">
          <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
          </svg>
          Monthly Project Expense
        </h3>
        <p class="text-[11px] text-[var(--text-muted)] mt-0.5">Monthly debit breakdown for selected project</p>
      </div>

      <select
        v-model="selectedProjectId"
        class="input-field text-xs !py-1.5 !px-2.5 max-w-xs font-medium"
      >
        <option v-for="project in projectsStore.projects.value" :key="project.id" :value="project.id">
          {{ project.name }}
        </option>
      </select>
    </div>

    <!-- Monthly Bar Chart Component -->
    <div class="space-y-4">
      <!-- Top Stats Row -->
      <div class="flex items-center justify-between text-xs text-[var(--text-muted)] px-1">
        <div>
          Selected Project Spend:
          <span class="font-mono font-bold text-blue-400 ml-1">{{ currencyStore.formatCurrency(totalProjectExpense) }}</span>
        </div>
        <div>
          Peak Month:
          <span class="font-mono font-bold text-amber-400 ml-1">{{ peakMonthLabel }}</span>
        </div>
      </div>

      <!-- Bar Columns Grid -->
      <div class="h-44 flex items-end justify-between gap-1.5 pt-6 pb-2 px-2 rounded-xl bg-[var(--bg-surface)] border border-[var(--border-color)] relative">
        <!-- Horizontal Grid Lines -->
        <div class="absolute inset-x-0 top-1/4 border-b border-[var(--border-color)]/30 pointer-events-none"></div>
        <div class="absolute inset-x-0 top-2/4 border-b border-[var(--border-color)]/30 pointer-events-none"></div>
        <div class="absolute inset-x-0 top-3/4 border-b border-[var(--border-color)]/30 pointer-events-none"></div>

        <div
          v-for="bar in monthlyBars"
          :key="bar.month"
          class="flex-1 flex flex-col items-center h-full justify-end group relative"
        >
          <!-- Tooltip Hover Popover -->
          <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-150 absolute -top-10 bg-slate-900 text-white text-[10px] font-mono px-2 py-1 rounded shadow-lg border border-slate-700 pointer-events-none whitespace-nowrap z-20">
            {{ bar.month }}: {{ currencyStore.formatCurrency(bar.amount) }}
          </div>

          <!-- Bar Column -->
          <div
            class="w-full max-w-[28px] rounded-t-md transition-all duration-300 relative group-hover:brightness-125"
            :class="bar.amount > 0 ? 'bg-gradient-to-t from-blue-600 to-emerald-400 border-t border-emerald-300/40' : 'bg-slate-700/20'"
            :style="{ height: `${Math.max(bar.heightPercent, 4)}%` }"
          ></div>

          <!-- Month Label -->
          <span class="text-[10px] font-mono text-[var(--text-muted)] mt-2 font-medium">
            {{ bar.month }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const currencyStore = useCurrency()
const projectsStore = useProjects()

const selectedProjectId = ref<number>(1)

const monthlyData = computed(() => {
  return projectsStore.getProjectMonthlyExpenses(selectedProjectId.value)
})

const totalProjectExpense = computed(() => {
  return monthlyData.value.reduce((sum: number, m: any) => sum + m.amount, 0)
})

const maxMonthlyAmount = computed(() => {
  const max = Math.max(...monthlyData.value.map((m: any) => m.amount), 0)
  return max > 0 ? max : 1
})

const peakMonthLabel = computed(() => {
  const sorted = [...monthlyData.value].sort((a: any, b: any) => b.amount - a.amount)
  if (!sorted[0] || sorted[0].amount <= 0) return 'N/A'
  return `${sorted[0].month} (${currencyStore.formatCurrency(sorted[0].amount)})`
})

const monthlyBars = computed(() => {
  const max = maxMonthlyAmount.value
  return monthlyData.value.map((m: any) => ({
    ...m,
    heightPercent: Math.round((m.amount / max) * 100),
  }))
})

</script>
