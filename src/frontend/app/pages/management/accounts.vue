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

    <!-- Search Bar + Status Filter -->
    <div class="glass-card p-4 rounded-xl border border-[var(--border-color)] space-y-3">
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
      <!-- Status Filter Chips -->
      <div class="flex flex-wrap items-center gap-2">
        <span class="text-[10px] font-bold uppercase tracking-wider text-[var(--text-muted)]">Filter:</span>
        <button
          v-for="chip in statusChips"
          :key="chip.value"
          type="button"
          @click="statusFilter = chip.value"
          class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border transition-all duration-150 cursor-pointer"
          :class="statusFilter === chip.value
            ? 'bg-blue-500/20 text-blue-400 border-blue-500/30'
            : 'bg-[var(--bg-surface)] text-[var(--text-muted)] border-[var(--border-color)] hover:border-blue-500/30'"
        >
          {{ chip.label }}
          <span class="ml-1 opacity-60">({{ chip.count }})</span>
        </button>
      </div>
    </div>

    <!-- Ledger Accounts Grid -->
    <ClientOnly>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div
          v-for="account in filteredAccounts"
          :key="account.id"
          @click="openAddItemModal(account)"
          class="glass-card p-5 rounded-xl border border-[var(--border-color)] relative overflow-hidden flex flex-col justify-between group cursor-pointer hover:border-blue-500/50 hover:shadow-lg hover:shadow-blue-500/10 transition-all duration-200"
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

          <div class="pt-3 mt-4 border-t border-[var(--border-color)] flex items-center justify-between text-xs">
            <span class="inline-flex items-center gap-1.5 text-[var(--text-muted)] text-[11px]">
              <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 11h.01M7 15h.01M11 7h7M11 11h7M11 15h7M4 5h16a1 1 0 011 1H4a1 1 0 01-1-1V6a1 1 0 011-1z" />
              </svg>
              {{ getAccountItemCount(account.id) }} Linked Item(s)
            </span>
            <span class="inline-flex items-center gap-1 text-blue-400 font-semibold text-[11px] group-hover:underline">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
              </svg>
              Add Items
            </span>
          </div>
        </div>
      </div>
      <p v-if="filteredAccounts.length === 0" class="text-center text-[var(--text-muted)] text-sm py-10">No accounts found for the selected filter.</p>
    </ClientOnly>

    <!-- Create Modal -->
    <Modal :isOpen="isModalOpen" title="Create New Ledger Account" @close="closeModal">
      <form @submit.prevent="handleCreateAccount" class="space-y-4">
        <!-- Error Alert Banner -->
        <div v-if="errorMessage" class="p-3 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs flex items-start gap-2">
          <svg class="w-4 h-4 text-rose-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span class="break-words">{{ errorMessage }}</span>
        </div>

        <div>
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Account Code *</label>
          <input v-model="newAcc.account_code" type="text" required placeholder="1030-PREPAID" :disabled="isSubmitting" class="input-field font-mono" />
        </div>

        <div>
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Account Name *</label>
          <input v-model="newAcc.account_name" type="text" required placeholder="Prepaid Software Subscriptions" :disabled="isSubmitting" class="input-field" />
        </div>

        <div>
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Description</label>
          <textarea v-model="newAcc.description" rows="3" placeholder="Account purpose..." :disabled="isSubmitting" class="input-field"></textarea>
        </div>

        <div class="pt-4 flex items-center justify-end gap-3 border-t border-[var(--border-color)]">
          <UiButton type="button" variant="secondary" size="sm" :disabled="isSubmitting" @click="closeModal">Cancel</UiButton>
          <UiButton type="submit" variant="primary" size="sm" :loading="isSubmitting" :disabled="isSubmitting">Save Ledger Account</UiButton>
        </div>
      </form>
    </Modal>

    <!-- Batch Add Account Items Modal -->
    <Modal :isOpen="isAddItemModalOpen" :title="`Add Account Items - ${selectedAccountForItems?.account_code || ''}`" @close="closeAddItemModal">
      <div v-if="selectedAccountForItems" class="space-y-5">
        <!-- Target Ledger Account Context -->
        <div class="p-3 rounded-lg bg-blue-500/10 border border-blue-500/20 text-xs flex items-center justify-between">
          <div>
            <span class="text-[var(--text-muted)] font-semibold uppercase tracking-wider block text-[10px]">Target Ledger Account</span>
            <span class="font-bold text-[var(--text-main)] text-sm">{{ selectedAccountForItems.account_name }}</span>
          </div>
          <span class="px-2.5 py-1 rounded bg-blue-500/20 text-blue-400 font-mono font-bold text-xs">
            {{ selectedAccountForItems.account_code }}
          </span>
        </div>

        <!-- Error Alert Banner -->
        <div v-if="addItemErrorMessage" class="p-3 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs flex items-start gap-2">
          <svg class="w-4 h-4 text-rose-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span class="break-words">{{ addItemErrorMessage }}</span>
        </div>

        <form @submit.prevent="handleSaveAccountItems" class="space-y-4">
          <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-1">
            <div
              v-for="(row, idx) in itemRows"
              :key="idx"
              class="p-4 rounded-xl border border-[var(--border-color)] bg-[var(--bg-surface)]/50 space-y-3 relative group"
            >
              <div class="flex items-center justify-between border-b border-[var(--border-color)] pb-2">
                <span class="text-xs font-bold text-blue-400 flex items-center gap-1.5">
                  <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                  Item #{{ idx + 1 }}
                </span>
                <button
                  v-if="itemRows.length > 1"
                  type="button"
                  @click="removeRow(idx)"
                  :disabled="isSubmittingItems"
                  class="text-[var(--text-muted)] hover:text-rose-400 text-xs font-semibold flex items-center gap-1 transition-colors cursor-pointer"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                  Remove
                </button>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                  <label class="block text-[10px] font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Item Code *</label>
                  <input
                    v-model="row.item_code"
                    type="text"
                    required
                    placeholder="ITEM-01"
                    :disabled="isSubmittingItems"
                    class="input-field font-mono text-xs"
                  />
                </div>

                <div>
                  <label class="block text-[10px] font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Item Name *</label>
                  <input
                    v-model="row.item_name"
                    type="text"
                    required
                    placeholder="Monthly Subscription"
                    :disabled="isSubmittingItems"
                    class="input-field text-xs"
                  />
                </div>
              </div>

              <div>
                <label class="block text-[10px] font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Transaction Type *</label>
                <div class="grid grid-cols-2 gap-2">
                  <UiButton
                    type="button"
                    :variant="row.transaction_type === 'debit' ? 'primary' : 'secondary'"
                    block
                    size="sm"
                    :disabled="isSubmittingItems"
                    @click="row.transaction_type = 'debit'"
                  >
                    <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                    Outflow (Expense)
                  </UiButton>
                  <UiButton
                    type="button"
                    :variant="row.transaction_type === 'credit' ? 'primary' : 'secondary'"
                    block
                    size="sm"
                    :disabled="isSubmittingItems"
                    @click="row.transaction_type = 'credit'"
                  >
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                    Inflow (Income / Refund)
                  </UiButton>
                </div>
              </div>

              <div>
                <label class="block text-[10px] font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Description</label>
                <input
                  v-model="row.description"
                  type="text"
                  placeholder="Optional notes or details..."
                  :disabled="isSubmittingItems"
                  class="input-field text-xs"
                />
              </div>
            </div>
          </div>

          <!-- Add Another Row Button -->
          <UiButton
            type="button"
            variant="secondary"
            block
            size="sm"
            :disabled="isSubmittingItems"
            @click="addAnotherRow"
            class="border-dashed"
          >
            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            <span>+ Add Another Item</span>
          </UiButton>

          <div class="pt-4 flex items-center justify-end gap-3 border-t border-[var(--border-color)]">
            <UiButton type="button" variant="secondary" size="sm" :disabled="isSubmittingItems" @click="closeAddItemModal">Cancel</UiButton>
            <UiButton type="submit" variant="primary" size="sm" :loading="isSubmittingItems" :disabled="isSubmittingItems">
              Save {{ itemRows.length }} Account Item{{ itemRows.length > 1 ? 's' : '' }}
            </UiButton>
          </div>
        </form>
      </div>
    </Modal>
  </div>
