<template>
  <div class="w-full h-full min-h-screen bg-[var(--bg-app)] text-[var(--text-main)] flex items-center justify-center p-4 relative overflow-hidden font-sans transition-colors duration-200">
    <!-- Ambient Background Glow Effects -->
    <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-emerald-500/10 rounded-full blur-[140px] pointer-events-none" />
    <div class="absolute bottom-10 right-10 w-[400px] h-[400px] bg-blue-500/10 rounded-full blur-[120px] pointer-events-none" />

    <div class="w-full max-w-xl z-10">
      <!-- Brand Header -->
      <div class="text-center mb-8">
        <NuxtLink to="/" class="inline-flex items-center gap-3">
          <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-emerald-500 to-blue-600 flex items-center justify-center text-white font-bold text-xl shadow-xl shadow-emerald-500/25">
            <svg class="w-6 h-6 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
          </div>
          <div class="text-left">
            <span class="text-2xl font-bold tracking-tight text-[var(--text-main)] block">AccountAnt</span>
            <span class="text-xs text-emerald-500 font-mono tracking-wide">AUTOMATED LEDGER SYSTEM</span>
          </div>
        </NuxtLink>
      </div>

      <slot />
    </div>
  </div>
</template>

<script setup lang="ts">
</script>
