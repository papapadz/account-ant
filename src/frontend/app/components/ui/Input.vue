<template>
  <div class="space-y-1.5 w-full">
    <label v-if="label" :for="id" class="block text-xs font-medium text-[var(--text-muted)] uppercase tracking-wider">
      {{ label }}
      <span v-if="required" class="text-rose-400">*</span>
    </label>

    <div class="relative rounded-lg shadow-sm">
      <div v-if="prefix" class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[var(--text-muted)] text-sm font-mono">
        {{ prefix }}
      </div>

      <input
        :id="id"
        :type="type || 'text'"
        :value="modelValue"
        :placeholder="placeholder"
        :required="required"
        :disabled="disabled"
        :step="step"
        :min="min"
        :max="max"
        class="w-full rounded-lg bg-[var(--bg-surface)] border border-[var(--border-color)] text-[var(--text-main)] text-sm px-3 py-2 transition-all placeholder-[var(--text-muted)] focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed"
        :class="[
          prefix ? 'pl-8' : '',
          error ? 'border-rose-500 focus:ring-rose-500/50' : ''
        ]"
        @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
      />
    </div>

    <p v-if="error" class="text-xs text-rose-400 mt-1">{{ error }}</p>
    <p v-else-if="hint" class="text-[11px] text-[var(--text-muted)]">{{ hint }}</p>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{
  modelValue: string | number
  label?: string
  id?: string
  type?: string
  placeholder?: string
  required?: boolean
  disabled?: boolean
  error?: string
  hint?: string
  prefix?: string
  step?: string | number
  min?: string | number
  max?: string | number
}>()

defineEmits(['update:modelValue'])
</script>
