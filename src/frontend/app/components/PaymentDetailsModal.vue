<template>
  <Modal
    :is-open="isOpen"
    title="Payment Settlement Details"
    max-width="max-w-md"
    @close="handleClose"
  >
    <div v-if="entry" class="space-y-4 text-xs">
      <!-- Status & Entry Header Card -->
      <div class="bg-[var(--bg-surface)] p-4 rounded-xl border border-[var(--border-color)] space-y-2.5">
        <div class="flex items-center justify-between">
          <span class="font-mono text-[10px] font-bold text-emerald-500 uppercase tracking-wider bg-emerald-500/10 px-2 py-0.5 rounded border border-emerald-500/20 flex items-center gap-1">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse" />
            Paid / Settled
          </span>
          <span class="font-mono text-xs text-[var(--text-muted)] font-bold">
            Entry #{{ entry.id }}
          </span>
        </div>

        <div class="flex items-baseline justify-between pt-1">
          <div>
            <h4 class="font-bold text-[var(--text-main)] text-sm line-clamp-1">
              {{ entry.description || entry.note || 'Journal Entry Settlement' }}
            </h4>
            <p v-if="vendorName" class="text-[11px] text-[var(--text-muted)] mt-0.5">
              Payee: <span class="font-bold text-[var(--text-main)]">{{ vendorName }}</span>
            </p>
          </div>
          <span class="font-mono font-bold text-base text-emerald-400 shrink-0 ml-2">
            {{ currencyStore.formatCurrency(entry.amount || 0) }}
          </span>
        </div>
      </div>

      <!-- Payment Audit Details Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <!-- Payment Date -->
        <div class="bg-[var(--bg-app)] p-3 rounded-lg border border-[var(--border-color)] space-y-0.5">
          <span class="text-[10px] font-bold text-[var(--text-muted)] uppercase tracking-wider block">Payment Date</span>
          <span class="font-mono font-semibold text-[var(--text-main)] text-xs block">
            {{ entry.payment_date || entry.posting_date || 'N/A' }}
          </span>
        </div>

        <!-- Disbursement Fund Source -->
        <div class="bg-[var(--bg-app)] p-3 rounded-lg border border-[var(--border-color)] space-y-0.5">
          <span class="text-[10px] font-bold text-[var(--text-muted)] uppercase tracking-wider block">Fund Source</span>
          <span class="font-semibold text-[var(--text-main)] text-xs block truncate" :title="fundName">
            {{ fundName }}
          </span>
        </div>
      </div>

      <!-- Additional Metadata (Project / Ledger Account) -->
      <div v-if="projectName || ledgerAccountName" class="bg-[var(--bg-app)] p-3 rounded-lg border border-[var(--border-color)] space-y-1.5 text-[11px]">
        <div v-if="projectName" class="flex items-center justify-between">
          <span class="text-[var(--text-muted)]">Project:</span>
          <span class="font-semibold text-[var(--text-main)] truncate max-w-[180px]">{{ projectName }}</span>
        </div>
        <div v-if="ledgerAccountName" class="flex items-center justify-between">
          <span class="text-[var(--text-muted)]">Ledger Account:</span>
          <span class="font-semibold text-[var(--text-main)] truncate max-w-[180px]">{{ ledgerAccountName }}</span>
        </div>
      </div>

      <!-- Payment Remarks -->
      <div class="space-y-1">
        <label class="block font-bold text-[var(--text-main)] text-[11px]">
          Payment Remarks / Reference Notes
        </label>
        <div class="bg-[var(--bg-app)] p-3 rounded-lg border border-[var(--border-color)] text-xs text-[var(--text-main)] font-mono min-h-[60px] whitespace-pre-wrap">
          {{ entry.payment_remarks || 'No payment remarks recorded.' }}
        </div>
      </div>

      <!-- Action Footer -->
      <div class="flex items-center justify-between pt-3 border-t border-[var(--border-color)]">
        <UiButton
          type="button"
          variant="secondary"
          size="sm"
          class="!text-rose-400 border-rose-500/30 hover:!bg-rose-500/10"
          @click="handleUnmarkPaid"
        >
          Revert to Unpaid
        </UiButton>
        <UiButton type="button" variant="primary" size="sm" @click="handleClose">
          Close Details
        </UiButton>
      </div>
    </div>
  </Modal>
</template>

<script setup lang="ts">
const props = defineProps<{
  isOpen: boolean
  entry?: any
  fundAccounts?: Array<{ id: number; fund_name: string; fund_code?: string; name?: string }>
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'unmarkPaid', entry: any): void
}>()

const currencyStore = useCurrency()

const vendorName = computed(() => {
  if (!props.entry) return ''
  return props.entry.accounts_payable?.name || props.entry.accounts_payable_name || ''
})

const fundName = computed(() => {
  if (!props.entry) return 'Corporate Operating Fund'
  if (props.entry.fundAccount) {
    return `${props.entry.fundAccount.fund_name} ${props.entry.fundAccount.fund_code ? `(${props.entry.fundAccount.fund_code})` : ''}`
  }
  const fundId = props.entry.fund_account_id || props.entry.fund_source_id
  if (fundId && props.fundAccounts) {
    const f = props.fundAccounts.find(fa => fa.id === fundId)
    if (f) return `${f.fund_name || f.name} ${f.fund_code ? `(${f.fund_code})` : ''}`
  }
  return 'General Fund Source'
})

const projectName = computed(() => {
  if (!props.entry) return ''
  return props.entry.project?.name || props.entry.project?.project_name || ''
})

const ledgerAccountName = computed(() => {
  if (!props.entry) return ''
  return props.entry.ledgerAccount?.account_name || props.entry.ledger_account?.account_name || ''
})

const handleClose = () => {
  emit('close')
}

const handleUnmarkPaid = () => {
  if (!props.entry) return
  emit('unmarkPaid', props.entry)
}
</script>
