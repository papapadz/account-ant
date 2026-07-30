<template>
  <div class="w-full space-y-1">
    <div
      class="w-full bg-[var(--bg-surface)] border border-[var(--border-color)] rounded-full overflow-hidden p-0.5"
      :class="trackHeightClass"
      role="progressbar"
      :aria-label="ariaLabel ?? label ?? 'Progress'"
      :aria-valuemin="0"
      :aria-valuemax="indeterminate ? undefined : 100"
      :aria-valuenow="indeterminate ? undefined : clampedPercentage"
      :aria-valuetext="indeterminate ? 'Loading' : `${clampedPercentage}%`"
    >
      <div
        class="h-full rounded-full"
        :class="[
          indeterminate ? 'w-1/3 animate-[indeterminate_1.4s_ease-in-out_infinite]' : motionSafeTransition,
          barColorClass,
        ]"
        :style="indeterminate ? undefined : { width: `${clampedPercentage}%` }"
      ></div>
    </div>
  </div>
</template>

<script lang="ts">
export type ProgressSize = 'sm' | 'md' | 'lg'

export interface ProgressThreshold {
  /** Minimum percentage (inclusive) at which this tier applies. */
  min: number
  bar: string
  text: string
}

export const DEFAULT_THRESHOLDS: ProgressThreshold[] = [
  { min: 90, bar: 'bg-rose-500', text: 'text-rose-400' },
  { min: 75, bar: 'bg-amber-500', text: 'text-amber-400' },
  { min: 0, bar: 'bg-emerald-500', text: 'text-emerald-400' },
]

export const SIZE_CLASSES: Record<ProgressSize, string> = {
  sm: 'h-1.5',
  md: 'h-2',
  lg: 'h-3',
}
</script>

<script setup lang="ts">
import { computed, ref, watch, onMounted, onBeforeUnmount } from 'vue'

const props = withDefaults(
  defineProps<{
    /** Current value, 0-100. Ignored while `indeterminate` is true. */
    percentage?: number
    /** Alias for percentage. */
    value?: number
    label?: string
    showLabel?: boolean
    /** Accessible name when no visible label is present. */
    ariaLabel?: string
    size?: ProgressSize
    /** Suffix shown after the number, e.g. '%' or ' GB'. */
    unit?: string
    /** Show a sweeping bar instead of a fixed fill, for unknown-duration loads. */
    indeterminate?: boolean
    /** Animate the displayed number counting up/down to the new value. */
    animateValue?: boolean
    /** Override automatic color tiers, e.g. [{min: 0, bar: 'bg-sky-500', text: 'text-sky-400'}]. */
    thresholds?: ProgressThreshold[]
  }>(),
  {
    showLabel: true,
    unit: '%',
    indeterminate: false,
    animateValue: true,
    size: 'md',
    thresholds: () => DEFAULT_THRESHOLDS,
  }
)

const rawPercentage = computed(() => props.percentage ?? props.value ?? 0)

const clampedPercentage = computed(() => {
  const val = rawPercentage.value
  if (Number.isNaN(val) || val < 0) return 0
  return Math.min(Math.round(val), 100)
})

const trackHeightClass = computed(() => SIZE_CLASSES[props.size])

const activeThreshold = computed(() => {
  const sorted = [...props.thresholds].sort((a, b) => b.min - a.min)
  return (
    sorted.find((t) => clampedPercentage.value >= t.min) ??
    sorted[sorted.length - 1] ??
    DEFAULT_THRESHOLDS[DEFAULT_THRESHOLDS.length - 1]
  )
})

const barColorClass = computed(() => activeThreshold.value.bar)
const textColorClass = computed(() => activeThreshold.value.text)

// Respect prefers-reduced-motion for the width transition.
const prefersReducedMotion = ref(false)
let mediaQuery: MediaQueryList | undefined
onMounted(() => {
  mediaQuery = window.matchMedia('(prefers-reduced-motion: reduce)')
  prefersReducedMotion.value = mediaQuery.matches
  mediaQuery.addEventListener('change', updateMotionPreference)
})
onBeforeUnmount(() => {
  mediaQuery?.removeEventListener('change', updateMotionPreference)
})
function updateMotionPreference(e: MediaQueryListEvent) {
  prefersReducedMotion.value = e.matches
}
const motionSafeTransition = computed(() =>
  prefersReducedMotion.value ? '' : 'transition-all duration-500 ease-out'
)

// Optional count-up/down animation for the displayed number.
const displayValue = ref(clampedPercentage.value)
let rafId: number | undefined
watch(clampedPercentage, (next, prev) => {
  if (!props.animateValue || prefersReducedMotion.value) {
    displayValue.value = next
    return
  }
  if (rafId) cancelAnimationFrame(rafId)
  const start = prev ?? next
  const duration = 400
  const startTime = performance.now()
  const step = (now: number) => {
    const t = Math.min((now - startTime) / duration, 1)
    displayValue.value = Math.round(start + (next - start) * t)
    if (t < 1) rafId = requestAnimationFrame(step)
  }
  rafId = requestAnimationFrame(step)
})
onBeforeUnmount(() => {
  if (rafId) cancelAnimationFrame(rafId)
})
</script>

<style scoped>
@keyframes indeterminate {
  0% {
    transform: translateX(-100%);
  }
  100% {
    transform: translateX(300%);
  }
}
</style>