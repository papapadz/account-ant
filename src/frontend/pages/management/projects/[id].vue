<template>
  <div v-if="metrics" class="space-y-6">
    <!-- Top Breadcrumb & Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 glass-card p-5 rounded-xl border border-[#1E293B]">
      <div>
        <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
          <NuxtLink to="/management/projects" class="hover:text-emerald-400 transition-colors">← Projects List</NuxtLink>
          <span>/</span>
          <span class="text-slate-200">Project #{{ project.id }}</span>
        </div>
        <h1 class="text-xl font-bold text-slate-100 tracking-tight flex items-center gap-2">
          {{ project.name }}
          <span
            class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border"
            :class="project.is_government ? 'bg-purple-500/10 text-purple-400 border-purple-500/30' : 'bg-blue-500/10 text-blue-400 border-blue-500/30'"
          >
            {{ project.is_government ? 'Government Project' : 'Private Project' }}
          </span>
        </h1>
        <p class="text-xs text-slate-400 mt-1 flex items-center gap-3">
          <span><strong class="text-slate-300">Client:</strong> {{ project.client_name }}</span>
          <span>•</span>
          <span><strong class="text-slate-300">Location:</strong> {{ project.street ? project.street + ', ' : '' }}{{ project.barangay }}, {{ project.city_name }} (Zip: {{ project.zip }})</span>
        </p>
      </div>

      <div class="flex items-center gap-3">
        <button @click="isFundModalOpen = true" class="btn-secondary text-xs">
          + Select & Add Fund Source (Steps 2-3)
        </button>
        <button @click="isJournalModalOpen = true" class="btn-primary text-xs">
          + Post Project Journal Entry (Step 4)
        </button>
      </div>
    </div>

    <!-- System Running Balance vs Budget Meter (Step 5) -->
    <div class="glass-card p-5 rounded-xl border border-[#1E293B] space-y-4">
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-[#1E293B] pb-4">
        <div>
          <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Step 5: System Running Balance vs Budget Analysis</span>
          <div class="flex items-baseline gap-3 mt-1">
            <span class="text-2xl font-extrabold font-mono text-emerald-400">${{ formatCurrency(metrics.runningBalance) }}</span>
            <span class="text-xs text-slate-400 font-semibold">Current Total Running Balance</span>
          </div>
        </div>

        <div class="flex items-center gap-4">
          <div class="text-right">
            <span class="text-[10px] text-slate-400 uppercase tracking-wider block">Budget Utilized</span>
            <span
              class="text-lg font-bold font-mono"
              :class="metrics.isOverBudget ? 'text-rose-400' : 'text-emerald-400'"
            >
              {{ metrics.budgetUtilizedPercentage }}%
            </span>
          </div>

          <div
            class="px-3 py-1.5 rounded-lg border text-xs font-bold uppercase tracking-wider flex items-center gap-1.5"
            :class="metrics.isOverBudget ? 'bg-rose-500/20 text-rose-400 border-rose-500/40' : 'bg-emerald-500/20 text-emerald-400 border-emerald-500/40'"
          >
            <span class="w-2 h-2 rounded-full" :class="metrics.isOverBudget ? 'bg-rose-400 animate-pulse' : 'bg-emerald-400'"></span>
            {{ metrics.isOverBudget ? 'OVER BUDGET WARNING' : 'BUDGET NORMAL' }}
          </div>
        </div>
      </div>

      <!-- Financial Metrics Grid -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-1">
        <div class="bg-[#0F172A] border border-[#1E293B] p-3.5 rounded-lg">
          <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider block">Total Project Budget</span>
          <span class="text-lg font-bold font-mono text-slate-100">${{ formatCurrency(project.budget) }}</span>
        </div>

        <div class="bg-[#0F172A] border border-[#1E293B] p-3.5 rounded-lg">
          <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider block">Total Initial Funds</span>
          <span class="text-lg font-bold font-mono text-blue-400">${{ formatCurrency(metrics.totalAllocatedFunds) }}</span>
        </div>

        <div class="bg-[#0F172A] border border-[#1E293B] p-3.5 rounded-lg">
          <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider block">Total Net Expenses</span>
          <span class="text-lg font-bold font-mono text-amber-400">${{ formatCurrency(metrics.netExpenses) }}</span>
        </div>

        <div class="bg-[#0F172A] border border-[#1E293B] p-3.5 rounded-lg">
          <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider block">Available Fund Surplus</span>
          <span class="text-lg font-bold font-mono text-emerald-400">${{ formatCurrency(metrics.runningBalance) }}</span>
        </div>
      </div>

      <!-- Progress Meter Bar -->
      <div class="space-y-1 pt-2">
        <div class="flex items-center justify-between text-xs font-mono text-slate-400">
          <span>$0.00 Spent</span>
          <span>Expenses: ${{ formatCurrency(metrics.netExpenses) }} / Budget: ${{ formatCurrency(project.budget) }}</span>
        </div>
        <div class="w-full bg-slate-800 rounded-full h-3 overflow-hidden p-0.5 border border-[#334155]">
          <div
            class="h-2 rounded-full transition-all duration-500"
            :class="metrics.isOverBudget ? 'bg-gradient-to-r from-amber-500 to-rose-500' : 'bg-gradient-to-r from-blue-500 to-emerald-500'"
            :style="{ width: Math.min(metrics.budgetUtilizedPercentage, 100) + '%' }"
          ></div>
        </div>
      </div>
    </div>

    <!-- Steps 2 & 3: Project Fund Sources & Initial Amounts Panel -->
    <div class="glass-card rounded-xl border border-[#1E293B] p-5 space-y-4">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-sm font-bold text-slate-100 uppercase tracking-wider flex items-center gap-2">
            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Steps 2 & 3: Project Fund Sources & Initial Amounts
          </h2>
          <p class="text-xs text-slate-400 mt-0.5">Fund accounts assigned to back this project and their current running balances</p>
        </div>

        <button @click="isFundModalOpen = true" class="btn-secondary py-1.5 px-3 text-xs">
          + Allocate Fund Source
        </button>
      </div>

      <div v-if="metrics.fundBreakdown.length === 0" class="p-8 text-center bg-[#0B1120] rounded-xl border border-[#1E293B]">
        <p class="text-xs text-slate-400">No fund accounts allocated to this project yet.</p>
        <button @click="isFundModalOpen = true" class="btn-primary mt-3 text-xs py-1.5 px-4">
          + Add Initial Fund Source
        </button>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div
          v-for="fund in metrics.fundBreakdown"
          :key="fund.fund_account_id"
          class="bg-[#0F172A] border border-[#1E293B] p-4 rounded-xl space-y-3"
        >
          <div class="flex items-center justify-between border-b border-[#1E293B] pb-2">
            <div>
              <span class="font-mono text-xs font-bold text-emerald-400 block">{{ fund.fund_code }}</span>
              <span class="text-xs font-bold text-slate-200">{{ fund.fund_name }}</span>
            </div>
            <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-blue-500/10 text-blue-400 border border-blue-500/20">
              Assigned Source
            </span>
          </div>

          <div class="grid grid-cols-3 gap-2 text-xs font-mono">
            <div>
              <span class="text-[10px] text-slate-400 block uppercase">Initial Amount</span>
              <span class="font-bold text-slate-200">${{ formatCurrency(fund.initial_amount) }}</span>
            </div>
            <div>
              <span class="text-[10px] text-slate-400 block uppercase">Net Expenses</span>
              <span class="font-bold text-amber-400">${{ formatCurrency(fund.net_expense) }}</span>
            </div>
            <div>
              <span class="text-[10px] text-slate-400 block uppercase">Running Balance</span>
              <span class="font-bold text-emerald-400">${{ formatCurrency(fund.running_balance) }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Step 4: Project Journal Ledger Entries Panel -->
    <div class="glass-card rounded-xl border border-[#1E293B] overflow-hidden">
      <div class="p-4 border-b border-[#1E293B] flex flex-col sm:flex-row items-center justify-between gap-3">
        <div>
          <h2 class="text-sm font-bold text-slate-100 uppercase tracking-wider flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Step 4: Project Journal Ledger Entries ({{ metrics.journalEntries.length }})
          </h2>
          <p class="text-xs text-slate-400 mt-0.5">Double-entry debit and credit journal items posted directly for {{ project.name }}</p>
        </div>

        <button @click="isJournalModalOpen = true" class="btn-primary text-xs py-1.5 px-3">
          + Post Project Journal Entry
        </button>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="text-[11px] text-slate-400 uppercase tracking-wider bg-[#0B1120] border-b border-[#1E293B]">
              <th class="p-3.5 font-semibold">Entry #</th>
              <th class="p-3.5 font-semibold">Fund Source Used</th>
              <th class="p-3.5 font-semibold">Ledger Account</th>
              <th class="p-3.5 font-semibold">Type</th>
              <th class="p-3.5 font-semibold text-right">Debit (Dr)</th>
              <th class="p-3.5 font-semibold text-right">Credit (Cr)</th>
              <th class="p-3.5 font-semibold">Description / Memo</th>
              <th class="p-3.5 font-semibold text-right">Date</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#1E293B]/60 text-xs">
            <tr v-if="metrics.journalEntries.length === 0">
              <td colspan="8" class="p-8 text-center text-slate-400">
                No journal entries posted for this project yet.
              </td>
            </tr>
            <tr v-for="entry in metrics.journalEntries" :key="entry.id" class="hover:bg-[#1E293B]/40 transition-colors">
              <td class="p-3.5 font-mono text-slate-400">#{{ entry.id }}</td>
              <td class="p-3.5 font-mono font-bold text-emerald-400">
                {{ getFundCode(entry.fund_account_id) }}
              </td>
              <td class="p-3.5 font-mono font-bold text-slate-200">
                {{ getAccountCode(entry.ledger_account_id) }}
              </td>
              <td class="p-3.5">
                <span
                  class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider"
                  :class="entry.transaction_type === 'debit' ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20'"
                >
                  {{ entry.transaction_type }}
                </span>
              </td>
              <td class="p-3.5 text-right font-mono font-bold text-blue-400">
                <span v-if="entry.transaction_type === 'debit'">${{ formatCurrency(entry.amount) }}</span>
                <span v-else class="text-slate-600">—</span>
              </td>
              <td class="p-3.5 text-right font-mono font-bold text-amber-400">
                <span v-if="entry.transaction_type === 'credit'">${{ formatCurrency(entry.amount) }}</span>
                <span v-else class="text-slate-600">—</span>
              </td>
              <td class="p-3.5 text-slate-300 max-w-xs truncate">{{ entry.description || 'N/A' }}</td>
              <td class="p-3.5 text-right font-mono text-slate-500 text-[11px]">{{ entry.created_at }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal for Steps 2 & 3: Add/Allocate Project Fund Source & Initial Amount -->
    <Modal :isOpen="isFundModalOpen" title="Steps 2 & 3: Allocate Fund Source & Initial Amount" @close="isFundModalOpen = false">
      <form @submit.prevent="handleAllocateFund" class="space-y-4">
        <div>
          <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Step 2: Select Fund Source Account *</label>
          <select v-model="fundForm.fund_account_id" required class="input-field">
            <option v-for="fund in accounting.fundAccounts.value" :key="fund.id" :value="fund.id">
              {{ fund.fund_code }} - {{ fund.fund_name }}
            </option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Step 3: Initial Allocated Amount ($ USD) *</label>
          <input
            v-model.number="fundForm.initial_amount"
            type="number"
            step="0.01"
            required
            placeholder="250000.00"
            class="input-field font-mono font-bold text-emerald-400"
          />
          <p class="text-[11px] text-slate-400 mt-1">This sets the initial capital cap for this fund source for {{ project.name }}.</p>
        </div>

        <div class="pt-4 flex items-center justify-end gap-3 border-t border-[#1E293B]">
          <button type="button" @click="isFundModalOpen = false" class="btn-secondary py-2 px-4 text-xs">Cancel</button>
          <button type="submit" class="btn-primary py-2 px-5 text-xs font-bold">Allocate Fund to Project</button>
        </div>
      </form>
    </Modal>

    <!-- Modal for Step 4: Post Project Journal Entry -->
    <Modal :isOpen="isJournalModalOpen" title="Step 4: Post Project Journal Transaction" @close="isJournalModalOpen = false">
      <form @submit.prevent="handlePostJournal" class="space-y-4">
        <div>
          <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Transaction Type *</label>
          <div class="grid grid-cols-2 gap-3">
            <button
              type="button"
              @click="journalForm.transaction_type = 'debit'"
              class="py-2 px-4 rounded-lg font-bold text-xs flex items-center justify-center gap-2 border transition-all"
              :class="journalForm.transaction_type === 'debit' ? 'bg-blue-500/20 text-blue-400 border-blue-500' : 'bg-[#0F172A] text-slate-400 border-[#334155]'"
            >
              Debit Entry (Dr)
            </button>
            <button
              type="button"
              @click="journalForm.transaction_type = 'credit'"
              class="py-2 px-4 rounded-lg font-bold text-xs flex items-center justify-center gap-2 border transition-all"
              :class="journalForm.transaction_type === 'credit' ? 'bg-amber-500/20 text-amber-400 border-amber-500' : 'bg-[#0F172A] text-slate-400 border-[#334155]'"
            >
              Credit Entry (Cr)
            </button>
          </div>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Available Project Fund Source *</label>
          <select v-model="journalForm.fund_account_id" required class="input-field">
            <option v-if="availableProjectFunds.length === 0" disabled value="">
              No funds allocated yet. Allocate a fund source first!
            </option>
            <option v-for="fund in availableProjectFunds" :key="fund.id" :value="fund.id">
              {{ fund.fund_code }} - {{ fund.fund_name }} (Avail: ${{ formatCurrency(getFundRunningBal(fund.id)) }})
            </option>
          </select>
          <p class="text-[10px] text-emerald-400 mt-1">Restricted exclusively to fund accounts allocated to {{ project.name }}.</p>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Target Ledger Account *</label>
          <select v-model="journalForm.ledger_account_id" required class="input-field">
            <option v-for="acc in accounting.ledgerAccounts.value" :key="acc.id" :value="acc.id">
              {{ acc.account_code }} - {{ acc.account_name }}
            </option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Master Account Item *</label>
          <select v-model="journalForm.account_item_id" required class="input-field">
            <option v-for="item in accounting.accountItems.value" :key="item.id" :value="item.id">
              {{ item.item_code }} - {{ item.item_name }}
            </option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Transaction Amount ($ USD) *</label>
          <input
            v-model.number="journalForm.amount"
            type="number"
            step="0.01"
            required
            placeholder="15000.00"
            class="input-field font-mono font-bold text-emerald-400"
          />
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Project Memo / Purpose</label>
          <textarea v-model="journalForm.description" rows="2" placeholder="Describe operational purpose of entry..." class="input-field"></textarea>
        </div>

        <div class="pt-4 flex items-center justify-end gap-3 border-t border-[#1E293B]">
          <button type="button" @click="isJournalModalOpen = false" class="btn-secondary py-2 px-4 text-xs">Cancel</button>
          <button type="submit" class="btn-primary py-2 px-5 text-xs font-bold" :disabled="availableProjectFunds.length === 0">
            Post Journal Entry to Project
          </button>
        </div>
      </form>
    </Modal>
  </div>
</template>

<script setup lang="ts">
const route = useRoute()
const projectId = computed(() => Number(route.params.id))

const projectStore = useProjects()
const accounting = useAccounting()

const metrics = computed(() => projectStore.getProjectMetrics(projectId.value))
const project = computed(() => metrics.value?.project)

const isFundModalOpen = ref(false)
const isJournalModalOpen = ref(false)

const fundForm = reactive({
  fund_account_id: 1,
  initial_amount: 100000.00,
})

const journalForm = reactive({
  fund_account_id: 1,
  ledger_account_id: 10,
  account_item_id: 1,
  amount: 25000.00,
  transaction_type: 'debit' as 'debit' | 'credit',
  description: '',
})

// Fund accounts allocated to this project
const availableProjectFunds = computed(() => {
  if (!metrics.value) return []
  const fundIds = metrics.value.funds.map(f => f.fund_account_id)
  return accounting.fundAccounts.value.filter(fa => fundIds.includes(fa.id))
})

// Automatically pick first available fund source when journal modal opens
watch(availableProjectFunds, (list) => {
  if (list.length > 0 && !list.some(f => f.id === journalForm.fund_account_id)) {
    journalForm.fund_account_id = list[0].id
  }
}, { immediate: true })

const getFundRunningBal = (fundId: number) => {
  const fund = metrics.value?.fundBreakdown.find(f => f.fund_account_id === fundId)
  return fund ? fund.running_balance : 0
}

const getAccountCode = (id: number) => {
  const acc = accounting.ledgerAccounts.value.find(a => a.id === id)
  return acc ? acc.account_code : `#${id}`
}

const getFundCode = (id?: number) => {
  if (!id) return 'General'
  const fund = accounting.fundAccounts.value.find(f => f.id === id)
  return fund ? fund.fund_code : `FND-${id}`
}

onMounted(async () => {
  await projectStore.fetchAll()
})

const handleAllocateFund = async () => {
  await projectStore.addProjectFund(projectId.value, fundForm.fund_account_id, fundForm.initial_amount)
  isFundModalOpen.value = false
  // Reset default
  fundForm.initial_amount = 100000.00
}

const handlePostJournal = async () => {
  await accounting.addJournalEntry({
    ledger_account_id: journalForm.ledger_account_id,
    fund_account_id: journalForm.fund_account_id,
    project_id: projectId.value,
    account_item_id: journalForm.account_item_id,
    amount: journalForm.amount,
    transaction_type: journalForm.transaction_type,
    description: journalForm.description || `Project #${projectId.value} journal transaction`,
    user_id: 1,
  })

  isJournalModalOpen.value = false
  journalForm.description = ''
  journalForm.amount = 25000.00
}

const formatCurrency = (val: number) => {
  return new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(val)
}
</script>
