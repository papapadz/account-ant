<template>
  <div class="glass-card p-5 rounded-2xl border border-[var(--border-color)] flex flex-col justify-between space-y-4 col-span-1 h-full shadow-sm hover:shadow-md transition-all">
    <!-- Header & Filter Toolbar -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 pb-3 border-b border-[var(--border-color)]">
      <div class="w-full md:w-auto">
        <h3 class="text-sm font-bold text-[var(--text-main)] tracking-tight flex items-center gap-2">
          <div class="p-1.5 rounded-lg bg-rose-500/10 text-rose-500 border border-rose-500/20">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4" />
            </svg>
          </div>
          Accounts Payable Volume Trend
        </h3>
        <p class="text-[11px] text-[var(--text-muted)] mt-0.5">
          Cumulative total outstanding accounts payable balance over time
        </p>
      </div>

      <!-- Toolbar: Year Filter -->
      <div class="flex items-center gap-1.5 w-full sm:w-auto">
        <span class="text-[11px] font-medium text-[var(--text-muted)] shrink-0">Year:</span>
        <select
          v-model="selectedYear"
          class="text-xs bg-[var(--bg-surface-elevated)] border border-[var(--border-color)] text-[var(--text-main)] rounded-lg px-2.5 py-1 focus:outline-none focus:ring-1 focus:ring-rose-500 font-mono transition-colors"
        >
          <option v-for="yr in availableYears" :key="yr" :value="yr">
            {{ yr }}
          </option>
        </select>
      </div>
    </div>

    <!-- Summary Stats Header Bar -->
    <div class="grid grid-cols-3 gap-2 py-2 px-3 rounded-xl bg-[var(--bg-surface-elevated)]/60 border border-[var(--border-color)]/60 text-xs">
      <div>
        <div class="text-[10px] uppercase font-semibold text-[var(--text-muted)] tracking-wider">
          Peak AP Balance
        </div>
        <div class="font-mono font-bold text-rose-500 mt-0.5">
          {{ currencyStore.formatCurrency(periodTotalAmount) }}
        </div>
      </div>
      <div>
        <div class="text-[10px] uppercase font-semibold text-[var(--text-muted)] tracking-wider">Peak Month</div>
        <div class="font-semibold text-[var(--text-main)] mt-0.5 truncate">
          {{ peakMonthLabel }}
        </div>
      </div>
      <div>
        <div class="text-[10px] uppercase font-semibold text-[var(--text-muted)] tracking-wider">Unpaid Items</div>
        <div class="font-mono font-semibold text-rose-400 mt-0.5">
          {{ totalUnpaidCount }} entries
        </div>
      </div>
    </div>

    <!-- Chart Visualization Container -->
    <div class="flex-1 w-full flex flex-col min-h-[220px]">
      <div class="relative w-full flex-1 pt-4 pb-8 px-4 rounded-xl bg-[var(--bg-surface)] border border-[var(--border-color)] overflow-hidden flex flex-col justify-between group">
        <!-- Background Grid Lines -->
        <div class="absolute inset-0 flex flex-col justify-between p-4 pointer-events-none opacity-20">
          <div class="border-b border-[var(--border-color)] border-dashed w-full"></div>
          <div class="border-b border-[var(--border-color)] border-dashed w-full"></div>
          <div class="border-b border-[var(--border-color)] border-dashed w-full"></div>
          <div class="border-b border-[var(--border-color)] border-dashed w-full"></div>
        </div>

        <!-- SVG Line & Area Chart -->
        <svg viewBox="0 0 600 180" class="w-full h-full overflow-visible" preserveAspectRatio="none">
          <defs>
            <linearGradient id="ap-area-gradient" x1="0" y1="0" x2="0" y2="1">
              <stop offset="0%" stop-color="#f43f5e" stop-opacity="0.35" />
              <stop offset="75%" stop-color="#f43f5e" stop-opacity="0.08" />
              <stop offset="100%" stop-color="#f43f5e" stop-opacity="0.0" />
            </linearGradient>
            <filter id="ap-glow" x="-20%" y="-20%" width="140%" height="140%">
              <feGaussianBlur stdDeviation="2.5" result="blur" />
              <feComposite in="SourceGraphic" in2="blur" operator="over" />
            </filter>
          </defs>

          <!-- Area Gradient Fill -->
          <path
            v-if="chartPoints.length > 0"
            :d="areaPath"
            fill="url(#ap-area-gradient)"
            class="transition-all duration-300 ease-out"
          />

          <!-- Main Curve Line -->
          <path
            v-if="chartPoints.length > 0"
            :d="linePath"
            fill="none"
            stroke="#f43f5e"
            stroke-width="2.5"
            stroke-linecap="round"
            stroke-linejoin="round"
            filter="url(#ap-glow)"
            class="transition-all duration-300 ease-out"
          />

          <!-- Data Points & Hover Targets -->
          <g v-for="(point, idx) in chartPoints" :key="idx" class="cursor-pointer">
            <!-- Pulsing Ring on Hover -->
            <circle
              v-if="hoveredIndex === idx"
              :cx="point.x"
              :cy="point.y"
              r="7"
              fill="none"
              stroke="#f43f5e"
              stroke-width="1.5"
              class="animate-ping opacity-75"
            />
            <!-- Solid Circle Point -->
            <circle
              :cx="point.x"
              :cy="point.y"
              :r="hoveredIndex === idx ? '5' : '3.5'"
              :fill="hoveredIndex === idx ? '#f43f5e' : 'var(--bg-surface)'"
              stroke="#f43f5e"
              :stroke-width="hoveredIndex === idx ? '2.5' : '2'"
              class="transition-all duration-150"
            />
            <!-- Transparent Hover Trigger Area -->
            <rect
              :x="point.x - 20"
              y="0"
              width="40"
              height="180"
              fill="transparent"
              @mouseenter="hoveredIndex = idx"
              @mouseleave="hoveredIndex = null"
            />
          </g>
        </svg>

        <!-- Floating Glassmorphism Tooltip -->
        <div
          v-if="hoveredPoint"
          class="absolute z-20 pointer-events-none transform -translate-x-1/2 -translate-y-full mb-3 px-3 py-2 rounded-xl bg-[var(--bg-surface-elevated)]/95 backdrop-blur-md border border-rose-500/30 shadow-xl text-[var(--text-main)] transition-all duration-150 ease-out"
          :style="{ left: `${(hoveredPoint.x / 600) * 100}%`, top: `${(hoveredPoint.y / 180) * 100}%` }"
        >
          <div class="text-[10px] text-rose-400 font-bold uppercase tracking-wider mb-0.5 flex items-center gap-1">
            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
            {{ hoveredPoint.month }} {{ selectedYear }}
          </div>
          <div class="text-xs font-mono font-bold text-[var(--text-main)]">
            {{ currencyStore.formatCurrency(hoveredPoint.amount) }}
          </div>
          <div class="text-[10px] text-[var(--text-muted)] mt-1 flex items-center justify-between gap-3">
            <span>Total Outstanding AP</span>
            <span class="font-mono text-rose-400 font-semibold">{{ hoveredPoint.count }} {{ hoveredPoint.count === 1 ? 'entry' : 'entries' }}</span>
          </div>
        </div>

        <!-- X-Axis Month Labels -->
        <div class="flex justify-between items-center px-1 pt-2 border-t border-[var(--border-color)]/60 text-[10px] font-mono text-[var(--text-muted)]">
          <span
            v-for="(m, idx) in monthsList"
            :key="m"
            :class="{ 'text-rose-400 font-bold': hoveredIndex === idx }"
            class="transition-colors duration-150"
          >
            {{ m }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useCurrency } from '@/composables/useCurrency'
