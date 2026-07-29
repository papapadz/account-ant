# Example custom components

These are starting conventions, not gospel — if the project already has `components/ui/`, match its existing patterns instead. All examples use `<script setup lang="ts">` and rely on Nuxt's auto-import for components placed in `components/ui/`.

## Button.vue

```vue
<script setup lang="ts">
interface Props {
  variant?: 'primary' | 'secondary' | 'ghost' | 'destructive'
  size?: 'sm' | 'md' | 'lg'
  disabled?: boolean
  block?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'primary',
  size: 'md',
  disabled: false,
  block: false,
})

const variantClasses: Record<NonNullable<Props['variant']>, string> = {
  primary: 'bg-brand-600 text-white active:bg-brand-700',
  secondary: 'bg-slate-100 text-slate-900 active:bg-slate-200',
  ghost: 'bg-transparent text-brand-600 active:bg-brand-50',
  destructive: 'bg-red-600 text-white active:bg-red-700',
}

const sizeClasses: Record<NonNullable<Props['size']>, string> = {
  sm: 'h-9 px-3 text-sm',
  md: 'h-11 px-4 text-base',
  lg: 'h-13 px-6 text-lg',
}
</script>

<template>
  <button
    :disabled="disabled"
    class="inline-flex items-center justify-center gap-2 rounded-xl font-medium
           transition-colors duration-150 disabled:opacity-40 disabled:pointer-events-none"
    :class="[variantClasses[variant], sizeClasses[size], block && 'w-full']"
  >
    <slot name="icon-start" />
    <slot />
    <slot name="icon-end" />
  </button>
</template>
```

Real `<button>` under the hood, so it gets native focus/keyboard/disabled semantics for free — no `role`/`tabindex` shims needed.

## Card.vue

```vue
<script setup lang="ts">
interface Props {
  padded?: boolean
}
withDefaults(defineProps<Props>(), { padded: true })
</script>

<template>
  <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
    <div v-if="$slots.header" class="border-b border-slate-100 px-4 py-3">
      <slot name="header" />
    </div>
    <div :class="padded ? 'p-4' : ''">
      <slot />
    </div>
    <div v-if="$slots.footer" class="border-t border-slate-100 px-4 py-3">
      <slot name="footer" />
    </div>
  </div>
</template>
```

## Input.vue

```vue
<script setup lang="ts">
interface Props {
  modelValue: string
  label?: string
  placeholder?: string
  error?: string
  type?: 'text' | 'email' | 'password' | 'number' | 'tel'
}
defineProps<Props>()
const emit = defineEmits<{ 'update:modelValue': [value: string] }>()
</script>

<template>
  <label class="block">
    <span v-if="label" class="mb-1.5 block text-sm font-medium text-slate-700">{{ label }}</span>
    <input
      :type="type ?? 'text'"
      :value="modelValue"
      :placeholder="placeholder"
      class="w-full rounded-xl border px-3.5 h-11 text-base
             placeholder:text-slate-400 focus:outline-none focus:ring-2
             transition-colors"
      :class="error
        ? 'border-red-400 focus:ring-red-200'
        : 'border-slate-200 focus:ring-brand-200 focus:border-brand-400'"
      @input="emit('update:modelValue', ($event.target as HTMLInputElement).value)"
    >
    <span v-if="error" class="mt-1 block text-sm text-red-600">{{ error }}</span>
  </label>
</template>
```

A real `<input>` — native mobile keyboards (`type="email"`, `type="tel"`, etc.), autofill, and form submission all work without extra plumbing that `IonInput` would otherwise handle for you.

## Toggle.vue

```vue
<script setup lang="ts">
interface Props { modelValue: boolean; disabled?: boolean }
defineProps<Props>()
const emit = defineEmits<{ 'update:modelValue': [value: boolean] }>()
</script>

<template>
  <button
    type="button"
    role="switch"
    :aria-checked="modelValue"
    :disabled="disabled"
    class="relative h-7 w-12 rounded-full transition-colors disabled:opacity-40"
    :class="modelValue ? 'bg-brand-600' : 'bg-slate-200'"
    @click="emit('update:modelValue', !modelValue)"
  >
    <span
      class="absolute top-0.5 left-0.5 h-6 w-6 rounded-full bg-white shadow transition-transform"
      :class="modelValue && 'translate-x-5'"
    />
  </button>
</template>
```

Uses `role="switch"` + `aria-checked` since this isn't a native input type — this is the accessibility contract that replaces what `IonToggle` gave you automatically.

## ListItem.vue + List.vue

```vue
<!-- ListItem.vue -->
<script setup lang="ts">
interface Props { clickable?: boolean }
withDefaults(defineProps<Props>(), { clickable: true })
</script>

<template>
  <component
    :is="clickable ? 'button' : 'div'"
    class="flex w-full items-center gap-3 px-4 py-3 text-left"
    :class="clickable && 'active:bg-slate-50'"
  >
    <slot name="leading" />
    <div class="flex-1 min-w-0">
      <slot />
    </div>
    <slot name="trailing" />
  </component>
</template>
```

```vue
<!-- List.vue -->
<template>
  <div class="divide-y divide-slate-100 rounded-2xl bg-white ring-1 ring-slate-100 overflow-hidden">
    <slot />
  </div>
</template>
```

## TabBar.vue

Pairs with Vue Router / Nuxt's routing (kept from Ionic conceptually via `IonRouterOutlet` for page transitions) but the bar itself is fully custom:

```vue
<script setup lang="ts">
interface Tab { to: string; label: string; icon: string }
defineProps<{ tabs: Tab[] }>()
const route = useRoute()
</script>

<template>
  <nav class="fixed bottom-0 inset-x-0 flex border-t border-slate-100 bg-white/95
              backdrop-blur pb-safe-b">
    <NuxtLink
      v-for="tab in tabs"
      :key="tab.to"
      :to="tab.to"
      class="flex-1 flex flex-col items-center gap-0.5 py-2 text-xs"
      :class="route.path === tab.to ? 'text-brand-600' : 'text-slate-400'"
    >
      <ion-icon :name="tab.icon" class="text-2xl" />
      {{ tab.label }}
    </NuxtLink>
  </nav>
</template>
```

Note the `<ion-icon>` — icons are the one place it's fine to keep using an Ionic element directly, since it's just rendering an SVG glyph and carries no Ionic visual styling to fight against.
