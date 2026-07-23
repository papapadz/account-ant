<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 glass-card p-5 rounded-xl border border-[#1E293B]">
      <div>
        <h1 class="text-lg font-bold text-slate-100 tracking-tight flex items-center gap-2">
          <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          Ledger Accounts (Chart of Accounts)
        </h1>
        <p class="text-xs text-slate-400 mt-0.5">Hierarchical general ledger accounts linked to fund accounts</p>
      </div>

      <button @click="isModalOpen = true" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
        </svg>
        <span>+ Add Ledger Account</span>
      </button>
    </div>

    <!-- Ledger Accounts Table -->
    <div class="glass-card rounded-xl border border-[#1E293B] overflow-hidden">
      <div class="p-4 border-b border-[#1E293B] flex items-center justify-between gap-4">
        <div class="text-xs font-semibold text-slate-300 uppercase tracking-wider">Active General Ledger Accounts</div>
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search by code or account name..."
          class="input-field max-w-xs py-1.5 text-xs"
        />
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="text-[11px] text-slate-400 uppercase tracking-wider bg-[#0B1120] border-b border-[#1E293B]">
              <th class="p-3.5 font-semibold">Account Code</th>
              <th class="p-3.5 font-semibold">Account Name</th>
              <th class="p-3.5 font-semibold">Parent Fund Account</th>
              <th class="p-3.5 font-semibold">Description</th>
              <th class="p-3.5 font-semibold text-right">Created Date</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#1E293B]/60 text-xs">
            <tr v-for="acc in filteredAccounts" :key="acc.id" class="hover:bg-[#1E293B]/40 transition-colors">
              <td class="p-3.5 font-mono font-bold text-emerald-400">{{ acc.account_code }}</td>
              <td class="p-3.5 font-semibold text-slate-100">{{ acc.account_name }}</td>
              <td class="p-3.5">
                <span class="bg-blue-500/10 text-blue-400 border border-blue-500/20 px-2 py-0.5 rounded font-mono text-[11px]">
                  {{ getFundCode(acc.fund_account_id) }}
                </span>
              </td>
              <td class="p-3.5 text-slate-400 max-w-md truncate">{{ acc.description || 'N/A' }}</td>
              <td class="p-3.5 text-right font-mono text-slate-500 text-[11px]">{{ acc.created_at }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Create Modal -->
    <Modal :isOpen="isModalOpen" title="Create New Ledger Account" @close="isModalOpen = false">
      <form @submit.prevent="handleCreateAccount" class="space-y-4">
        <div>
          <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Account Code *</label>
          <input v-model="newAcc.account_code" type="text" required placeholder="1030-PREPAID" class="input-field font-mono" />
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Account Name *</label>
          <input v-model="newAcc.account_name" type="text" required placeholder="Prepaid Software Subscriptions" class="input-field" />
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Parent Fund Account *</label>
          <select v-model="newAcc.fund_account_id" class="input-field">
            <option v-for="fund in accounting.fundAccounts.value" :key="fund.id" :value="fund.id">
              {{ fund.fund_code }} - {{ fund.fund_name }}
            </option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Description</label>
          <textarea v-model="newAcc.description" rows="3" placeholder="Account purpose..." class="input-field"></textarea>
        </div>

        <div class="pt-4 flex items-center justify-end gap-3 border-t border-[#1E293B]">
          <button type="button" @click="isModalOpen = false" class="btn-secondary py-2 px-4 text-xs">Cancel</button>
          <button type="submit" class="btn-primary py-2 px-5 text-xs font-bold">Save Ledger Account</button>
        </div>
      </form>
    </Modal>
  </div>
</template>

<script setup lang="ts">
const accounting = useAccounting()
const searchQuery = ref('')
const isModalOpen = ref(false)

const newAcc = reactive({
  account_code: '',
  account_name: '',
  description: '',
  fund_account_id: 1,
  user_id: 1,
  ledger_account_id: 1,
})

const getFundCode = (fundId: number) => {
  const f = accounting.fundAccounts.value.find(item => item.id === fundId)
  return f ? f.fund_code : `FND-#${fundId}`
}

const filteredAccounts = computed(() => {
  if (!searchQuery.value) return accounting.ledgerAccounts.value
  const q = searchQuery.value.toLowerCase()
  return accounting.ledgerAccounts.value.filter(
    a => a.account_code.toLowerCase().includes(q) || a.account_name.toLowerCase().includes(q)
  )
})

onMounted(async () => {
  await accounting.fetchAll()
})

const handleCreateAccount = async () => {
  await accounting.addLedgerAccount({ ...newAcc })
  isModalOpen.value = false
  newAcc.account_code = ''
  newAcc.account_name = ''
  newAcc.description = ''
}
</script>
