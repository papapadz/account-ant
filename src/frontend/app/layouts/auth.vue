<template>
  <div class="fixed inset-0 w-full h-full bg-[var(--bg-app)] text-[var(--text-main)] flex items-center justify-center p-4 z-50 overflow-y-auto font-sans transition-colors duration-200 m-0">
    <!-- Ambient Background Glow Effects -->
    <div class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-emerald-500/10 rounded-full blur-[140px] pointer-events-none" />

    <div class="w-full max-w-md mx-auto my-auto flex flex-col items-center justify-center shrink-0 z-10">
      <slot />
    </div>
  </div>
</template>

<script setup lang="ts">
</script>