import { useAccounting } from '@/composables/useAccounting'

const currencyStore = useCurrency()
const accounting = useAccounting()

const selectedYear = ref<number>(2026)
const hoveredIndex = ref<number | null>(null)

const monthsList = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']

// Available years extracted from journal entries
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

// Calculate monthly incurred unpaid AP amounts & entry counts
const rawMonthlyIncurred = computed(() => {
  const result = monthsList.map((month, idx) => ({
    month,
    monthIndex: idx,
    incurredAmount: 0,
    count: 0,
  }))

  const targetYear = selectedYear.value

  for (const entry of accounting.journalEntries.value) {
    // Only unpaid accounts payable
    if (entry.is_paid !== false) continue

    const dateStr = entry.posting_date || entry.created_at
    if (!dateStr) continue

    const d = new Date(dateStr)
    if (isNaN(d.getTime())) continue

    if (d.getFullYear() === targetYear) {
      const mIdx = d.getMonth()
      if (mIdx >= 0 && mIdx < 12) {
        result[mIdx].incurredAmount += Number(entry.amount || 0)
        result[mIdx].count += 1
      }
    }
  }

  return result
})

// Calculate prior years' carried-over unpaid AP balance
const priorCarriedOverBalance = computed(() => {
  const targetYear = selectedYear.value
  let carried = 0

  for (const entry of accounting.journalEntries.value) {
    if (entry.is_paid !== false) continue

    const dateStr = entry.posting_date || entry.created_at
    if (!dateStr) continue

    const d = new Date(dateStr)
    if (!isNaN(d.getTime()) && d.getFullYear() < targetYear) {
      carried += Number(entry.amount || 0)
    }
  }

  return carried
})

