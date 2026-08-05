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
  status?: 'active' | 'inactive' | 'archived'
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
  status?: 'active' | 'inactive' | 'archived'
  ledger_account_id?: number
  transaction_type?: 'debit' | 'credit'
}

export interface LedgerAccountItem {
  id: number
  ledger_account_id: number
  fund_account_id?: number
  project_id?: number
  account_item_id: number
  amount: number
  transaction_type: 'debit' | 'credit'
  description?: string
  posting_date?: string
  status?: 'posted' | 'void' | 'reconciled'
  is_paid?: boolean
  user_id: number
  created_at: string
  project?: {
    id: number
    name?: string
    project_name?: string
  }
  items?: any[]
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

  const totalUnpaidBalance = computed(() => {
    return journalEntries.value
      .filter(e => e.transaction_type === 'debit' && e.is_paid === false)
      .reduce((sum, e) => sum + Number(e.amount), 0)
  })

  const getFundAccountUnpaidBalance = (fundAccountId: number): number => {
    return journalEntries.value
      .filter(e => e.fund_account_id === fundAccountId && e.transaction_type === 'debit' && e.is_paid === false)
      .reduce((sum, e) => sum + Number(e.amount), 0)
  }

  const getTotalUnpaidBalance = (fundAccountId?: number): number => {
    if (fundAccountId !== undefined && fundAccountId !== null) {
      return getFundAccountUnpaidBalance(fundAccountId)
    }
    return totalUnpaidBalance.value
  }

  const getFundAccountSpent = (fundAccountId: number): number => {
    return journalEntries.value
      .filter(e => e.fund_account_id === fundAccountId && e.transaction_type === 'debit' && e.is_paid !== false)
      .reduce((sum, e) => sum + Number(e.amount), 0)
  }

  const getFundAccountCredits = (fundAccountId: number): number => {
    return journalEntries.value
      .filter(e => e.fund_account_id === fundAccountId && e.transaction_type === 'credit')
      .reduce((sum, e) => sum + Number(e.amount), 0)
  }

  const getFundAccountRemaining = (fundAccountId: number): number => {
    const fund = fundAccounts.value.find(f => f.id === fundAccountId)
    if (!fund) return 0
    const initialAmount = Number(fund.amount) || 0
    return initialAmount + getFundAccountCredits(fundAccountId) - getFundAccountSpent(fundAccountId)
  }

  const totalFundAccountsBalance = computed(() => {
    return fundAccounts.value.reduce((sum, f) => sum + getFundAccountRemaining(f.id), 0)
  })


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

  const updateFundAccount = async (id: number, fund: Partial<FundAccount>) => {
    try {
      const res = await api.request<{ data: FundAccount }>(`/fund-accounts/${id}`, {
        method: 'PUT',
        body: fund,
      })
      const updated = res.data || res
      const idx = fundAccounts.value.findIndex(f => f.id === id)
      if (idx !== -1) {
        fundAccounts.value[idx] = { ...fundAccounts.value[idx], ...updated, ...fund }
      }
      return fundAccounts.value[idx]
    } catch (e) {
      const idx = fundAccounts.value.findIndex(f => f.id === id)
      if (idx !== -1) {
        fundAccounts.value[idx] = { ...fundAccounts.value[idx], ...fund }
      }
      return fundAccounts.value[idx]
    }
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

  // Status Update Methods
  const updateLedgerAccountStatus = async (id: number, status: 'active' | 'inactive' | 'archived') => {
    const res = await api.request<{ data: LedgerAccount }>(`/ledger-accounts/${id}/status`, {
      method: 'PATCH',
      body: { status },
    })
    const updated = res.data || res
    const idx = ledgerAccounts.value.findIndex(a => a.id === id)
    if (idx !== -1) ledgerAccounts.value[idx] = { ...ledgerAccounts.value[idx], status }
    return updated
  }

  const updateAccountItemStatus = async (id: number, status: 'active' | 'inactive' | 'archived') => {
    const res = await api.request<{ data: AccountItem }>(`/account-items/${id}/status`, {
      method: 'PATCH',
      body: { status },
    })
    const updated = res.data || res
    const idx = accountItems.value.findIndex(a => a.id === id)
    if (idx !== -1) accountItems.value[idx] = { ...accountItems.value[idx], status }
    return updated
  }

  const updateJournalEntryPaymentStatus = async (id: number, is_paid: boolean) => {
    const res = await api.request<{ data: LedgerAccountItem }>(`/journal-entries/${id}/is-paid`, {
      method: 'PATCH',
      body: { is_paid },
    })
    const updated = res.data || res
    const idx = journalEntries.value.findIndex(e => e.id === id)
    if (idx !== -1) journalEntries.value[idx] = { ...journalEntries.value[idx], is_paid }
    return updated
  }

  return {
    fundAccounts,
    ledgerAccounts,
    accountItems,
    journalEntries,
    totalDebits,
    totalCredits,
    netLedgerBalance,
    totalUnpaidBalance,
    getFundAccountUnpaidBalance,
    getTotalUnpaidBalance,
    fetchFundAccounts,
    fetchLedgerAccounts,
    fetchAccountItems,
    fetchJournalEntries,
    addFundAccount,
    updateFundAccount,
    addLedgerAccount,
    addAccountItem,
    addJournalEntry,
    getFundAccountSpent,
    getFundAccountCredits,
    getFundAccountRemaining,
    totalFundAccountsBalance,
    updateLedgerAccountStatus,
    updateAccountItemStatus,
    updateJournalEntryPaymentStatus,
  }
}
