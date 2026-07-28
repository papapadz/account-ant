export interface FundAccount {
  id: number
  company_id: number
  fund_code: string
  fund_name: string
  description?: string
  amount?: number
  user_id: number
  ledger_account_id?: number
  created_at?: string
}

export interface LedgerAccount {
  id: number
  account_code: string
  account_name: string
  description?: string
  fund_account_id?: number
  user_id: number
  ledger_account_id?: number
  created_at?: string
}

export interface AccountItem {
  id: number
  item_code: string
  item_name: string
  description?: string
  ledger_account_id?: number
}

export interface LedgerAccountItem {
  id: number
  ledger_account_id: number
  fund_account_id?: number
  account_item_id: number
  amount: number
  transaction_type: 'debit' | 'credit'
  description?: string
  user_id: number
  created_at: string
}

export const useAccounting = () => {
  const api = useApi()

  // State management initialized to empty arrays
  const fundAccounts = useState<FundAccount[]>('accounting_fund_accounts', () => [])
  const ledgerAccounts = useState<LedgerAccount[]>('accounting_ledger_accounts', () => [])
  const accountItems = useState<AccountItem[]>('accounting_account_items', () => [])
  const journalEntries = useState<LedgerAccountItem[]>('accounting_journal_entries', () => [])

  // Computed Financial Aggregations
  const totalDebits = computed(() => {
    return journalEntries.value
      .filter(e => e.transaction_type === 'debit')
      .reduce((sum, e) => sum + Number(e.amount), 0)
  })

  const totalCredits = computed(() => {
    return journalEntries.value
      .filter(e => e.transaction_type === 'credit')
      .reduce((sum, e) => sum + Number(e.amount), 0)
  })

  const netLedgerBalance = computed(() => totalDebits.value - totalCredits.value)

  // Fetch actions
  const fetchFundAccounts = async () => {
    fundAccounts.value = await api.request<FundAccount[]>('/fund-accounts')
  }

  const fetchLedgerAccounts = async () => {
    ledgerAccounts.value = await api.request<LedgerAccount[]>('/ledger-accounts')
  }

  const fetchAccountItems = async () => {
    accountItems.value = await api.request<AccountItem[]>('/account-items')
  }

  const fetchJournalEntries = async () => {
    journalEntries.value = await api.request<LedgerAccountItem[]>('/journal-entries')
  }

  // Fund Account CRUD
  const addFundAccount = async (fund: Omit<FundAccount, 'id' | 'created_at'>) => {
    const res = await api.request<{ data: FundAccount }>('/fund-accounts', {
      method: 'POST',
      body: fund,
    })
    const created = res.data || res
    fundAccounts.value.push(created)
    return created
  }

  // Ledger Account CRUD
  const addLedgerAccount = async (acc: Omit<LedgerAccount, 'id' | 'created_at'>) => {
    const res = await api.request<{ data: LedgerAccount }>('/ledger-accounts', {
      method: 'POST',
      body: acc,
    })
    const created = res.data || res
    ledgerAccounts.value.push(created)
    return created
  }

  // Account Item CRUD
  const addAccountItem = async (item: Omit<AccountItem, 'id'>) => {
    const res = await api.request<{ data: AccountItem }>('/account-items', {
      method: 'POST',
      body: item,
    })
    const created = res.data || res
    accountItems.value.push(created)
    return created
  }

  // Journal Entry CRUD
  const addJournalEntry = async (entry: Omit<LedgerAccountItem, 'id' | 'created_at'>) => {
    const res = await api.request<{ data: LedgerAccountItem }>('/journal-entries', {
      method: 'POST',
      body: entry,
    })
    const created = res.data || res
    journalEntries.value.unshift(created)
    return created
  }

  return {
    fundAccounts,
    ledgerAccounts,
    accountItems,
    journalEntries,
    totalDebits,
    totalCredits,
    netLedgerBalance,
    fetchFundAccounts,
    fetchLedgerAccounts,
    fetchAccountItems,
    fetchJournalEntries,
    addFundAccount,
    addLedgerAccount,
    addAccountItem,
    addJournalEntry,
  }
}
