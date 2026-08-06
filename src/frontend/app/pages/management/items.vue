<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 glass-card p-5 rounded-xl border border-[var(--border-color)]">
      <div>
        <h1 class="text-lg font-bold text-[var(--text-main)] tracking-tight flex items-center gap-2">
          <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 11h.01M7 15h.01M11 7h7M11 11h7M11 15h7M4 5h16a1 1 0 011 1v12a1 1 0 01-1 1H4a1 1 0 01-1-1V6a1 1 0 011-1z" />
          </svg>
          Account Items Management
        </h1>
        <p class="text-xs text-[var(--text-muted)] mt-0.5">Standardized catalog of line items mapped to ledger accounts</p>
      </div>

      <UiButton variant="primary" @click="isModalOpen = true">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
        </svg>
        <span>Add Account Item</span>
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
          placeholder="Search items by code, name, or description..."
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
            ? 'bg-amber-500/20 text-amber-400 border-amber-500/30'
            : 'bg-[var(--bg-surface)] text-[var(--text-muted)] border-[var(--border-color)] hover:border-amber-500/30'"
        >
          {{ chip.label }}
          <span class="ml-1 opacity-60">({{ chip.count }})</span>
        </button>
      </div>
    </div>

    <!-- Catalog Grid -->
    <ClientOnly>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div
          v-for="item in filteredItems"
          :key="item.id"
          class="glass-card p-5 rounded-xl border border-[var(--border-color)] relative overflow-hidden flex flex-col justify-between"
        >
          <div>
            <div class="flex items-center justify-between mb-2">
              <span class="px-2 py-0.5 rounded bg-amber-500/10 text-amber-500 border border-amber-500/20 font-mono text-xs font-semibold">
                {{ item.item_code }}
              </span>
              <div class="flex items-center gap-2">
                <span
                  class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border"
                  :class="item.transaction_type === 'credit' ? 'bg-amber-500/10 text-amber-400 border-amber-500/20' : 'bg-blue-500/10 text-blue-400 border-blue-500/20'"
                >
                  {{ item.transaction_type === 'credit' ? 'Inflow' : 'Outflow' }}
                </span>
                <span class="text-[10px] text-[var(--text-muted)] font-mono">#{{ item.id }}</span>
              </div>
            </div>

            <h3 class="text-sm font-bold text-[var(--text-main)] mt-1">
              {{ item.item_name }}  
            </h3>
            <p class="text-xs text-[var(--text-muted)] mt-1.5 leading-relaxed">
              {{ item.description || 'Standard accounting item.' }}
            </p>
          </div>

          <div class="pt-3 mt-4 border-t border-[var(--border-color)] flex items-center justify-between text-[11px] text-[var(--text-muted)]">
            <span>{{ getLedgerAccountCode(item.ledger_account_id) }}</span>
          </div>
        </div>
      </div>
      <p v-if="filteredItems.length === 0" class="text-center text-[var(--text-muted)] text-sm py-10">No items found for the selected filter.</p>
    </ClientOnly>

    <!-- Create Modal -->
    <Modal :isOpen="isModalOpen" title="Create Account Catalog Item" @close="isModalOpen = false">
      <form @submit.prevent="handleCreateItem" class="space-y-4">
        
        <div>
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Linked Ledger Account *</label>
          <select v-model="newItem.ledger_account_id" required class="input-field">
            <option :value="undefined" disabled>Select Ledger account...</option>
            <option v-for="acc in accounting.ledgerAccounts.value" :key="acc.id" :value="acc.id">
              {{ acc.account_code }} - {{ acc.account_name }}
            </option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Item Code *</label>
          <input v-model="newItem.item_code" type="text" required placeholder="ITEM-SRV-06" class="input-field font-mono" />
        </div>

        <div>
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Item Name *</label>
          <input v-model="newItem.item_name" type="text" required placeholder="API Data Stream Processing Service" class="input-field" />
        </div>

        <div>
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Transaction Type *</label>
          <div class="grid grid-cols-2 gap-3">
            <UiButton
              type="button"
              :variant="newItem.transaction_type === 'debit' ? 'primary' : 'secondary'"
              block
              size="sm"
              @click="newItem.transaction_type = 'debit'"
            >
              <span class="w-2 h-2 rounded-full bg-blue-400"></span>
              Outflow (Expense)
            </UiButton>
            <UiButton
              type="button"
              :variant="newItem.transaction_type === 'credit' ? 'primary' : 'secondary'"
              block
              size="sm"
              @click="newItem.transaction_type = 'credit'"
            >
              <span class="w-2 h-2 rounded-full bg-amber-400"></span>
              Inflow (Income / Refund)
            </UiButton>
          </div>
        </div>
        <div>
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Description</label>
          <textarea v-model="newItem.description" rows="3" placeholder="Detailed description of the transaction line item..." class="input-field"></textarea>
        </div>

        <div class="pt-4 flex items-center justify-end gap-3 border-t border-[var(--border-color)]">
          <UiButton type="button" variant="secondary" size="sm" @click="isModalOpen = false">Cancel</UiButton>
          <UiButton type="submit" variant="primary" size="sm">Save Account Item</UiButton>
        </div>
      </form>
    </Modal>
  </div>
</template>

<script setup lang="ts">
const accounting = useAccounting()
const isModalOpen = ref(false)
const searchQuery = ref('')
const statusFilter = ref('active')

const itemStatusOptions = [
  { value: 'active', label: 'Active' },
  { value: 'inactive', label: 'Inactive' },
  { value: 'archived', label: 'Archived' },
]

const statusChips = computed(() => [
  { value: 'all', label: 'All', count: accounting.accountItems.value.length },
  { value: 'active', label: 'Active', count: accounting.accountItems.value.filter(i => (i.status || 'active') === 'active').length },
  { value: 'inactive', label: 'Inactive', count: accounting.accountItems.value.filter(i => i.status === 'inactive').length },
  { value: 'archived', label: 'Archived', count: accounting.accountItems.value.filter(i => i.status === 'archived').length },
])

const filteredItems = computed(() => {
  const query = searchQuery.value.trim().toLowerCase()
  let items = accounting.accountItems.value
  if (statusFilter.value !== 'all') {
    items = items.filter(i => (i.status || 'active') === statusFilter.value)
  }
  if (!query) return items
  return items.filter(item =>
    item.item_code.toLowerCase().includes(query) ||
    item.item_name.toLowerCase().includes(query) ||
    (item.description && item.description.toLowerCase().includes(query))
  )
})

const newItem = reactive({
  item_code: '',
  item_name: '',
  description: '',
  ledger_account_id: undefined as number | undefined,
  transaction_type: 'debit' as 'debit' | 'credit',
})

const getLedgerAccountCode = (ledgerAccountId?: number) => {
  if (!ledgerAccountId) return 'N/A'
  const acc = accounting.ledgerAccounts.value.find(a => a.id === ledgerAccountId)
  return acc ? `${acc.account_code} (${acc.account_name})` : `#${ledgerAccountId}`
}

const handleCreateItem = () => {
  accounting.addAccountItem({ ...newItem })
  isModalOpen.value = false
  newItem.item_code = ''
  newItem.item_name = ''
  newItem.description = ''
  newItem.ledger_account_id = undefined
  newItem.transaction_type = 'debit'
}

const handleItemStatusChange = async (id: number, newStatus: string) => {
  try {
    await accounting.updateAccountItemStatus(id, newStatus as 'active' | 'inactive' | 'archived')
  } catch (err: any) {
    alert(err?.data?.message || err?.message || 'Failed to update status.')
  }
}
</script>
