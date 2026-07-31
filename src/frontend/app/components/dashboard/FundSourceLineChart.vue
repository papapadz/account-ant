<template>
  <div class="glass-card p-5 rounded-2xl border border-[var(--border-color)] flex flex-col justify-between space-y-4 col-span-1 lg:col-span-2">
    <!-- Header & Filter Toolbar -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 pb-3 border-b border-[var(--border-color)]">
      <div>
        <h3 class="text-sm font-bold text-[var(--text-main)] tracking-tight flex items-center gap-2">
          <svg class="w-4.5 h-4.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
          </svg>
          Fund Source Amount Trends (Jan – Dec)
        </h3>
        <p class="text-[11px] text-[var(--text-muted)] mt-0.5">
          Monthly fund allocation and balance movement across the selected fiscal year
        </p>
      </div>

      <!-- Filters: Per Fund Source Account & Per Year -->
      <div class="flex flex-wrap items-center gap-2.5 w-full md:w-auto">
        <!-- Fund Account Filter -->
        <div class="flex items-center gap-1.5 min-w-[160px] flex-1 md:flex-none">
          <span class="text-[11px] font-medium text-[var(--text-muted)] shrink-0">Account:</span>
          <select
            v-model="selectedFundId"
            class="input-field text-xs !py-1.5 !px-2.5 w-full font-medium"
          >
            <option value="all">All Fund Accounts</option>
            <option
              v-for="fund in accounting.fundAccounts.value"
              :key="fund.id"
              :value="fund.id"
            >
              {{ fund.fund_code }} - {{ fund.fund_name }}
            </option>
          </select>
        </div>

        <!-- Year Filter -->
        <div class="flex items-center gap-1.5 min-w-[110px]">
          <span class="text-[11px] font-medium text-[var(--text-muted)] shrink-0">Year:</span>
          <select
            v-model="selectedYear"
            class="input-field text-xs !py-1.5 !px-2.5 w-full font-mono font-medium"
          >
            <option v-for="yr in availableYears" :key="yr" :value="yr">
              {{ yr }}
            </option>
          </select>
        </div>
      </div>
    </div>

    <!-- Stats Summary Row -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs bg-[var(--bg-surface)]/60 p-3 rounded-xl border border-[var(--border-color)]/70">
      <div>
        <span class="text-[10px] uppercase font-semibold text-[var(--text-muted)] tracking-wider block">Total Period Volume</span>
        <span class="font-mono font-bold text-emerald-400 text-sm">{{ currencyStore.formatCurrency(periodTotalAmount) }}</span>
      </div>
      <div>
        <span class="text-[10px] uppercase font-semibold text-[var(--text-muted)] tracking-wider block">Peak Month</span>
        <span class="font-mono font-bold text-amber-400 text-sm">{{ peakMonthLabel }}</span>
      </div>
      <div>
        <span class="text-[10px] uppercase font-semibold text-[var(--text-muted)] tracking-wider block">Monthly Average</span>
        <span class="font-mono font-bold text-blue-400 text-sm">{{ currencyStore.formatCurrency(monthlyAverage) }}</span>
      </div>
      <div>
        <span class="text-[10px] uppercase font-semibold text-[var(--text-muted)] tracking-wider block">Active Accounts</span>
        <span class="font-mono font-bold text-[var(--text-main)] text-sm">{{ activeAccountsCount }} Sources</span>
      </div>
    </div>

    <!-- Line Chart Display -->
    <div class="space-y-2">
      <div class="relative w-full h-56 pt-6 pb-8 px-4 rounded-xl bg-[var(--bg-surface)] border border-[var(--border-color)] overflow-hidden flex flex-col justify-between select-none">
        <!-- SVG Grid & Line Chart -->
        <svg viewBox="0 0 600 180" class="w-full h-full overflow-visible" preserveAspectRatio="none">
          <defs>
            <!-- Area Gradient Fill -->
            <linearGradient id="fundLineGradient" x1="0" y1="0" x2="0" y2="1">
              <stop offset="0%" stop-color="#10b981" stop-opacity="0.35" />
              <stop offset="100%" stop-color="#10b981" stop-opacity="0.0" />
            </linearGradient>
          </defs>

          <!-- Horizontal Grid Lines -->
          <line x1="0" y1="30" x2="600" y2="30" stroke="var(--border-color)" stroke-dasharray="4,4" opacity="0.4" />
          <line x1="0" y1="80" x2="600" y2="80" stroke="var(--border-color)" stroke-dasharray="4,4" opacity="0.4" />
          <line x1="0" y1="130" x2="600" y2="130" stroke="var(--border-color)" stroke-dasharray="4,4" opacity="0.4" />

          <!-- Filled Area Under Curve -->
          <path
            v-if="chartPoints.length > 0"
            :d="areaPath"
            fill="url(#fundLineGradient)"
            class="transition-all duration-500"
          />

          <!-- Line Stroke Curve -->
          <path
            v-if="chartPoints.length > 0"
            :d="linePath"
            fill="none"
            stroke="#10b981"
            stroke-width="3"
            stroke-linecap="round"
            stroke-linejoin="round"
            class="transition-all duration-500"
          />

          <!-- Interactive Data Points / Nodes -->
          <g v-for="(point, idx) in chartPoints" :key="idx">
            <circle
              :cx="point.x"
              :cy="point.y"
              r="4.5"
              fill="#10b981"
              stroke="var(--bg-surface)"
              stroke-width="2"
              class="transition-all duration-200 hover:r-7 hover:fill-emerald-300 cursor-pointer"
              @mouseenter="hoveredIndex = idx"
              @mouseleave="hoveredIndex = null"
            />
          </g>
        </svg>

        <!-- Hover Tooltip Floating Card -->
        <div
          v-if="hoveredPoint"
          class="absolute z-20 pointer-events-none transform -translate-x-1/2 -translate-y-full mb-2 bg-slate-900/95 text-white p-2 rounded-lg shadow-xl border border-slate-700/80 text-xs font-mono transition-all duration-150"
          :style="{ left: `${(hoveredPoint.x / 600) * 100}%`, top: `${(hoveredPoint.y / 180) * 100}%` }"
        >
          <div class="text-[10px] text-emerald-400 font-bold uppercase tracking-wider mb-0.5">{{ hoveredPoint.month }} {{ selectedYear }}</div>
          <div class="text-xs font-bold">{{ currencyStore.formatCurrency(hoveredPoint.amount) }}</div>
        </div>

        <!-- Month X-Axis Labels -->
        <div class="flex justify-between items-center px-1 pt-2 border-t border-[var(--border-color)]/60 text-[10px] font-mono text-[var(--text-muted)]">
          <span v-for="m in monthsList" :key="m" class="font-medium hover:text-[var(--text-main)] transition-colors">
            {{ m }}
          </span>
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
const hoveredIndex = ref<number | null>(null)

