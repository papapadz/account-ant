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
        <p class="text-xs text-[var(--text-muted)] mt-0.5">Corporate fund accounts and liquidity pools linked to {{ auth.currentCompany.value?.business_name }}</p>
      </div>

      <UiButton variant="primary" @click="isModalOpen = true">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
        </svg>
        <span>Add Fund Account</span>
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
          placeholder="Search fund accounts by code, name, or description..."
          class="input-field pl-9 text-xs"
        />
      </div>
    </div>

    <!-- Fund Accounts Grid -->
    <ClientOnly>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div
          v-for="fund in filteredFunds"
          :key="fund.id"
          class="glass-card p-5 rounded-xl border border-[var(--border-color)] relative overflow-hidden flex flex-col justify-between group cursor-pointer hover:border-emerald-500/40 transition-all"
          @click="openEditModal(fund)"
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

            <div class="mt-3 space-y-2">
              <div class="p-2 rounded-lg flex items-center justify-between">
                <span class="text-[11px] text-[var(--text-muted)] font-semibold">Initial Capital:</span>
                <span class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-mono font-bold text-xs">
                  {{ currencyStore.formatCurrency(fund.amount || 0) }}
                </span>
              </div>
              <div class="p-2 rounded-lg flex items-center justify-between">
                <span class="text-[11px] text-[var(--text-muted)] font-semibold">Available Balance:</span>
                <span class="px-2 py-0.5 rounded font-mono font-bold text-xs" :class="accounting.getFundAccountRemaining(fund.id) < 0 ? 'bg-rose-500/10 text-rose-400 border border-rose-500/20 font-extrabold' : 'bg-blue-500/10 text-blue-400 border border-blue-500/20'">
                  {{ currencyStore.formatCurrency(accounting.getFundAccountRemaining(fund.id)) }}
                </span>
              </div>
            </div>
          </div>

          <div class="pt-4 mt-4 border-t border-[var(--border-color)] flex items-center justify-between text-xs">
            <span class="text-emerald-500 font-semibold">Active</span>
            <span class="text-xs font-bold text-emerald-400 group-hover:underline flex items-center gap-1">
              <span>Edit Account</span>
              <span>&rarr;</span>
            </span>
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
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Initial Fund Amount *</label>
          <input v-model.number="newFund.amount" type="number" step="1000" required placeholder="500000" class="input-field font-mono font-bold text-emerald-400" />
        </div>

        <div>
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Description</label>
          <textarea v-model="newFund.description" rows="3" placeholder="Specify purpose of this fund account..." class="input-field"></textarea>
        </div>

        <div class="pt-4 flex items-center justify-end gap-3 border-t border-[var(--border-color)]">
          <UiButton type="button" variant="secondary" size="sm" @click="isModalOpen = false">Cancel</UiButton>
          <UiButton type="submit" variant="primary" size="sm">Save Fund Account</UiButton>
        </div>
      </form>
    </Modal>

    <!-- Edit Modal -->
    <Modal :isOpen="isEditModalOpen" title="Edit Fund Account" @close="isEditModalOpen = false">
      <form @submit.prevent="handleUpdateFund" class="space-y-4">
        <div>
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Fund Code *</label>
          <input v-model="editingFund.fund_code" type="text" required placeholder="FND-404" class="input-field font-mono" />
        </div>

        <div>
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Fund Name *</label>
          <input v-model="editingFund.fund_name" type="text" required placeholder="Special Operating & Liquidity Reserve" class="input-field" />
        </div>

        <div>
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Initial Capital / Fund Amount *</label>
          <input v-model.number="editingFund.amount" type="number" step="1000" required class="input-field font-mono font-bold text-emerald-400" />
        </div>

        <!-- Live Adjustment Indicator -->
        <div v-if="amountDifference !== 0" class="p-3 rounded-lg border text-xs space-y-1" :class="amountDifference > 0 ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400' : 'bg-rose-500/10 border-rose-500/30 text-rose-400'">
          <div class="font-bold flex items-center justify-between">
            <span>Adjustment Detected:</span>
            <span class="font-mono font-bold">{{ amountDifference > 0 ? '+' : '' }}{{ currencyStore.formatCurrency(amountDifference) }}</span>
          </div>
          <p class="text-[11px] opacity-90">
            An automated journal entry (<strong>{{ amountDifference > 0 ? 'Credit / Fund Addition' : 'Debit / Fund Reduction' }}</strong>) will be posted for <strong>Update in Fund Amount</strong>.
          </p>
        </div>

        <div>
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Description</label>
          <textarea v-model="editingFund.description" rows="3" placeholder="Specify purpose of this fund account..." class="input-field"></textarea>
        </div>

        <div class="pt-4 flex items-center justify-end gap-3 border-t border-[var(--border-color)]">
          <UiButton type="button" variant="secondary" size="sm" @click="isEditModalOpen = false">Cancel</UiButton>
          <UiButton type="submit" variant="primary" size="sm">Save Changes</UiButton>
        </div>
      </form>
    </Modal>

    <!-- Toast Notification -->
    <div
      v-if="showToast"
      class="fixed bottom-6 right-6 bg-emerald-500 text-slate-950 px-4 py-3 rounded-xl shadow-2xl font-bold text-xs flex items-center gap-2.5 z-50 animate-bounce"
    >
      <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
      </svg>
      <span>{{ toastMessage }}</span>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { FundAccount } from '~/composables/useAccounting'

