<template>
  <div class="glass-card rounded-xl border border-[var(--border-color)] p-6 transition-all duration-200 hover:border-emerald-500/30 group">
    <div v-if="$slots.header || title" class="flex items-center justify-between mb-4 pb-3 border-b border-[var(--border-color)]">
      <slot name="header">
        <div>
          <h3 class="text-sm font-bold text-[var(--text-main)] tracking-tight">{{ title }}</h3>
          <p v-if="subtitle" class="text-xs text-[var(--text-muted)] mt-0.5">{{ subtitle }}</p>
        </div>
      </slot>
      <slot name="header-action" />
    </div>

    <div>
      <slot />
    </div>

    <div v-if="$slots.footer" class="mt-4 pt-3 border-t border-[var(--border-color)] text-xs text-[var(--text-muted)]">
      <slot name="footer" />
    </div>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{
  title?: string
  subtitle?: string
}>()
</script>
