<template>
  <div>
    <button
      type="button"
      class="text-[11px] font-semibold text-emerald-400 hover:text-emerald-300"
      @click="toggle"
    >
      {{ open ? 'Cancel' : `+ New ${copy.label}` }}
    </button>

    <div
      v-if="open"
      class="mt-1.5 p-3 rounded-lg bg-[var(--bg-surface)] border border-emerald-500/30 space-y-2.5"
    >
      <div v-if="kind === 'item'" class="space-y-1">
        <label class="block text-[11px] font-medium text-[var(--text-muted)] uppercase tracking-wider">
          Linked Ledger Account <span class="text-rose-400">*</span>
        </label>
        <select v-model="form.ledger_account_id" class="input-field text-sm" @change="suggestItemCode">
          <option value="" disabled>-- Select Ledger Account --</option>
          <option v-for="acc in accounting.ledgerAccounts.value" :key="acc.id" :value="acc.id">
            [{{ acc.account_code }}] {{ acc.account_name }}
          </option>
        </select>
      </div>

      <div class="grid grid-cols-3 gap-2">
        <input
          v-model="form.code"
          type="text"
          maxlength="20"
          :placeholder="copy.code"
          class="input-field col-span-1 font-mono text-sm"
          @keydown.enter.prevent="save"
        />
        <input
          v-model="form.name"
          type="text"
          maxlength="100"
          :placeholder="copy.name"
          class="input-field col-span-2 text-sm"
          @keydown.enter.prevent="save"
        />
      </div>

      <div v-if="kind === 'fund'" class="space-y-1">
        <label class="block text-[11px] font-medium text-[var(--text-muted)] uppercase tracking-wider">
          Opening amount (optional)
        </label>
        <input
          v-model.number="form.amount"
          type="number"
          min="0"
          step="0.01"
          class="input-field font-mono text-sm"
          @keydown.enter.prevent="save"
        />
      </div>

      <div v-if="kind === 'item'" class="grid grid-cols-2 gap-2">
        <button
          v-for="opt in typeOptions"
          :key="opt.value"
          type="button"
          class="px-3 py-1.5 rounded-lg border text-xs font-semibold transition-colors"
          :class="form.transaction_type === opt.value
            ? 'bg-emerald-500/10 border-emerald-500/40 text-emerald-400'
            : 'border-[var(--border-color)] text-[var(--text-muted)]'"
          @click="form.transaction_type = opt.value"
        >
          {{ opt.label }}
        </button>
      </div>

      <p v-if="error" class="text-xs text-rose-400">{{ error }}</p>
      <UiButton type="button" size="sm" :loading="saving" @click="save">
        Create {{ copy.label }}
      </UiButton>
    </div>
  </div>
</template>

<script setup lang="ts">
// Inline "+ New" for the journal entry modals. The parent decides what to do with the new record in `afterCreate`
// (select it, refetch, link it to a project); if that throws, the message shows in the panel.
const props = defineProps<{
  kind: 'fund' | 'ledger' | 'item'
  ledgerId?: number | string // item: ledger account to preselect
  transactionType?: 'debit' | 'credit' // item: Outflow/Inflow to preselect
  afterCreate?: (id: number) => unknown
}>()

const accounting = useAccounting()
const auth = useAuth()

const copy = computed(() => ({
  fund: { label: 'Fund Account', code: 'Code e.g. FND-101', name: 'Fund name' },
  ledger: { label: 'Ledger Account', code: 'Code e.g. 1030-PREPAID', name: 'Account name' },
  item: { label: 'Account Item', code: 'Item code', name: 'Item name' },
})[props.kind])

const typeOptions = [
  { value: 'debit', label: 'Outflow' },
  { value: 'credit', label: 'Inflow' },
] as const

const open = ref(false)
const saving = ref(false)
const error = ref('')
const form = reactive({
  code: '',
  name: '',
  amount: 0,
  ledger_account_id: '' as number | string,
  transaction_type: 'debit' as 'debit' | 'credit',
})
let lastSuggestedCode = ''

// Suggest ACCOUNTCODE-NN for the picked ledger account, unless the user already typed their own code
const suggestItemCode = () => {
  const ledger = accounting.ledgerAccounts.value.find(a => a.id === Number(form.ledger_account_id))
  if (!ledger) return
  if (form.code && form.code !== lastSuggestedCode) return
  const count = accounting.accountItems.value.filter(i => i.ledger_account_id === ledger.id).length
  const cleanCode = ledger.account_code.replace(/[^a-zA-Z0-9]/g, '-').toUpperCase()
  lastSuggestedCode = `${cleanCode}-${String(count + 1).padStart(2, '0')}`
  form.code = lastSuggestedCode
}

const toggle = () => {
  open.value = !open.value
  if (!open.value) return
  error.value = ''
  lastSuggestedCode = ''
  Object.assign(form, {
    code: '',
    name: '',
    amount: 0,
    ledger_account_id: props.ledgerId || '',
    transaction_type: props.transactionType || 'debit',
  })
  if (props.kind === 'item') suggestItemCode()
}

const save = async () => {
  const code = form.code.trim()
  const name = form.name.trim()
  const ledgerId = Number(form.ledger_account_id)
  const amount = Number(form.amount) || 0
  if (props.kind === 'item' && !ledgerId) {
    error.value = 'Select the ledger account this item belongs to.'
    return
  }
  if (!code || !name) {
    error.value = 'Code and name are required.'
    return
  }
  if (props.kind === 'fund' && amount < 0) {
    error.value = 'Opening amount cannot be negative.'
    return
  }
  saving.value = true
  error.value = ''
  try {
    const created = props.kind === 'fund'
      ? await accounting.addFundAccount({ fund_code: code, fund_name: name, amount, company_id: auth.currentCompany.value?.id || 1 })
      : props.kind === 'ledger'
        ? await accounting.addLedgerAccount({ account_code: code, account_name: name })
        : await accounting.addAccountItem({ item_code: code, item_name: name, transaction_type: form.transaction_type, ledger_account_id: ledgerId })
    await props.afterCreate?.(created.id)
    open.value = false
  } catch (err: any) {
    error.value = err?.data?.message || err?.message || `Failed to create ${copy.value.label}.`
  } finally {
    saving.value = false
  }
}
</script>
