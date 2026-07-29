<template>
  <Teleport to="body">
    <div
      v-if="isOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm transition-opacity"
      @click.self="close"
    >
      <div
        class="bg-[var(--bg-modal)] border border-[var(--border-color)] text-[var(--text-main)] rounded-xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all duration-200"
      >
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-[var(--border-color)] flex items-center justify-between">
          <h3 class="text-sm font-bold text-[var(--text-main)] uppercase tracking-wider flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            {{ title }}
          </h3>
          <UiButton
            variant="ghost"
            size="sm"
            class="!p-1 text-[var(--text-muted)] hover:text-[var(--text-main)]"
            @click="close"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </UiButton>
        </div>

        <!-- Modal Body -->
        <div class="p-6 max-h-[80vh] overflow-y-auto">
          <slot></slot>
        </div>

        <!-- Modal Footer -->
        <div v-if="$slots.footer" class="px-6 py-4 border-t border-[var(--border-color)] bg-[var(--bg-sidebar)] flex items-center justify-end gap-3">
          <slot name="footer"></slot>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
const props = defineProps<{
  isOpen: boolean
  title: string
}>()

const emit = defineEmits(['close'])

const close = () => {
  emit('close')
}
</script>
