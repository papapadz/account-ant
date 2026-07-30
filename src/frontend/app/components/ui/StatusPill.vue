<template>
  <div class="relative inline-block" ref="pillRef">
    <!-- Status Badge Button -->
    <button
      type="button"
      @click.stop="toggleDropdown"
      class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border cursor-pointer transition-all duration-200 hover:opacity-90"
      :class="colorClass"
      :title="`Status: ${status} — click to change`"
    >
      <span class="w-1.5 h-1.5 rounded-full" :class="dotClass"></span>
      {{ status }}
      <svg class="w-2.5 h-2.5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
      </svg>
    </button>

    <!-- Dropdown Panel -->
    <Transition
      enter-active-class="transition-all duration-150 ease-out"
      enter-from-class="opacity-0 scale-95 -translate-y-1"
      enter-to-class="opacity-100 scale-100 translate-y-0"
      leave-active-class="transition-all duration-100 ease-in"
      leave-from-class="opacity-100 scale-100 translate-y-0"
      leave-to-class="opacity-0 scale-95 -translate-y-1"
    >
      <div
        v-if="isOpen"
        class="absolute left-0 top-full mt-1.5 z-50 min-w-[160px] glass-card rounded-xl border border-[var(--border-color)] shadow-xl overflow-hidden"
        style="backdrop-filter: blur(12px);"
      >
        <div class="p-1">
          <p class="px-2 py-1 text-[10px] font-bold uppercase tracking-widest text-[var(--text-muted)] border-b border-[var(--border-color)] mb-1">Change Status</p>
          <button
            v-for="option in options"
            :key="option.value"
            type="button"
            @click.stop="selectStatus(option.value)"
            class="w-full flex items-center gap-2 px-2.5 py-2 rounded-lg text-xs font-semibold transition-colors duration-150 cursor-pointer"
            :class="[
              option.value === status
                ? 'bg-[var(--border-color)] text-[var(--text-main)]'
                : 'text-[var(--text-muted)] hover:bg-[var(--border-color)] hover:text-[var(--text-main)]'
            ]"
          >
            <span class="w-2 h-2 rounded-full flex-shrink-0" :class="optionDotClass(option.value)"></span>
            {{ option.label }}
            <svg v-if="option.value === status" class="w-3 h-3 ml-auto text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
            </svg>
          </button>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup lang="ts">
interface StatusOption {
  value: string
  label: string
}

const props = defineProps<{
  status: string
  options: StatusOption[]
}>()

const emit = defineEmits<{
  change: [value: string]
}>()

const isOpen = ref(false)
const pillRef = ref<HTMLElement | null>(null)

const STATUS_COLOR_MAP: Record<string, { pill: string; dot: string }> = {
  active:     { pill: 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20', dot: 'bg-emerald-400' },
  inactive:   { pill: 'bg-amber-500/10 text-amber-400 border-amber-500/20',       dot: 'bg-amber-400' },
  archived:   { pill: 'bg-slate-500/10 text-slate-400 border-slate-500/30',       dot: 'bg-slate-400' },
  posted:     { pill: 'bg-blue-500/10 text-blue-400 border-blue-500/20',           dot: 'bg-blue-400' },
  void:       { pill: 'bg-rose-500/10 text-rose-400 border-rose-500/20',           dot: 'bg-rose-400' },
  reconciled: { pill: 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20', dot: 'bg-emerald-400' },
  'on-hold':  { pill: 'bg-amber-500/10 text-amber-400 border-amber-500/20',       dot: 'bg-amber-400' },
  completed:  { pill: 'bg-blue-500/10 text-blue-400 border-blue-500/20',           dot: 'bg-blue-400' },
}

const colorClass = computed(() => STATUS_COLOR_MAP[props.status]?.pill ?? 'bg-slate-500/10 text-slate-400 border-slate-500/30')
const dotClass = computed(() => STATUS_COLOR_MAP[props.status]?.dot ?? 'bg-slate-400')

const optionDotClass = (val: string) => STATUS_COLOR_MAP[val]?.dot ?? 'bg-slate-400'

const toggleDropdown = () => { isOpen.value = !isOpen.value }

const selectStatus = (val: string) => {
  isOpen.value = false
  if (val !== props.status) {
    emit('change', val)
  }
}

// Close on outside click
const handleOutsideClick = (e: MouseEvent) => {
  if (pillRef.value && !pillRef.value.contains(e.target as Node)) {
    isOpen.value = false
  }
}

onMounted(() => document.addEventListener('click', handleOutsideClick, true))
onUnmounted(() => document.removeEventListener('click', handleOutsideClick, true))
</script>
