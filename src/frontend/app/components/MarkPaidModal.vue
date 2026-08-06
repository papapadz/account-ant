<template>
  <Modal
    :is-open="isOpen"
    title="Settle Payment"
    max-width="max-w-md"
    @close="handleClose"
  >
    <div v-if="entry" class="space-y-4 text-xs">
      <!-- Entry Summary Card -->
      <div class="bg-[var(--bg-surface)] p-3.5 rounded-xl border border-[var(--border-color)] space-y-2">
        <div class="flex items-center justify-between">
          <span class="font-mono text-[10px] font-bold text-amber-500 uppercase tracking-wider bg-amber-500/10 px-2 py-0.5 rounded border border-amber-500/20">
            Payable #{{ entry.id }}
          </span>
          <span class="font-mono font-bold text-sm text-[var(--text-main)]">
            {{ currencyStore.formatCurrency(entry.amount || 0) }}
          </span>
        </div>

        <div>
          <h4 class="font-bold text-[var(--text-main)] text-xs line-clamp-1">
            {{ entry.description || entry.note || 'Journal Entry Settlement' }}
          </h4>
          <p v-if="vendorName" class="text-[11px] text-[var(--text-muted)] mt-0.5 flex items-center gap-1.5">
            <span class="font-semibold text-[var(--text-main)]">Payee / Vendor:</span>
            <span class="bg-[var(--bg-app)] border border-[var(--border-color)] px-1.5 py-0.5 rounded text-[10px] font-mono text-emerald-400 font-bold">
              {{ vendorName }}
            </span>
          </p>
        </div>
      </div>

      <!-- Form Inputs -->
      <form @submit.prevent="handleConfirm" class="space-y-3.5">
        <!-- Payment Date Field -->
        <div>
          <div class="flex items-center justify-between mb-1">
            <label class="block font-bold text-[var(--text-main)] text-[11px]">
              Payment Date <span class="text-rose-500">*</span>
            </label>
            <button
              type="button"
              class="text-[10px] font-bold text-emerald-500 hover:text-emerald-400 transition-colors"
              @click="setToday"
            >
              Set Today
            </button>
          </div>
          <input
            v-model="paymentDate"
            type="date"
            required
            class="w-full bg-[var(--bg-app)] border border-[var(--border-color)] rounded-lg px-3 py-2 text-xs text-[var(--text-main)] focus:outline-none focus:border-emerald-500 transition-colors font-mono"
          />
        </div>

        <!-- Fund Account Selector -->
        <div v-if="fundAccountsList.length > 0">
          <label class="block font-bold text-[var(--text-main)] text-[11px] mb-1">
            Disbursement Fund Account <span class="text-rose-500">*</span>
          </label>
          <select
            v-model="selectedFundAccountId"
            required
            class="w-full bg-[var(--bg-app)] border border-[var(--border-color)] rounded-lg px-3 py-2 text-xs text-[var(--text-main)] focus:outline-none focus:border-emerald-500 transition-colors"
          >
            <option v-for="fund in fundAccountsList" :key="fund.id" :value="fund.id">
              {{ fund.fund_name }} {{ fund.fund_code ? `(${fund.fund_code})` : '' }}
            </option>
          </select>
        </div>

        <!-- Payment Remarks / Notes -->
        <div>
          <label class="block font-bold text-[var(--text-main)] text-[11px] mb-1">
            Payment Remarks / Reference
          </label>
          <textarea
            v-model="paymentRemarks"
            rows="3"
            placeholder="e.g. Check #10892, BDO Bank Transfer Ref #92831, or settlement notes..."
            class="w-full bg-[var(--bg-app)] border border-[var(--border-color)] rounded-lg p-2.5 text-xs text-[var(--text-main)] focus:outline-none focus:border-emerald-500 transition-colors resize-none"
          ></textarea>
        </div>

        <!-- Action Footer -->
        <div class="flex items-center justify-end gap-2 pt-3 border-t border-[var(--border-color)]">
          <UiButton type="button" variant="secondary" size="sm" @click="handleClose">
            Cancel
          </UiButton>          <UiButton
            type="submit"
            variant="primary"
            size="sm"
            :disabled="isSubmitting || !paymentDate"
            class="!bg-emerald-600 hover:!bg-emerald-500"
          >
            <svg v-if="!isSubmitting" class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <svg v-else class="w-4 h-4 mr-1 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Confirm Settlement
          </UiButton>
        </div>
      </form>
    </div>
  </Modal>
</template>

<script setup lang="ts">
const props = defineProps<{
  isOpen: boolean
  entry?: any
  fundAccounts?: Array<{ id: number; fund_name: string; fund_code?: string; name?: string }>
  isSubmitting?: boolean
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'confirm', payload: {
    id: number
    is_paid: boolean
    payment_date: string
    payment_remarks: string
    fund_account_id?: number
    fund_source_id?: number
    accounts_payable_name?: string
  }): void
}>()

const currencyStore = useCurrency()

const paymentDate = ref(new Date().toISOString().split('T')[0])
const paymentRemarks = ref('')
const selectedFundAccountId = ref<number | undefined>(undefined)

const vendorName = computed(() => {
  if (!props.entry) return ''
  return props.entry.accounts_payable_name || props.entry.accounts_payable?.name || ''
})

const fundAccountsList = computed(() => {
  if (!props.fundAccounts) return []
  return props.fundAccounts.map(f => ({
    id: f.id,
    fund_name: f.fund_name || f.name || `Fund #${f.id}`,
    fund_code: f.fund_code || '',
  }))
})

watch(() => props.isOpen, (newVal) => {
  if (newVal && props.entry) {
    paymentDate.value = props.entry.payment_date || new Date().toISOString().split('T')[0]
    paymentRemarks.value = props.entry.payment_remarks || ''
    const defaultFundId = props.entry.fund_account_id || props.entry.fund_source_id || (fundAccountsList.value[0]?.id)
    selectedFundAccountId.value = defaultFundId
  }
}, { immediate: true })

const setToday = () => {
  paymentDate.value = new Date().toISOString().split('T')[0]
}

const handleClose = () => {
  emit('close')
}

const handleConfirm = () => {
  if (!props.entry || !paymentDate.value) return
  emit('confirm', {
    id: props.entry.id,
    is_paid: true,
    payment_date: paymentDate.value,
    payment_remarks: paymentRemarks.value,
    fund_account_id: selectedFundAccountId.value,
    fund_source_id: selectedFundAccountId.value,
    accounts_payable_name: vendorName.value,
  })
}
</script>