const monthsList = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']

const availableYears = computed(() => {
  const yearsSet = new Set<number>([2026, 2025, 2024])
  for (const f of accounting.fundAccounts.value) {
    if (f.created_at) {
      const yr = new Date(f.created_at).getFullYear()
      if (!isNaN(yr)) yearsSet.add(yr)
    }
  }
  for (const t of projectsStore.transactions.value) {
    const txDateStr = t.posting_date || t.date
    if (txDateStr) {
      const yr = new Date(txDateStr).getFullYear()
      if (!isNaN(yr)) yearsSet.add(yr)
    }
  }
  return Array.from(yearsSet).sort((a, b) => b - a)
})

const activeAccountsCount = computed(() => {
  if (selectedFundId.value === 'all') return accounting.fundAccounts.value.length || 1
  return 1
})

const monthlyData = computed(() => {
  const result = monthsList.map((month, idx) => ({ month, monthIndex: idx, amount: 0, netChange: 0 }))

  const targetYear = selectedYear.value
  const targetFund = selectedFundId.value

  let priorBaseline = 0

  // 1. Initial creation allocation amount
  for (const fund of accounting.fundAccounts.value) {
    if (targetFund !== 'all' && fund.id !== targetFund) continue
    const dateStr = fund.created_at || `${targetYear}-01-01`
    const fundDate = new Date(dateStr)
    const yr = fundDate.getFullYear()
    const amt = Number(fund.amount || 0)
    if (yr < targetYear) {
      priorBaseline += amt
    } else if (yr === targetYear) {
      const mIdx = fundDate.getMonth()
      if (mIdx >= 0 && mIdx < 12) {
        result[mIdx].netChange += amt
      }
    }
  }

  // 2. Journal debits and credits to the account
  for (const tx of projectsStore.transactions.value) {
    const txDateStr = tx.posting_date || tx.date
    if (!txDateStr) continue
    if (targetFund !== 'all' && tx.fund_source_id !== targetFund) continue
    const txDate = new Date(txDateStr)
    const yr = txDate.getFullYear()
    const amt = Number(tx.amount || 0)
    const delta = tx.type === 'debit' ? -amt : amt

    if (yr < targetYear) {
      priorBaseline += delta
    } else if (yr === targetYear) {
      const mIdx = txDate.getMonth()
      if (mIdx >= 0 && mIdx < 12) {
        result[mIdx].netChange += delta
      }
    }
  }

  // 3. Compute running cumulative balance trajectory (Jan – Dec)
  let currentRunningBalance = priorBaseline
  let hasRealActivity = priorBaseline !== 0

  for (let i = 0; i < 12; i++) {
    if (result[i].netChange !== 0) hasRealActivity = true
    currentRunningBalance += result[i].netChange
    result[i].amount = currentRunningBalance
  }

  // if (!hasRealActivity) {
  //   const baseVal = targetFund === 'all' ? 1200000 : 350000
  //   const dummyMultipliers = [0.45, 0.52, 0.60, 0.58, 0.72, 0.85, 0.78, 0.90, 0.95, 1.10, 1.05, 1.25]
  //   return result.map((item, idx) => ({
  //     ...item,
  //     amount: Math.round(baseVal * dummyMultipliers[idx])
  //   }))
  // }

  return result
})

