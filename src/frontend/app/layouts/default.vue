<template>
  <div class="min-h-screen bg-[var(--bg-app)] text-[var(--text-main)] flex font-sans antialiased transition-colors duration-200">
    <!-- Sidebar -->
    <AppSidebar />

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0">
      <AppHeader />
      <main class="flex-1 p-6 overflow-y-auto">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
</script>
