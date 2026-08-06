<template>
  <Teleport to="body">
    <div
      v-if="isOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm transition-opacity"
      @click.self="close"
    >
      <div
        :class="[
          maxWidth,
          'w-full overflow-hidden transform transition-all duration-200 rounded-xl',
          variant === 'flat-white'
            ? 'bg-white border-0 shadow-none text-slate-900'
            : 'bg-[var(--bg-modal)] border border-[var(--border-color)] text-[var(--text-main)] shadow-2xl'
        ]"
      >
        <!-- Modal Header -->
        <div
          :class="[
            'px-6 py-4 flex items-center justify-between',
            variant === 'flat-white'
              ? 'border-b border-slate-100 bg-white'
              : 'border-b border-[var(--border-color)]'
          ]"
        >
          <h3
            :class="[
              'text-sm font-bold uppercase tracking-wider flex items-center gap-2',
              variant === 'flat-white' ? 'text-slate-900' : 'text-[var(--text-main)]'
            ]"
          >
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            {{ title }}
          </h3>
          <UiButton
            variant="ghost"
            size="sm"
            :class="[
              '!p-1',
              variant === 'flat-white'
                ? 'text-slate-400 hover:text-slate-900'
                : 'text-[var(--text-muted)] hover:text-[var(--text-main)]'
            ]"
            @click="close"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </UiButton>
        </div>

        <!-- Modal Body -->
        <div
          :class="[
            'p-6 max-h-[85vh] overflow-y-auto',
            variant === 'flat-white' ? 'bg-white text-slate-900' : ''
          ]"
        >
          <slot></slot>
        </div>

        <!-- Modal Footer -->
        <div
          v-if="$slots.footer"
          :class="[
            'px-6 py-4 flex items-center justify-end gap-3',
            variant === 'flat-white'
              ? 'border-t border-slate-100 bg-slate-50'
              : 'border-t border-[var(--border-color)] bg-[var(--bg-sidebar)]'
          ]"
        >
          <slot name="footer"></slot>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
export interface ModalProps {
  isOpen: boolean
  title: string
  variant?: 'default' | 'flat-white'
  maxWidth?: string
}

const props = withDefaults(defineProps<ModalProps>(), {
  variant: 'default',
  maxWidth: 'max-w-lg'
})

const emit = defineEmits(['close'])

const close = () => {
  emit('close')
}
</script>
