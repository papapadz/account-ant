<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 glass-card p-5 rounded-xl border border-[var(--border-color)]">
      <div>
        <h1 class="text-lg font-bold text-[var(--text-main)] tracking-tight flex items-center gap-2">
          <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          Ledger Accounts Management
        </h1>
        <p class="text-xs text-[var(--text-muted)] mt-0.5">Ledger Accounts used across transaction ledgers and financial reporting</p>
      </div>

      <UiButton variant="primary" @click="isModalOpen = true">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
        </svg>
        <span>Add Ledger Account</span>
      </UiButton>
    </div>

    <!-- Search Bar -->
    <div class="glass-card p-4 rounded-xl border border-[var(--border-color)]">
      <div class="relative">
        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-[var(--text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search by account code, name, or description..."
          class="input-field pl-9 text-xs"
        />
      </div>
    </div>

    <!-- Ledger Accounts Grid -->
    <ClientOnly>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div
          v-for="account in filteredAccounts"
          :key="account.id"
          class="glass-card p-5 rounded-xl border border-[var(--border-color)] relative overflow-hidden flex flex-col justify-between group"
        >
          <div>
            <div class="flex items-center justify-between mb-3">
              <span class="px-2.5 py-1 rounded bg-blue-500/10 text-blue-400 border border-blue-500/20 font-mono text-xs font-bold">
                {{ account.account_code }}
              </span>
              <span class="text-[11px] text-[var(--text-muted)] font-mono">ID: #{{ account.id }}</span>
            </div>

            <h3 class="text-base font-bold text-[var(--text-main)] group-hover:text-blue-500 transition-colors">
              {{ account.account_name }}
            </h3>
            <p class="text-xs text-[var(--text-muted)] mt-1 line-clamp-2">
              {{ account.description || 'No description provided.' }}
            </p>
          </div>

          <div class="pt-4 mt-4 border-t border-[var(--border-color)] flex items-center justify-between text-xs">
            <span class="text-blue-500 font-semibold">Active</span>
          </div>
        </div>
      </div>
    </ClientOnly>

    <!-- Create Modal -->
    <Modal :isOpen="isModalOpen" title="Create New Ledger Account" @close="isModalOpen = false">
      <form @submit.prevent="handleCreateAccount" class="space-y-4">
        <div>
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Account Code *</label>
          <input v-model="newAcc.account_code" type="text" required placeholder="1030-PREPAID" class="input-field font-mono" />
        </div>

        <div>
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Account Name *</label>
          <input v-model="newAcc.account_name" type="text" required placeholder="Prepaid Software Subscriptions" class="input-field" />
        </div>

        <div>
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Description</label>
          <textarea v-model="newAcc.description" rows="3" placeholder="Account purpose..." class="input-field"></textarea>
        </div>

        <div class="pt-4 flex items-center justify-end gap-3 border-t border-[var(--border-color)]">
          <UiButton type="button" variant="secondary" size="sm" @click="isModalOpen = false">Cancel</UiButton>
          <UiButton type="submit" variant="primary" size="sm">Save Ledger Account</UiButton>
        </div>
      </form>
    </Modal>
  </div>
</template>

<script setup lang="ts">
const accounting = useAccounting()
const isModalOpen = ref(false)
const searchQuery = ref('')

const filteredAccounts = computed(() => {
  const query = searchQuery.value.trim().toLowerCase()
  if (!query) return accounting.ledgerAccounts.value
  return accounting.ledgerAccounts.value.filter(account =>
    account.account_code.toLowerCase().includes(query) ||
    account.account_name.toLowerCase().includes(query) ||
    (account.description && account.description.toLowerCase().includes(query))
  )
})

const newAcc = reactive({
  account_code: '',
  account_name: '',
  description: '',
  user_id: 1,
  ledger_account_id: 1,
})

const handleCreateAccount = () => {
  accounting.addLedgerAccount({ ...newAcc })
  isModalOpen.value = false
  newAcc.account_code = ''
  newAcc.account_name = ''
  newAcc.description = ''
}
</script>
