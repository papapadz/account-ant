<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 glass-card p-5 rounded-xl border border-[var(--border-color)]">
      <div>
        <h1 class="text-lg font-bold text-[var(--text-main)] tracking-tight flex items-center gap-2">
          <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          Journal Ledger Transactions (`LedgerAccountItem`)
        </h1>
        <p class="text-xs text-[var(--text-muted)] mt-0.5">Post and view double-entry debit and credit journal entries with dedicated fund sources</p>
      </div>

      <button @click="isModalOpen = true" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
        </svg>
        <span>+ Post New Journal Entry</span>
      </button>
    </div>

    <!-- Real-time Balance Summary Bar -->
    <ClientOnly>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-[var(--bg-surface)] border border-[var(--border-color)] p-4 rounded-xl flex items-center justify-between">
          <div>
            <span class="text-[11px] text-[var(--text-muted)] font-semibold uppercase tracking-wider block">Total Debits</span>
            <span class="text-xl font-bold font-mono-num text-blue-500">${{ formatCurrency(accounting.totalDebits.value) }}</span>
          </div>
          <div class="w-8 h-8 rounded bg-blue-500/10 text-blue-500 flex items-center justify-center font-bold text-xs">Dr</div>
        </div>

        <div class="bg-[var(--bg-surface)] border border-[var(--border-color)] p-4 rounded-xl flex items-center justify-between">
          <div>
            <span class="text-[11px] text-[var(--text-muted)] font-semibold uppercase tracking-wider block">Total Credits</span>
            <span class="text-xl font-bold font-mono-num text-amber-500">${{ formatCurrency(accounting.totalCredits.value) }}</span>
          </div>
          <div class="w-8 h-8 rounded bg-amber-500/10 text-amber-500 flex items-center justify-center font-bold text-xs">Cr</div>
        </div>

        <div class="bg-[var(--bg-surface)] border border-[var(--border-color)] p-4 rounded-xl flex items-center justify-between">
          <div>
            <span class="text-[11px] text-[var(--text-muted)] font-semibold uppercase tracking-wider block">Net Ledger Balance</span>
            <span class="text-xl font-bold font-mono-num text-emerald-500">${{ formatCurrency(accounting.netLedgerBalance.value) }}</span>
          </div>
          <div class="w-8 h-8 rounded bg-emerald-500/10 text-emerald-500 flex items-center justify-center font-bold text-xs">Bal</div>
        </div>
      </div>
    </ClientOnly>

    <!-- Filter & Table -->
    <div class="glass-card rounded-xl border border-[var(--border-color)] overflow-hidden">
      <div class="p-4 border-b border-[var(--border-color)] flex flex-col sm:flex-row items-center justify-between gap-3">
        <ClientOnly>
          <div class="flex items-center gap-2">
            <button
              @click="filterType = 'all'"
              class="px-3 py-1 rounded text-xs font-semibold transition-colors cursor-pointer"
              :class="filterType === 'all' ? 'bg-emerald-500/20 text-emerald-500 border border-emerald-500/30' : 'text-[var(--text-muted)] hover:text-[var(--text-main)]'"
            >
              All Entries ({{ accounting.journalEntries.value.length }})
            </button>
            <button
              @click="filterType = 'debit'"
              class="px-3 py-1 rounded text-xs font-semibold transition-colors cursor-pointer"
              :class="filterType === 'debit' ? 'bg-blue-500/20 text-blue-500 border border-blue-500/30' : 'text-[var(--text-muted)] hover:text-[var(--text-main)]'"
            >
              Debits (Dr)
            </button>
            <button
              @click="filterType = 'credit'"
              class="px-3 py-1 rounded text-xs font-semibold transition-colors cursor-pointer"
              :class="filterType === 'credit' ? 'bg-amber-500/20 text-amber-500 border border-amber-500/30' : 'text-[var(--text-muted)] hover:text-[var(--text-main)]'"
            >
              Credits (Cr)
            </button>
          </div>
        </ClientOnly>

        <input
          v-model="searchQuery"
          type="text"
          placeholder="Filter description, account or fund..."
          class="input-field max-w-xs py-1.5 text-xs"
        />
      </div>

      <ClientOnly>
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="text-[11px] text-[var(--text-muted)] uppercase tracking-wider bg-[var(--bg-table-header)] border-b border-[var(--border-color)]">
                <th class="p-3.5 font-semibold">Entry #</th>
                <th class="p-3.5 font-semibold">Fund Source</th>
                <th class="p-3.5 font-semibold">Ledger Account</th>
                <th class="p-3.5 font-semibold">Catalog Item</th>
                <th class="p-3.5 font-semibold">Type</th>
                <th class="p-3.5 font-semibold text-right">Debit (Dr)</th>
                <th class="p-3.5 font-semibold text-right">Credit (Cr)</th>
                <th class="p-3.5 font-semibold">Description</th>
                <th class="p-3.5 font-semibold text-right">Timestamp</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[var(--border-color)] text-xs">
              <tr v-for="entry in filteredEntries" :key="entry.id" class="hover:bg-[var(--bg-table-hover)] transition-colors">
                <td class="p-3.5 font-mono text-[var(--text-muted)]">#{{ entry.id }}</td>
                <td class="p-3.5">
                  <span class="bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 px-2 py-0.5 rounded font-mono text-[11px] font-semibold">
                    {{ getFundCode(entry.fund_account_id) }}
                  </span>
                </td>
                <td class="p-3.5 font-mono font-bold text-[var(--text-main)]">
                  {{ getAccountCode(entry.ledger_account_id) }}
                </td>
                <td class="p-3.5 text-[var(--text-main)]">
                  {{ getItemName(entry.account_item_id) }}
                </td>
                <td class="p-3.5">
                  <span
                    class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider"
                    :class="entry.transaction_type === 'debit' ? 'bg-blue-500/10 text-blue-500 border border-blue-500/20' : 'bg-amber-500/10 text-amber-500 border border-amber-500/20'"
                  >
                    {{ entry.transaction_type }}
                  </span>
                </td>
                <td class="p-3.5 text-right font-mono font-bold text-blue-500">
                  <span v-if="entry.transaction_type === 'debit'">${{ formatCurrency(entry.amount) }}</span>
                  <span v-else class="text-[var(--text-sub)]">—</span>
                </td>
                <td class="p-3.5 text-right font-mono font-bold text-amber-500">
                  <span v-if="entry.transaction_type === 'credit'">${{ formatCurrency(entry.amount) }}</span>
                  <span v-else class="text-[var(--text-sub)]">—</span>
                </td>
                <td class="p-3.5 text-[var(--text-main)] max-w-xs truncate">{{ entry.description || 'N/A' }}</td>
                <td class="p-3.5 text-right font-mono text-[var(--text-muted)] text-[11px]">{{ entry.created_at }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </ClientOnly>
    </div>

    <!-- Create Journal Entry Modal -->
    <Modal :isOpen="isModalOpen" title="Post Double-Entry Journal Transaction" @close="isModalOpen = false">
      <form @submit.prevent="handleCreateEntry" class="space-y-4">
        <div>
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Transaction Type *</label>
          <div class="grid grid-cols-2 gap-3">
            <button
              type="button"
              @click="newEntry.transaction_type = 'debit'"
              class="py-2.5 px-4 rounded-lg font-bold text-xs flex items-center justify-center gap-2 border transition-all cursor-pointer"
              :class="newEntry.transaction_type === 'debit' ? 'bg-blue-500/20 text-blue-500 border-blue-500 ring-2 ring-blue-500/30' : 'bg-[var(--bg-input)] text-[var(--text-muted)] border-[var(--border-color)]'"
            >
              Debit Entry (Dr)
            </button>
            <button
              type="button"
              @click="newEntry.transaction_type = 'credit'"
              class="py-2.5 px-4 rounded-lg font-bold text-xs flex items-center justify-center gap-2 border transition-all cursor-pointer"
              :class="newEntry.transaction_type === 'credit' ? 'bg-amber-500/20 text-amber-500 border-amber-500 ring-2 ring-amber-500/30' : 'bg-[var(--bg-input)] text-[var(--text-muted)] border-[var(--border-color)]'"
            >
              Credit Entry (Cr)
            </button>
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
            class="input-field font-mono font-bold text-base text-emerald-500"
          />
        </div>

        <div>
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Entry Memo / Description</label>
          <textarea v-model="newEntry.description" rows="2" placeholder="Record purpose of journal entry..." class="input-field"></textarea>
        </div>

        <div class="pt-4 flex items-center justify-end gap-3 border-t border-[var(--border-color)]">
          <button type="button" @click="isModalOpen = false" class="btn-secondary py-2 px-4 text-xs">Cancel</button>
          <button type="submit" class="btn-primary py-2 px-5 text-xs font-bold">Post Transaction to Ledger</button>
        </div>
      </form>
    </Modal>
  </div>
</template>

<script setup lang="ts">
const accounting = useAccounting()
const filterType = ref<'all' | 'debit' | 'credit'>('all')
const searchQuery = ref('')
const isModalOpen = ref(false)

const newEntry = reactive({
  ledger_account_id: 10,
  fund_account_id: 1,
  account_item_id: 1,
  amount: 1000.00,
  transaction_type: 'debit' as 'debit' | 'credit',
  description: '',
  user_id: 1,
})

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
    if (searchQuery.value) {
      const q = searchQuery.value.toLowerCase()
      const desc = (entry.description || '').toLowerCase()
      const acc = getAccountCode(entry.ledger_account_id).toLowerCase()
      const fund = getFundCode(entry.fund_account_id).toLowerCase()
      return desc.includes(q) || acc.includes(q) || fund.includes(q)
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

const formatCurrency = (val: number) => {
  const num = typeof val === 'number' && !isNaN(val) ? val : 0
  return num.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',')
}
</script>
