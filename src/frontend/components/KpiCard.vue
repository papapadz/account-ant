<template>
  <div class="glass-card rounded-xl p-5 border border-[#1E293B] relative overflow-hidden group">
    <!-- Accent background subtle gradient -->
    <div
      class="absolute -right-6 -top-6 w-24 h-24 rounded-full opacity-10 blur-xl transition-all duration-300 group-hover:opacity-25"
      :class="accentClass"
    ></div>

    <div class="flex items-center justify-between mb-3">
      <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ title }}</span>
      <div
        class="w-9 h-9 rounded-lg flex items-center justify-center border transition-colors"
        :class="iconBgClass"
      >
        <slot name="icon">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </slot>
      </div>
    </div>

    <div class="flex items-baseline gap-2">
      <span class="text-2xl font-bold tracking-tight font-mono-num text-slate-100">{{ value }}</span>
      <span v-if="change" class="text-xs font-medium px-1.5 py-0.5 rounded" :class="changeIsPositive ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20'">
        {{ changeIsPositive ? '+' : '' }}{{ change }}
      </span>
    </div>

    <p class="text-xs text-slate-500 mt-2 flex items-center gap-1">
      <span>{{ subtitle }}</span>
    </p>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{
  title: string
  value: string
  subtitle?: string
  change?: string
  changeIsPositive?: boolean
  type?: 'emerald' | 'blue' | 'amber' | 'rose'
}>()

const accentClass = computed(() => {
  switch (props.type) {
    case 'emerald': return 'bg-emerald-500'
    case 'blue': return 'bg-blue-500'
    case 'amber': return 'bg-amber-500'
    case 'rose': return 'bg-rose-500'
    default: return 'bg-emerald-500'
  }
})

const iconBgClass = computed(() => {
  switch (props.type) {
    case 'emerald': return 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400'
    case 'blue': return 'bg-blue-500/10 border-blue-500/20 text-blue-400'
    case 'amber': return 'bg-amber-500/10 border-amber-500/20 text-amber-400'
    case 'rose': return 'bg-rose-500/10 border-rose-500/20 text-rose-400'
    default: return 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400'
  }
})
</script>
