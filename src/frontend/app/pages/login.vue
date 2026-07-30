<template>
  <div class="w-full max-w-md bg-[var(--bg-surface)] border border-[var(--border-color)] rounded-2xl shadow-xl p-6 sm:p-8 space-y-6 font-sans mx-auto my-auto shrink-0">
    <!-- Header with Brand Logo -->
    <div class="flex flex-col items-center text-center space-y-3">
      <div class="w-14 h-14 rounded-2xl overflow-hidden shrink-0 shadow-lg shadow-emerald-500/20 border border-[var(--border-color)] bg-[var(--bg-surface)] p-1.5 flex items-center justify-center">
        <img src="~/assets/img/logo.png" alt="Account-Ant Logo" class="w-full h-full object-contain" />
      </div>
      <div>
        <h1 class="text-2xl font-extrabold text-[var(--text-main)] tracking-tight">Account-Ant</h1>
        <p class="text-xs text-[var(--text-muted)] mt-1">Automated Ledger System</p>
      </div>
    </div>

    <!-- Error Alert Banner -->
    <div
      v-if="errorMessage"
      class="p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs flex items-start gap-2.5 animate-fadeIn"
    >
      <svg class="w-4 h-4 shrink-0 mt-0.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
      </svg>
      <span class="leading-relaxed">{{ errorMessage }}</span>
    </div>

    <!-- Login Form -->
    <form @submit.prevent="handleLogin" class="space-y-4">
      <!-- Email Field -->
      <UiInput
        v-model="email"
        type="email"
        label="Email Address"
        placeholder="admin@accountant.io"
        :required="true"
        :disabled="isLoading"
      >
        <template #icon-left>
          <svg class="w-4 h-4 text-emerald-500/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
          </svg>
        </template>
      </UiInput>

      <!-- Password Field -->
      <div class="space-y-1.5">
        <div class="flex justify-between items-center">
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider">
            Password <span class="text-rose-400">*</span>
          </label>
          <a href="#" @click.prevent="showForgotNotice" class="text-xs text-emerald-500 hover:text-emerald-400 font-medium hover:underline transition-colors">
            Forgot password?
          </a>
        </div>
        <UiInput
          v-model="password"
          :type="showPassword ? 'text' : 'password'"
          placeholder="••••••••••••"
          :required="true"
          :disabled="isLoading"
        >
          <template #icon-left>
            <svg class="w-4 h-4 text-emerald-500/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
          </template>
          <template #icon-right>
            <button
              type="button"
              @click="showPassword = !showPassword"
              class="text-slate-400 hover:text-slate-200 transition-colors focus:outline-none cursor-pointer"
            >
              <svg v-if="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
              <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a8.959 8.959 0 014.122-.963c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21f-9 9 0 00-9-9" />
              </svg>
            </button>
          </template>
        </UiInput>
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
      <div class="pt-2">
        <UiButton
          type="submit"
          variant="primary"
          size="lg"
          :block="true"
          :loading="isLoading"
          :disabled="isLoading"
          class="shadow-lg shadow-emerald-500/20"
        >
          <span>Sign In to Dashboard &rarr;</span>
        </UiButton>
      </div>
    </form>
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