const periodTotalAmount = computed(() => {
  return monthlyData.value.reduce((sum, item) => sum + item.amount, 0)
})

const monthlyAverage = computed(() => {
  return Math.round(periodTotalAmount.value / 12)
})

const peakMonthLabel = computed(() => {
  const sorted = [...monthlyData.value].sort((a, b) => b.amount - a.amount)
  if (!sorted[0] || sorted[0].amount <= 0) return 'N/A'
  return `${sorted[0].month} (${currencyStore.formatCurrency(sorted[0].amount)})`
})

const chartPoints = computed(() => {
  const data = monthlyData.value
  const maxVal = Math.max(...data.map(d => d.amount), 1)
  const minVal = Math.min(...data.map(d => d.amount), 0)
  const range = maxVal - minVal || 1

  const width = 600
  const height = 180
  const paddingX = 20
  const paddingY = 25

  const usableWidth = width - paddingX * 2
  const usableHeight = height - paddingY * 2

  return data.map((item, idx) => {
    const x = paddingX + (idx / (data.length - 1)) * usableWidth
    const normalizedY = (item.amount - minVal) / range
    const y = height - paddingY - (normalizedY * usableHeight)
    return {
      x: Math.round(x * 10) / 10,
      y: Math.round(y * 10) / 10,
      month: item.month,
      amount: item.amount,
    }
  })
})

const linePath = computed(() => {
  const points = chartPoints.value
  if (points.length === 0) return ''
  let d = `M ${points[0].x} ${points[0].y}`
  for (let i = 1; i < points.length; i++) {
    const prev = points[i - 1]
    const curr = points[i]
    const cx = (prev.x + curr.x) / 2
    d += ` C ${cx} ${prev.y}, ${cx} ${curr.y}, ${curr.x} ${curr.y}`
  }
  return d
})

const areaPath = computed(() => {
  const points = chartPoints.value
  if (points.length === 0) return ''
  const first = points[0]
  const last = points[points.length - 1]
  return `${linePath.value} L ${last.x} 165 L ${first.x} 165 Z`
})

const hoveredPoint = computed(() => {
  if (hoveredIndex.value === null) return null
  return chartPoints.value[hoveredIndex.value] || null
})
</script>