const auth = useAuth()
const currencyStore = useCurrency()
const accounting = useAccounting()

const isModalOpen = ref(false)
const isEditModalOpen = ref(false)
const searchQuery = ref('')
const showToast = ref(false)
const toastMessage = ref('')

watch(showToast, (val) => {
  if (val) {
    setTimeout(() => {
      showToast.value = false
    }, 3000)
  }
})

const filteredFunds = computed(() => {
  const query = searchQuery.value.trim().toLowerCase()
  if (!query) return accounting.fundAccounts.value
  return accounting.fundAccounts.value.filter(fund =>
    fund.fund_code.toLowerCase().includes(query) ||
    fund.fund_name.toLowerCase().includes(query) ||
    (fund.description && fund.description.toLowerCase().includes(query))
  )
})

const newFund = reactive({
  fund_code: '',
  fund_name: '',
  description: '',
  amount: 500000.00,
  company_id: 1,
  user_id: 1,
})

const editingFund = reactive({
  id: 0,
  fund_code: '',
  fund_name: '',
  description: '',
  amount: 0,
  originalAmount: 0,
})

const amountDifference = computed(() => {
  return (editingFund.amount || 0) - editingFund.originalAmount
})

const openEditModal = (fund: FundAccount) => {
  editingFund.id = fund.id
  editingFund.fund_code = fund.fund_code
  editingFund.fund_name = fund.fund_name
  editingFund.description = fund.description || ''
  editingFund.amount = Number(fund.amount) || 0
  editingFund.originalAmount = Number(fund.amount) || 0
  isEditModalOpen.value = true
}

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
  newFund.amount = 500000.00

  toastMessage.value = 'Fund account created successfully!'
  showToast.value = true
}

const handleUpdateFund = async () => {
  const diff = amountDifference.value
  const newAmount = Number(editingFund.amount) || 0

  await accounting.updateFundAccount(editingFund.id, {
    fund_code: editingFund.fund_code,
    fund_name: editingFund.fund_name,
    description: editingFund.description,
    amount: newAmount,
  })

  // If amount changed, automatically record a journal entry for the difference
  if (diff !== 0) {
    const isAddition = diff > 0
    const absDiff = Math.abs(diff)
    const formattedDiff = currencyStore.formatCurrency(absDiff)
    const formattedOld = currencyStore.formatCurrency(editingFund.originalAmount)
    const formattedNew = currencyStore.formatCurrency(newAmount)

    const firstLedgerId = accounting.ledgerAccounts.value[0]?.id || 1
    const firstItemId = accounting.accountItems.value[0]?.id || 1

    await accounting.addJournalEntry({
      fund_account_id: editingFund.id,
      ledger_account_id: firstLedgerId,
      account_item_id: firstItemId,
      amount: absDiff,
      transaction_type: isAddition ? 'credit' : 'debit',
      description: `Update in Fund Amount (${isAddition ? 'Addition' : 'Reduction'}): ${isAddition ? '+' : '-'}${formattedDiff} (From ${formattedOld} to ${formattedNew})`,
      posting_date: new Date().toISOString().split('T')[0],
      status: 'posted',
      is_paid: true,
      user_id: auth.currentUser.value?.id || 1,
    })
  }

  isEditModalOpen.value = false
  toastMessage.value = 'Fund account changes saved successfully!'
  showToast.value = true
}
</script>
