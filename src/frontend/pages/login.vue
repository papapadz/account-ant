<template>
  <NuxtLayout name="auth">
    <div class="glass-card p-8 rounded-2xl border border-[#1E293B] shadow-2xl space-y-6">
      <div>
        <h2 class="text-xl font-bold text-slate-100 tracking-tight">Sign in to AccountAnt</h2>
        <p class="text-xs text-slate-400 mt-1">Access your automated ledger & financial management dashboard</p>
      </div>

      <form @submit.prevent="handleLogin" class="space-y-4">
        <div>
          <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Email Address</label>
          <input
            v-model="email"
            type="email"
            required
            placeholder="admin@accountant.io"
            class="input-field"
          />
        </div>

        <div>
          <div class="flex justify-between items-center mb-1.5">
            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">Password</label>
            <a href="#" class="text-xs text-emerald-400 hover:underline">Forgot password?</a>
          </div>
          <input
            v-model="password"
            type="password"
            required
            placeholder="••••••••••••"
            class="input-field"
          />
        </div>

        <div class="flex items-center gap-2">
          <input id="remember" type="checkbox" class="rounded bg-[#0F172A] border-[#334155] text-emerald-500 focus:ring-emerald-500/20" />
          <label for="remember" class="text-xs text-slate-400">Remember this device</label>
        </div>

        <button
          type="submit"
          :disabled="isLoading"
          class="btn-primary w-full py-3 text-sm font-bold shadow-lg shadow-emerald-500/20"
        >
          <span v-if="isLoading" class="flex items-center gap-2">
            <svg class="animate-spin w-4 h-4 text-slate-950" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Authenticating...
          </span>
          <span v-else>Sign In to AccountAnt &rarr;</span>
        </button>
      </form>

      <div class="pt-4 border-t border-[#1E293B] text-center">
        <p class="text-xs text-slate-400">
          Need a new organization setup?
          <NuxtLink to="/register" class="text-emerald-400 font-semibold hover:underline">Register Account & Company &rarr;</NuxtLink>
        </p>
      </div>
    </div>
  </NuxtLayout>
</template>

<script setup lang="ts">
definePageMeta({
  layout: false,
})

const auth = useAuth()
const email = ref('admin@accountant.io')
const password = ref('password')
const isLoading = ref(false)

const handleLogin = async () => {
  isLoading.value = true
  try {
    await auth.login(email.value, password.value)
    await navigateTo('/')
  } finally {
    isLoading.value = false
  }
}
</script>
