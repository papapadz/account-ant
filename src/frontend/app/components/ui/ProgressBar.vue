<template>
  <div class="w-full space-y-1">
    <div v-if="showLabel" class="flex justify-between items-center text-xs">
      <span class="text-[var(--text-muted)] font-medium">{{ label || 'Usage' }}</span>
      <span class="font-mono font-semibold" :class="textColorClass">{{ percentage }}%</span>
    </div>
    <div class="w-full bg-[var(--bg-surface)] border border-[var(--border-color)] rounded-full h-2 overflow-hidden p-0.5">
      <div
        class="h-full rounded-full transition-all duration-500 ease-out"
        :class="barColorClass"
        :style="{ width: `${clampedPercentage}%` }"
      ></div>
    </div>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{
  percentage: number
  label?: string
  showLabel?: boolean
}>()

const clampedPercentage = computed(() => {
  if (isNaN(props.percentage) || props.percentage < 0) return 0
  return Math.min(props.percentage, 100)
})

const barColorClass = computed(() => {
  if (clampedPercentage.value >= 90) return 'bg-rose-500'
  if (clampedPercentage.value >= 75) return 'bg-amber-500'
  return 'bg-emerald-500'
})

const textColorClass = computed(() => {
  if (clampedPercentage.value >= 90) return 'text-rose-400'
  if (clampedPercentage.value >= 75) return 'text-amber-400'
  return 'text-emerald-400'
})
</script>
