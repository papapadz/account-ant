<template>
  <span
    class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wider border shrink-0 select-none"
    :class="badgeClasses"
  >
    <span class="w-1.5 h-1.5 rounded-full" :class="dotClasses"></span>
    <slot>{{ statusLabel }}</slot>
  </span>
</template>

<script setup lang="ts">
const props = defineProps<{
  status?: 'active' | 'on-hold' | 'completed' | 'archived' | 'emerald' | 'amber' | 'blue' | 'rose' | 'gray'
}>()

const statusLabel = computed(() => {
  switch (props.status) {
    case 'active': return 'Active'
    case 'on-hold': return 'On Hold'
    case 'completed': return 'Completed'
    case 'archived': return 'Archived'
    default: return props.status || 'Active'
  }
})

const badgeClasses = computed(() => {
  switch (props.status) {
    case 'active':
    case 'emerald':
      return 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20'
    case 'on-hold':
    case 'amber':
      return 'bg-amber-500/10 text-amber-400 border-amber-500/20'
    case 'completed':
    case 'blue':
      return 'bg-blue-500/10 text-blue-400 border-blue-500/20'
    case 'rose':
      return 'bg-rose-500/10 text-rose-400 border-rose-500/20'
    case 'archived':
    case 'gray':
    default:
      return 'bg-slate-500/10 text-slate-400 border-slate-500/20'
  }
})

const dotClasses = computed(() => {
  switch (props.status) {
    case 'active':
    case 'emerald':
      return 'bg-emerald-400 animate-pulse'
    case 'on-hold':
    case 'amber':
      return 'bg-amber-400'
    case 'completed':
    case 'blue':
      return 'bg-blue-400'
    case 'rose':
      return 'bg-rose-400'
    case 'archived':
    case 'gray':
    default:
      return 'bg-slate-400'
  }
})
</script>