</template>

<script setup lang="ts">
interface ItemRow {
  item_code: string
  item_name: string
  transaction_type: 'debit' | 'credit'
  description: string
}

const accounting = useAccounting()
const isModalOpen = ref(false)
const isSubmitting = ref(false)
const errorMessage = ref('')
const searchQuery = ref('')
const statusFilter = ref('active')
const statusChangeSuccess = ref<number | null>(null)

// Add Item Modal state
const isAddItemModalOpen = ref(false)
const isSubmittingItems = ref(false)
const addItemErrorMessage = ref('')
const selectedAccountForItems = ref<any | null>(null)
const itemRows = ref<ItemRow[]>([])

onMounted(() => {
  if (accounting.accountItems.value.length === 0) {
    accounting.fetchAccountItems()
  }
})

const getAccountItemCount = (ledgerAccountId: number) => {
  return accounting.accountItems.value.filter(item => item.ledger_account_id === ledgerAccountId).length
}

const generateItemCode = (accountCode: string, index: number) => {
  const cleanCode = accountCode.replace(/[^a-zA-Z0-9]/g, '-').toUpperCase()
  const seq = String(index).padStart(2, '0')
  return `${cleanCode}-${seq}`
}

const openAddItemModal = (account: any) => {
  selectedAccountForItems.value = account
  addItemErrorMessage.value = ''
  const existingCount = getAccountItemCount(account.id)
  itemRows.value = [
    {
      item_code: generateItemCode(account.account_code, existingCount + 1),
      item_name: '',
      transaction_type: 'debit',
      description: '',
    }
  ]
  isAddItemModalOpen.value = true
}

