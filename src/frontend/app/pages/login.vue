<template>
  <div class="glass-card p-8 sm:p-10 rounded-3xl border border-[var(--border-color)] shadow-2xl space-y-6 bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl transition-all duration-300">
    <div class="space-y-1 text-center sm:text-left">
      <h2 class="text-2xl font-bold text-[var(--text-main)] tracking-tight">Sign in to AccountAnt</h2>
      <p class="text-xs text-[var(--text-muted)]">Enter your corporate credentials to access your financial ledger</p>
    </div>

    <!-- Quick Persona Selector for Dev / Demo -->
    <!-- <div class="p-3 rounded-xl bg-slate-100 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60 space-y-2">
      <div class="flex items-center justify-between text-[11px] font-semibold text-[var(--text-muted)] tracking-wider uppercase">
        <span>Quick Demo Login</span>
        <span class="text-emerald-500 font-mono text-[10px]">3 Seeded Roles</span>
      </div>
      <div class="grid grid-cols-3 gap-2">
        <button
          v-for="persona in demoPersonas"
          :key="persona.email"
          type="button"
          @click="selectPersona(persona)"
          class="px-2.5 py-1.5 rounded-lg text-xs font-medium transition-all text-left flex flex-col border"
          :class="email === persona.email
            ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/40 ring-1 ring-emerald-500/30'
            : 'bg-white/60 dark:bg-slate-900/40 text-[var(--text-muted)] border-slate-200 dark:border-slate-800 hover:border-emerald-500/30 hover:text-[var(--text-main)]'"
        >
          <span class="font-semibold text-xs leading-tight truncate">{{ persona.label }}</span>
          <span class="text-[10px] opacity-75 truncate font-mono mt-0.5">{{ persona.role }}</span>
        </button>
      </div>
    </div> -->

    <!-- Error Alert Banner -->
    <div
      v-if="errorMessage"
      class="p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-600 dark:text-rose-400 text-xs flex items-start gap-2.5 animate-fadeIn"
    >
      <svg class="w-4 h-4 shrink-0 mt-0.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
      </svg>
      <span class="leading-relaxed">{{ errorMessage }}</span>
    </div>

    <form @submit.prevent="handleLogin" class="space-y-4">
      <!-- Email Field -->
      <div>
        <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">
          Email Address
        </label>
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
            </svg>
          </div>
          <input
            v-model="email"
            type="email"
            required
            placeholder="admin@accountant.io"
            class="input-field pl-10 w-full"
          />
        </div>
      </div>

      <!-- Password Field -->
      <div>
        <div class="flex justify-between items-center mb-1.5">
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider">
            Password
          </label>
          <a href="#" @click.prevent="showForgotNotice" class="text-xs text-emerald-500 hover:text-emerald-400 font-medium hover:underline transition-colors">
            Forgot password?
          </a>
        </div>
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
          </div>
          <input
            v-model="password"
            :type="showPassword ? 'text' : 'password'"
            required
            placeholder="••••••••••••"
            class="input-field pl-10 pr-10 w-full"
          />
          <button
            type="button"
            @click="showPassword = !showPassword"
            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-200 transition-colors focus:outline-none"
          >
            <svg v-if="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a8.959 8.959 0 014.122-.963c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21f-9 9 0 00-9-9" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Remember device checkbox -->
      <div class="flex items-center gap-2 pt-1">
        <input
          id="remember"
          v-model="rememberMe"
          type="checkbox"
          class="rounded bg-[var(--bg-input)] border-[var(--border-color)] text-emerald-500 focus:ring-emerald-500/20 cursor-pointer"
        />
        <label for="remember" class="text-xs text-[var(--text-muted)] cursor-pointer select-none">
          Remember this session on this device
        </label>
      </div>

      <!-- Submit Button -->
      <UiButton
        type="submit"
        variant="primary"
        block
        size="lg"
        :loading="isLoading"
        class="mt-2 shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/30 transition-all cursor-pointer"
      >
        <span>Sign In to AccountAnt &rarr;</span>
      </UiButton>
    </form>

    <!-- Footer Links -->
    <!-- <div class="pt-4 border-t border-[var(--border-color)] text-center space-y-2">
      <p class="text-xs text-[var(--text-muted)]">
        Need a new organization setup?
        <NuxtLink to="/register" class="text-emerald-500 font-semibold hover:underline ml-1">
          Register Account & Company &rarr;
        </NuxtLink>
      </p>
    </div> -->
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'auth',
})

const auth = useAuth()

const email = ref('admin@accountant.io')
const password = ref('password')
const showPassword = ref(false)
const rememberMe = ref(true)
const isLoading = ref(false)
const errorMessage = ref('')

const demoPersonas = [
  { label: 'Admin', role: 'Finance Director', email: 'admin@accountant.io', password: 'password' },
  { label: 'Accountant', role: 'Senior Accountant', email: 'accountant@accountant.io', password: 'password' },
  { label: 'Auditor', role: 'Internal Auditor', email: 'auditor@accountant.io', password: 'password' },
]

const selectPersona = (persona: { email: string; password: string }) => {
  email.value = persona.email
  password.value = persona.password
  errorMessage.value = ''
}

const showForgotNotice = () => {
  errorMessage.value = 'Password reset instructions have been dispatched to your corporate email administrator.'
}

const handleLogin = async () => {
  if (!email.value || !password.value) {
    errorMessage.value = 'Please enter both your email address and password.'
    return
  }

  isLoading.value = true
  errorMessage.value = ''

  try {
    await auth.login(email.value, password.value)
    
    // Fetch initial user and data right away
    const accountingStore = useAccounting()
    const projectsStore = useProjects()
    await auth.fetchUser()
    await accountingStore.fetchFundAccounts()
    await accountingStore.fetchLedgerAccounts()
    await accountingStore.fetchAccountItems()
    await accountingStore.fetchJournalEntries()
    await projectsStore.fetchProjects()

    await navigateTo('/')
  } catch (error: any) {
    errorMessage.value = error?.data?.message || error?.message || 'Invalid authentication credentials. Please try again.'
  } finally {
    isLoading.value = false
  }
}
</script>
