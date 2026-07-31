<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 glass-card p-5 rounded-xl border border-[var(--border-color)]">
      <div>
        <h1 class="text-lg font-bold text-[var(--text-main)] tracking-tight flex items-center gap-2">
          <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          Journal Ledger Transactions (`LedgerAccountItem`)
        </h1>
        <p class="text-xs text-[var(--text-muted)] mt-0.5">Post and view double-entry outflow and inflow journal entries with dedicated fund sources</p>
      </div>

      <UiButton variant="primary" @click="isModalOpen = true">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
        </svg>
        <span>+ Post New Journal Entry</span>
      </UiButton>
    </div>

    <!-- Real-time Balance Summary Bar -->
    <ClientOnly>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-[var(--bg-surface)] border border-[var(--border-color)] p-4 rounded-xl flex items-center justify-between">
          <div>
            <span class="text-[11px] text-[var(--text-muted)] font-semibold uppercase tracking-wider block">Total Outflows</span>
            <span class="text-xl font-bold font-mono-num text-blue-500">{{ currencyStore.formatCurrency(accounting.totalDebits.value) }}</span>
          </div>
          <div class="w-8 h-8 rounded bg-blue-500/10 text-blue-500 flex items-center justify-center font-bold text-xs">Out</div>
        </div>

        <div class="bg-[var(--bg-surface)] border border-[var(--border-color)] p-4 rounded-xl flex items-center justify-between">
          <div>
            <span class="text-[11px] text-[var(--text-muted)] font-semibold uppercase tracking-wider block">Total Inflows</span>
            <span class="text-xl font-bold font-mono-num text-amber-500">{{ currencyStore.formatCurrency(accounting.totalCredits.value) }}</span>
          </div>
          <div class="w-8 h-8 rounded bg-amber-500/10 text-amber-500 flex items-center justify-center font-bold text-xs">In</div>
        </div>

        <div class="bg-[var(--bg-surface)] border border-[var(--border-color)] p-4 rounded-xl flex items-center justify-between">
          <div>
            <span class="text-[11px] text-[var(--text-muted)] font-semibold uppercase tracking-wider block">Net Ledger Balance</span>
            <span class="text-xl font-bold font-mono-num text-emerald-500">{{ currencyStore.formatCurrency(accounting.netLedgerBalance.value) }}</span>
          </div>
          <div class="w-8 h-8 rounded bg-emerald-500/10 text-emerald-500 flex items-center justify-center font-bold text-xs">Bal</div>
        </div>
      </div>
    </ClientOnly>

    <!-- Filter & Responsive Table -->
    <ClientOnly>
      <UiDataTable
        :items="filteredEntries"
        :columns="journalColumns"
        :searchable="true"
        search-placeholder="Search description, account or fund..."
        :search-fields="['description']"
        default-sort-key="id"
        default-sort-order="desc"
        :default-page-size="10"
      >
        <!-- Header Actions: Type Filters -->
        <template #header-actions>
          <UiTabs v-model="filterType" :items="journalFilterTabs" variant="segmented" size="sm" />
        </template>

        <!-- Cell: ID -->
        <template #cell-id="{ value }">
          <span class="font-mono text-[var(--text-muted)] font-bold">#{{ value }}</span>
        </template>

        <!-- Cell: Fund Account -->
        <template #cell-fund_account_id="{ value }">
          <span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 px-2 py-0.5 rounded font-mono text-[11px] font-semibold">
            {{ getFundCode(value) }}
          </span>
        </template>

        <!-- Cell: Ledger Account -->
        <template #cell-ledger_account_id="{ value }">
          <span class="font-mono font-bold text-[var(--text-main)]">
            {{ getAccountCode(value) }}
          </span>
        </template>

        <!-- Cell: Account Item -->
        <template #cell-account_item_id="{ value }">
          <span class="text-[var(--text-main)] font-medium">
            {{ getItemName(value) }}
          </span>
        </template>

        <!-- Cell: Transaction Type -->
        <template #cell-transaction_type="{ value }">
          <span
            class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider"
            :class="value === 'debit' ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20'"
          >
            {{ value }}
          </span>
        </template>

        <!-- Cell: Debit -->
        <template #cell-debit="{ item }">
          <span v-if="item.transaction_type === 'debit'" class="font-mono font-bold text-blue-400 whitespace-nowrap">
            {{ currencyStore.formatCurrency(item.amount) }}
          </span>
          <span v-else class="text-[var(--text-sub)]">—</span>
        </template>

        <!-- Cell: Credit -->
        <template #cell-credit="{ item }">
          <span v-if="item.transaction_type === 'credit'" class="font-mono font-bold text-amber-400 whitespace-nowrap">
            {{ currencyStore.formatCurrency(item.amount) }}
          </span>
          <span v-else class="text-[var(--text-sub)]">—</span>
        </template>

        <!-- Cell: Description -->
        <template #cell-description="{ value }">
          <span class="text-[var(--text-main)] max-w-xs truncate block">{{ value || 'N/A' }}</span>
        </template>

        <!-- Cell: Timestamp -->
        <template #cell-created_at="{ value }">
          <span class="font-mono text-[var(--text-muted)] text-[11px] whitespace-nowrap">{{ value }}</span>
        </template>
      </UiDataTable>
    </ClientOnly>

    <!-- Create Journal Entry Modal -->
    <Modal :isOpen="isModalOpen" title="Post Double-Entry Journal Transaction" @close="isModalOpen = false">
      <form @submit.prevent="handleCreateEntry" class="space-y-4">
        <div>
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Transaction Type *</label>
          <div class="grid grid-cols-2 gap-3">
            <UiButton
              type="button"
              :variant="newEntry.transaction_type === 'debit' ? 'primary' : 'secondary'"
              block
              @click="newEntry.transaction_type = 'debit'"
            >
              Outflow Entry
            </UiButton>
            <UiButton
              type="button"
              :variant="newEntry.transaction_type === 'credit' ? 'primary' : 'secondary'"
              block
              @click="newEntry.transaction_type = 'credit'"
            >
              Inflow Entry
            </UiButton>
          </div>
        </div>

        <div>
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Fund Source *</label>
          <select v-model="newEntry.fund_account_id" class="input-field font-medium">
            <option v-for="fund in accounting.fundAccounts.value" :key="fund.id" :value="fund.id">
              {{ fund.fund_code }} - {{ fund.fund_name }}
            </option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Target Ledger Account *</label>
          <select v-model="newEntry.ledger_account_id" class="input-field font-medium">
            <option v-for="acc in accounting.ledgerAccounts.value" :key="acc.id" :value="acc.id">
              {{ acc.account_code }} - {{ acc.account_name }}
            </option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Master Account Item *</label>
          <select v-model="newEntry.account_item_id" class="input-field font-medium">
            <option v-for="item in accounting.accountItems.value" :key="item.id" :value="item.id">
              {{ item.item_code }} - {{ item.item_name }}
            </option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Transaction Amount ($ USD) *</label>
          <input
            v-model.number="newEntry.amount"
            type="number"
            step="0.01"
            required
            placeholder="5000.00"
            class="input-field font-mono font-bold text-base text-emerald-400"
          />
        </div>

        <div>
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Entry Memo / Description</label>
          <textarea v-model="newEntry.description" rows="2" placeholder="Record purpose of journal entry..." class="input-field"></textarea>
        </div>

        <div class="pt-4 flex items-center justify-end gap-3 border-t border-[var(--border-color)]">
          <UiButton type="button" variant="secondary" size="sm" @click="isModalOpen = false">Cancel</UiButton>
          <UiButton type="submit" variant="primary" size="sm">Post Transaction to Ledger</UiButton>
        </div>
      </form>
    </Modal>
  </div>
