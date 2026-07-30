<template>
  <div class="space-y-1.5 w-full">
    <label v-if="label" :for="id" class="block text-xs font-medium text-[var(--text-muted)] uppercase tracking-wider">
      {{ label }}
      <span v-if="required" class="text-rose-400">*</span>
    </label>

    <div class="relative rounded-lg shadow-sm">
      <div v-if="prefix || $slots['icon-left']" class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[var(--text-muted)] text-sm font-mono z-10">
        <slot name="icon-left">{{ prefix }}</slot>
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
        class="input-field"
        :class="[
          (prefix || $slots['icon-left']) ? '!pl-10' : '',
          $slots['icon-right'] ? '!pr-10' : '',
          error ? 'border-rose-500 focus:ring-rose-500/50' : ''
        ]"
        @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
      />

      <div v-if="$slots['icon-right']" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-[var(--text-muted)] text-sm z-10">
        <slot name="icon-right" />
      </div>
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
