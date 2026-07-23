<template>
  <Teleport to="body">
    <div
      v-if="isOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm transition-opacity"
      @click.self="close"
    >
      <div
        class="bg-[#0F172A] border border-[#1E293B] rounded-xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all"
      >
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-[#1E293B] flex items-center justify-between">
          <h3 class="text-sm font-bold text-slate-100 uppercase tracking-wider flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            {{ title }}
          </h3>
          <button
            @click="close"
            class="text-slate-400 hover:text-slate-200 p-1 rounded-md hover:bg-[#1E293B] transition-colors"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 max-h-[80vh] overflow-y-auto">
          <slot></slot>
        </div>

        <!-- Modal Footer -->
        <div v-if="$slots.footer" class="px-6 py-4 border-t border-[#1E293B] bg-[#0B1120]/50 flex items-center justify-end gap-3">
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