</template>

<script setup lang="ts">
import type { DataTableColumn } from '~/components/ui/DataTable.vue'

const currencyStore = useCurrency() 
const accounting = useAccounting()
const filterType = ref<'all' | 'debit' | 'credit'>('all')

const journalFilterTabs = computed<TabItem[]>(() => [
  { value: 'all', label: 'All', badge: accounting.journalEntries.value.length },
  { value: 'debit', label: 'Outflows' },
  { value: 'credit', label: 'Inflows' },
])
const isModalOpen = ref(false)

const journalColumns: DataTableColumn[] = [
  { key: 'id', label: 'Entry #', width: 'w-24' },
  { key: 'fund_account_id', label: 'Fund Source', width: 'min-w-[140px]' },
  { key: 'ledger_account_id', label: 'Ledger Account', width: 'min-w-[150px]' },
  { key: 'account_item_id', label: 'Catalog Item', width: 'min-w-[180px]' },
  { key: 'transaction_type', label: 'Type', width: 'w-24' },
  { key: 'debit', label: 'Outflow', align: 'right', width: 'min-w-[130px]' },
  { key: 'credit', label: 'Inflow', align: 'right', width: 'min-w-[130px]' },
  { key: 'description', label: 'Description', width: 'min-w-[200px]' },
  { key: 'created_at', label: 'Timestamp', align: 'right', width: 'min-w-[130px]' },
]

const newEntry = reactive({
  ledger_account_id: 10,
  fund_account_id: 1,
  account_item_id: 1,
  amount: 1000.00,
  transaction_type: 'debit' as 'debit' | 'credit',
  description: '',
  user_id: 1,
})

watch(() => newEntry.account_item_id, (newItemId) => {
  if (!newItemId) return
  const item = accounting.accountItems.value.find(i => i.id === Number(newItemId))
  if (item && item.ledger_account_id) {
    newEntry.ledger_account_id = item.ledger_account_id
  }
}, { immediate: true })

const getFundCode = (id?: number) => {
  if (!id) return 'FND-101'
  const fund = accounting.fundAccounts.value.find(f => f.id === id)
  return fund ? fund.fund_code : `FND-#${id}`
}

const getAccountCode = (id: number) => {
  const acc = accounting.ledgerAccounts.value.find(a => a.id === id)
  return acc ? acc.account_code : `#${id}`
}

const getItemName = (id: number) => {
  const item = accounting.accountItems.value.find(i => i.id === id)
  return item ? item.item_name : `Item #${id}`
}

const filteredEntries = computed(() => {
  return accounting.journalEntries.value.filter(entry => {
    if (filterType.value !== 'all' && entry.transaction_type !== filterType.value) {
      return false
    }
    return true
  })
})

const handleCreateEntry = () => {
  accounting.addJournalEntry({ ...newEntry })
  isModalOpen.value = false
  newEntry.description = ''
  newEntry.amount = 1000.00
}
</script>
