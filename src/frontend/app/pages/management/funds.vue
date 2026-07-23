<template>
  <div class="space-y-6">
    <!-- Header Controls -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 glass-card p-5 rounded-xl border border-[var(--border-color)]">
      <div>
        <h1 class="text-lg font-bold text-[var(--text-main)] tracking-tight flex items-center gap-2">
          <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
          </svg>
          Fund Accounts Management
        </h1>
        <p class="text-xs text-[var(--text-muted)] mt-0.5">Corporate fund accounts linked to {{ auth.currentCompany.value?.business_name }}</p>
      </div>

      <button @click="isModalOpen = true" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
        </svg>
        <span>+ Add Fund Account</span>
      </button>
    </div>

    <!-- Fund Accounts Table/Grid -->
    <ClientOnly>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div
          v-for="fund in accounting.fundAccounts.value"
          :key="fund.id"
          class="glass-card p-5 rounded-xl border border-[var(--border-color)] relative overflow-hidden flex flex-col justify-between group"
        >
          <div>
            <div class="flex items-center justify-between mb-3">
              <span class="px-2.5 py-1 rounded bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 font-mono text-xs font-bold">
                {{ fund.fund_code }}
              </span>
              <span class="text-[11px] text-[var(--text-muted)] font-mono">ID: #{{ fund.id }}</span>
            </div>

            <h3 class="text-base font-bold text-[var(--text-main)] group-hover:text-emerald-500 transition-colors">
              {{ fund.fund_name }}
            </h3>
            <p class="text-xs text-[var(--text-muted)] mt-1 line-clamp-2">
              {{ fund.description || 'No description provided.' }}
            </p>
          </div>

          <div class="pt-4 mt-4 border-t border-[var(--border-color)] flex items-center justify-between text-xs">
            <div class="text-[var(--text-muted)]">
              Created: <span class="font-mono text-[var(--text-main)]">{{ fund.created_at }}</span>
            </div>
            <span class="text-emerald-500 font-semibold">Active Fund</span>
          </div>
        </div>
      </div>
    </ClientOnly>

    <!-- Create Modal -->
    <Modal :isOpen="isModalOpen" title="Create New Fund Account" @close="isModalOpen = false">
      <form @submit.prevent="handleCreateFund" class="space-y-4">
        <div>
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Fund Code *</label>
          <input v-model="newFund.fund_code" type="text" required placeholder="FND-404" class="input-field font-mono" />
        </div>

        <div>
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Fund Name *</label>
          <input v-model="newFund.fund_name" type="text" required placeholder="Special Operating & Liquidity Reserve" class="input-field" />
        </div>

        <div>
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Description</label>
          <textarea v-model="newFund.description" rows="3" placeholder="Specify purpose of this fund account..." class="input-field"></textarea>
        </div>

        <div class="pt-4 flex items-center justify-end gap-3 border-t border-[var(--border-color)]">
          <button type="button" @click="isModalOpen = false" class="btn-secondary py-2 px-4 text-xs">Cancel</button>
          <button type="submit" class="btn-primary py-2 px-5 text-xs font-bold">Save Fund Account</button>
        </div>
      </form>
    </Modal>
  </div>
</template>

<script setup lang="ts">
const auth = useAuth()
const accounting = useAccounting()
const isModalOpen = ref(false)

const newFund = reactive({
  fund_code: '',
  fund_name: '',
  description: '',
  company_id: 1,
  user_id: 1,
})

const handleCreateFund = () => {
  accounting.addFundAccount({
    ...newFund,
    company_id: auth.currentCompany.value?.id || 1,
    user_id: auth.currentUser.value?.id || 1,
  })
  isModalOpen.value = false
  newFund.fund_code = ''
  newFund.fund_name = ''
  newFund.description = ''
}
</script>