const closeAddItemModal = () => {
  if (!isSubmittingItems.value) {
    isAddItemModalOpen.value = false
    selectedAccountForItems.value = null
    itemRows.value = []
    addItemErrorMessage.value = ''
  }
}

const addAnotherRow = () => {
  if (!selectedAccountForItems.value) return
  const currentTotal = getAccountItemCount(selectedAccountForItems.value.id) + itemRows.value.length + 1
  itemRows.value.push({
    item_code: generateItemCode(selectedAccountForItems.value.account_code, currentTotal),
    item_name: '',
    transaction_type: 'debit',
    description: '',
  })
}

const removeRow = (index: number) => {
  if (itemRows.value.length > 1) {
    itemRows.value.splice(index, 1)
  }
}

const handleSaveAccountItems = async () => {
  if (!selectedAccountForItems.value) return
  isSubmittingItems.value = true
  addItemErrorMessage.value = ''

  try {
    for (const row of itemRows.value) {
      if (!row.item_code.trim() || !row.item_name.trim()) {
        throw new Error('All item codes and item names are required.')
      }
      await accounting.addAccountItem({
        item_code: row.item_code.trim(),
        item_name: row.item_name.trim(),
        transaction_type: row.transaction_type,
        description: row.description.trim() || undefined,
        ledger_account_id: selectedAccountForItems.value.id,
      })
    }
    await accounting.fetchAccountItems()
    closeAddItemModal()
  } catch (err: any) {
    console.error('Failed to save account items:', err)
    addItemErrorMessage.value = err?.data?.message || err?.statusMessage || err?.message || 'Failed to save account items. Please check inputs and try again.'
  } finally {
    isSubmittingItems.value = false
  }
}

const ledgerStatusOptions = [
  { value: 'active', label: 'Active' },
  { value: 'inactive', label: 'Inactive' },
  { value: 'archived', label: 'Archived' },
]

const statusChips = computed(() => [
  { value: 'all', label: 'All', count: accounting.ledgerAccounts.value.length },
  { value: 'active', label: 'Active', count: accounting.ledgerAccounts.value.filter(a => (a.status || 'active') === 'active').length },
  { value: 'inactive', label: 'Inactive', count: accounting.ledgerAccounts.value.filter(a => a.status === 'inactive').length },
  { value: 'archived', label: 'Archived', count: accounting.ledgerAccounts.value.filter(a => a.status === 'archived').length },
])

const closeModal = () => {
  if (!isSubmitting.value) {
    isModalOpen.value = false
    errorMessage.value = ''
  }
}

const filteredAccounts = computed(() => {
  const query = searchQuery.value.trim().toLowerCase()
  let accounts = accounting.ledgerAccounts.value
  if (statusFilter.value !== 'all') {
    accounts = accounts.filter(a => (a.status || 'active') === statusFilter.value)
  }
  if (!query) return accounts
  return accounts.filter(account =>
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

const handleCreateAccount = async () => {
  isSubmitting.value = true
  errorMessage.value = ''
  try {
    await accounting.addLedgerAccount({ ...newAcc })
    isModalOpen.value = false
    newAcc.account_code = ''
    newAcc.account_name = ''
    newAcc.description = ''
  } catch (err: any) {
    console.error('Failed to create ledger account:', err)
    errorMessage.value = err?.data?.message || err?.statusMessage || err?.message || 'Failed to create ledger account. Please check inputs and try again.'
  } finally {
    isSubmitting.value = false
  }
}

const handleAccountStatusChange = async (id: number, newStatus: string) => {
  try {
    await accounting.updateLedgerAccountStatus(id, newStatus as 'active' | 'inactive' | 'archived')
    statusChangeSuccess.value = id
    setTimeout(() => { statusChangeSuccess.value = null }, 2000)
  } catch (err: any) {
    alert(err?.data?.message || err?.message || 'Failed to update status.')
  }
}
</script>
