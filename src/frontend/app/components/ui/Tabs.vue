<template>
  <div
    role="tablist"
    class="flex items-center overflow-x-auto no-scrollbar select-none"
    :class="containerClasses"
  >
    <button
      v-for="(item, index) in items"
      :key="item.value"
      role="tab"
      :aria-selected="modelValue === item.value"
      :tabindex="modelValue === item.value ? 0 : -1"
      type="button"
      class="inline-flex items-center justify-center gap-2 font-semibold transition-all duration-200 cursor-pointer shrink-0 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/50 active:scale-[0.97]"
      :class="[
        sizeClasses[size || 'md'],
        getItemClasses(item.value)
      ]"
      @click="selectTab(item.value)"
      @keydown="handleKeydown($event, index)"
    >
      <!-- Icon slot / SVG icon -->
      <slot name="icon" :item="item">
        <svg
          v-if="item.icon"
          class="w-4 h-4 shrink-0 transition-colors"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
          v-html="item.icon"
        />
      </slot>

      <span>{{ item.label }}</span>

      <!-- Badge Pill -->
      <span
        v-if="item.badge !== undefined"
        class="ml-1 px-1.5 py-0.5 text-[10px] font-mono font-bold rounded-full transition-colors"
        :class="modelValue === item.value ? activeBadgeClasses : inactiveBadgeClasses"
      >
        {{ item.badge }}
      </span>
    </button>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

export interface TabItem {
  value: string | number
  label: string
  icon?: string
  badge?: string | number
}

const props = defineProps<{
  modelValue: string | number
  items: TabItem[]
  variant?: 'segmented' | 'pills' | 'underline'
  size?: 'sm' | 'md'
}>()

const emit = defineEmits(['update:modelValue', 'change'])

const selectTab = (val: string | number) => {
  emit('update:modelValue', val)
  emit('change', val)
}

const sizeClasses = {
  sm: 'px-2.5 py-1 text-xs',
  md: 'px-3.5 py-2 text-sm',
}

const containerClasses = computed(() => {
  switch (props.variant) {
    case 'segmented':
      return 'bg-[var(--bg-sidebar)] p-1 rounded-xl border border-[var(--border-color)] gap-1'
    case 'underline':
      return 'border-b border-[var(--border-color)] gap-4 pb-0'
    case 'pills':
    default:
      return 'gap-2'
  }
})

const getItemClasses = (itemValue: string | number) => {
  const isActive = props.modelValue === itemValue
  const variant = props.variant || 'segmented'

  if (variant === 'segmented') {
    return isActive
      ? 'bg-[var(--bg-surface)] text-[var(--color-primary)] font-bold border border-[var(--border-color)] shadow-sm'
      : 'text-[var(--text-muted)] hover:text-[var(--text-main)] hover:bg-[var(--bg-surface)]/50'
  }

  if (variant === 'underline') {
    return isActive
      ? 'text-[var(--color-primary)] font-bold border-b-2 border-[var(--color-primary)] rounded-b-none !px-1 pb-2'
      : 'text-[var(--text-muted)] hover:text-[var(--text-main)] !px-1 pb-2 rounded-b-none'
  }

  // default: 'pills'
  return isActive
    ? 'bg-[var(--color-primary)] text-slate-950 font-bold shadow-md shadow-emerald-500/20'
    : 'bg-[var(--bg-surface)] text-[var(--text-muted)] hover:text-[var(--text-main)] border border-[var(--border-color)] hover:bg-[var(--bg-sidebar)]'
}

const activeBadgeClasses = computed(() => {
  return props.variant === 'pills'
    ? 'bg-slate-950/20 text-slate-950'
    : 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30'
})

const inactiveBadgeClasses = computed(() => {
  return 'bg-[var(--bg-sidebar)] text-[var(--text-muted)] border border-[var(--border-color)]'
})

const handleKeydown = (e: KeyboardEvent, currentIndex: number) => {
  let targetIndex = -1
  if (e.key === 'ArrowRight') {
    targetIndex = (currentIndex + 1) % props.items.length
  } else if (e.key === 'ArrowLeft') {
    targetIndex = (currentIndex - 1 + props.items.length) % props.items.length
  } else if (e.key === 'Home') {
    targetIndex = 0
  } else if (e.key === 'End') {
    targetIndex = props.items.length - 1
  }

  if (targetIndex !== -1) {
    e.preventDefault()
    selectTab(props.items[targetIndex].value)
  }
}
</script>