// Monthly data calculation strictly as Cumulative Total Outstanding AP
const monthlyData = computed(() => {
  let runningTotal = priorCarriedOverBalance.value
  let runningCount = 0

  return rawMonthlyIncurred.value.map(item => {
    runningTotal += item.incurredAmount
    runningCount += item.count
    return {
      month: item.month,
      monthIndex: item.monthIndex,
      amount: runningTotal,
      count: runningCount,
    }
  })
})

// Total period peak volume amount
const periodTotalAmount = computed(() => {
  const amounts = monthlyData.value.map(m => m.amount)
  return Math.max(...amounts, 0)
})

// Total count of active unpaid items for target year
const totalUnpaidCount = computed(() => {
  return rawMonthlyIncurred.value.reduce((sum, m) => sum + m.count, 0)
})

// Month with peak AP volume
const peakMonthLabel = computed(() => {
  const max = [...monthlyData.value].sort((a, b) => b.amount - a.amount)[0]
  if (!max || max.amount === 0) return 'None'
  return `${max.month} (${currencyStore.formatCurrency(max.amount)})`
})

// Calculate SVG coordinates (chart ViewBox: 600 x 180)
const chartPoints = computed(() => {
  const data = monthlyData.value
  const maxVal = Math.max(...data.map(d => d.amount), 100)
  const width = 600
  const height = 130
  const offsetY = 25
  const paddingX = 30
  const stepX = (width - paddingX * 2) / (data.length - 1)

  return data.map((item, idx) => {
    const x = paddingX + idx * stepX
    const normalizedY = (item.amount / maxVal) * height
    const y = height + offsetY - normalizedY
    return {
      x,
      y,
      amount: item.amount,
      count: item.count,
      month: item.month,
    }
  })
})

// Smooth cubic Bezier line path calculation
const linePath = computed(() => {
  const points = chartPoints.value
  if (points.length === 0) return ''
  if (points.length === 1) return `M ${points[0].x} ${points[0].y}`

  let d = `M ${points[0].x} ${points[0].y}`
  for (let i = 0; i < points.length - 1; i++) {
    const p0 = points[i]
    const p1 = points[i + 1]
    const controlX = (p0.x + p1.x) / 2
    d += ` C ${controlX} ${p0.y}, ${controlX} ${p1.y}, ${p1.x} ${p1.y}`
  }
  return d
})

// Area path for gradient fill under the line curve
const areaPath = computed(() => {
  const points = chartPoints.value
  if (points.length === 0) return ''
  const first = points[0]
  const last = points[points.length - 1]
  return `${linePath.value} L ${last.x} 165 L ${first.x} 165 Z`
})

// Hovered point details
const hoveredPoint = computed(() => {
  if (hoveredIndex.value === null) return null
  return chartPoints.value[hoveredIndex.value] || null
})
</script>
