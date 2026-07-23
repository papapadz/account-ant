<template>
  <aside class="w-64 bg-[#0B1120] border-r border-[#1E293B] flex flex-col justify-between h-screen sticky top-0 z-30 select-none">
    <!-- Brand / Header -->
    <div>
      <div class="h-16 flex items-center px-6 border-b border-[#1E293B] gap-3">
        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-500 to-blue-600 flex items-center justify-center text-slate-950 font-bold text-lg shadow-lg shadow-emerald-500/20">
          <svg class="w-5 h-5 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
          </svg>
        </div>
        <div>
          <h1 class="font-bold text-slate-100 tracking-tight text-base flex items-center gap-1.5">
            AccountAnt
            <span class="text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 px-1.5 py-0.5 rounded-full uppercase tracking-wider">v1.0</span>
          </h1>
          <p class="text-[11px] text-slate-400">Automated Ledger System</p>
        </div>
      </div>

      <!-- Company Selector Badge -->
      <div class="p-4 border-b border-[#1E293B]/60">
        <div class="bg-[#0F172A] border border-[#1E293B] rounded-lg p-2.5 flex items-center justify-between">
          <div class="flex items-center gap-2.5 overflow-hidden">
            <div class="w-7 h-7 rounded bg-blue-500/10 border border-blue-500/20 text-blue-400 flex items-center justify-center font-bold text-xs shrink-0">
              {{ companyInitials }}
            </div>
            <div class="truncate">
              <div class="text-xs font-semibold text-slate-200 truncate">{{ auth.currentCompany.value?.business_name || 'Apex Technologies' }}</div>
              <div class="text-[10px] text-slate-400 capitalize">{{ auth.currentCompany.value?.business_scope || 'National' }} Scope</div>
            </div>
          </div>
          <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
          </svg>
        </div>
      </div>

      <!-- Navigation Links -->
      <nav class="p-3 space-y-1">
        <div class="px-3 py-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Main Ledger</div>

        <NuxtLink
          to="/"
          class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-medium transition-colors"
          :class="route.path === '/' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-semibold' : 'text-slate-400 hover:text-slate-200 hover:bg-[#1E293B]/50'"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
          </svg>
          Overview Dashboard
        </NuxtLink>

        <NuxtLink
          to="/management/journal"
          class="flex items-center justify-between px-3 py-2.5 rounded-lg text-xs font-medium transition-colors"
          :class="route.path === '/management/journal' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-semibold' : 'text-slate-400 hover:text-slate-200 hover:bg-[#1E293B]/50'"
        >
          <div class="flex items-center gap-3">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Journal Entries
          </div>
          <span class="bg-emerald-500/20 text-emerald-400 text-[10px] px-1.5 py-0.5 rounded-md font-mono">{{ journalCount }}</span>
        </NuxtLink>

        <div class="px-3 pt-4 pb-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Management & Projects</div>

        <NuxtLink
          to="/management/projects"
          class="flex items-center justify-between px-3 py-2.5 rounded-lg text-xs font-medium transition-colors"
          :class="route.path.startsWith('/management/projects') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-semibold' : 'text-slate-400 hover:text-slate-200 hover:bg-[#1E293B]/50'"
        >
          <div class="flex items-center gap-3">
            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m3 0h1m-4-8a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2a1 1 0 01-1-1v-4z" />
            </svg>
            Projects Ledger
          </div>
          <span class="bg-purple-500/20 text-purple-400 text-[10px] px-1.5 py-0.5 rounded-md font-mono font-bold">New</span>
        </NuxtLink>

        <NuxtLink
          to="/management/funds"
          class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-medium transition-colors"
          :class="route.path === '/management/funds' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-semibold' : 'text-slate-400 hover:text-slate-200 hover:bg-[#1E293B]/50'"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
          </svg>
          Fund Accounts
        </NuxtLink>

        <NuxtLink
          to="/management/accounts"
          class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-medium transition-colors"
          :class="route.path === '/management/accounts' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-semibold' : 'text-slate-400 hover:text-slate-200 hover:bg-[#1E293B]/50'"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          Ledger Accounts
        </NuxtLink>

        <NuxtLink
          to="/management/items"
          class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-medium transition-colors"
          :class="route.path === '/management/items' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-semibold' : 'text-slate-400 hover:text-slate-200 hover:bg-[#1E293B]/50'"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 11h.01M7 15h.01M11 7h7M11 11h7M11 15h7M4 5h16a1 1 0 011 1v12a1 1 0 01-1 1H4a1 1 0 01-1-1V6a1 1 0 011-1z" />
          </svg>
          Account Items Catalog
        </NuxtLink>

        <div class="px-3 pt-4 pb-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider">System</div>

        <NuxtLink
          to="/settings"
          class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-medium transition-colors"
          :class="route.path === '/settings' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-semibold' : 'text-slate-400 hover:text-slate-200 hover:bg-[#1E293B]/50'"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          Settings & Profile
        </NuxtLink>
      </nav>
    </div>

    <!-- User Footer Profile -->
    <div class="p-3 border-t border-[#1E293B]">
      <div class="flex items-center justify-between p-2 rounded-lg bg-[#0F172A] border border-[#1E293B]">
        <div class="flex items-center gap-2.5 overflow-hidden">
          <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-400 to-blue-500 flex items-center justify-center text-slate-950 font-bold text-xs shrink-0">
            {{ userInitials }}
          </div>
          <div class="truncate">
            <div class="text-xs font-medium text-slate-200 truncate">{{ auth.currentPerson.value?.first_name }} {{ auth.currentPerson.value?.last_name }}</div>
            <div class="text-[10px] text-slate-400 truncate">{{ auth.currentPosition.value?.title || 'Accountant' }}</div>
          </div>
        </div>

        <button
          @click="auth.logout()"
          title="Sign Out"
          class="p-1.5 text-slate-400 hover:text-rose-400 rounded-md hover:bg-rose-500/10 transition-colors"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
        </button>
      </div>
    </div>
  </aside>
</template>

<script setup lang="ts">
const route = useRoute()
const auth = useAuth()
const accounting = useAccounting()

const journalCount = computed(() => accounting.journalEntries.value.length)

const companyInitials = computed(() => {
  const name = auth.currentCompany.value?.business_name || 'APEX'
  return name.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase()
})

const userInitials = computed(() => {
  const f = auth.currentPerson.value?.first_name?.[0] || 'A'
  const l = auth.currentPerson.value?.last_name?.[0] || 'S'
  return `${f}${l}`.toUpperCase()
})
</script>
