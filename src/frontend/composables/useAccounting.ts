export interface FundAccount {
  id: number
  company_id: number
  fund_code: string
  fund_name: string
  description?: string
  user_id: number
  ledger_account_id?: number
  created_at?: string
}

export interface LedgerAccount {
  id: number
  account_code: string
  account_name: string
  description?: string
  fund_account_id: number
  user_id: number
  ledger_account_id?: number
  created_at?: string
}

export interface AccountItem {
  id: number
  item_code: string
  item_name: string
  description?: string
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
  user_id: number
  created_at: string
}

export const useAccounting = () => {
  const api = useApi()

  // State management with seed fallback
  const fundAccounts = useState<FundAccount[]>('accounting_fund_accounts', () => [])
  const ledgerAccounts = useState<LedgerAccount[]>('accounting_ledger_accounts', () => [])
  const accountItems = useState<AccountItem[]>('accounting_account_items', () => [])
  const journalEntries = useState<LedgerAccountItem[]>('accounting_journal_entries', () => [])
  const isLoading = useState<boolean>('accounting_loading', () => false)

  // Fetch Methods
  const fetchFundAccounts = async () => {
    try {
      const data = await api.request<FundAccount[]>('/fund-accounts')
      if (Array.isArray(data) && data.length > 0) {
        fundAccounts.value = data
      }
    } catch (e) {
      console.warn('[useAccounting] Failed to fetch fund accounts, using existing state.')
    }
  }

  const fetchLedgerAccounts = async () => {
    try {
      const data = await api.request<LedgerAccount[]>('/ledger-accounts')
      if (Array.isArray(data) && data.length > 0) {
        ledgerAccounts.value = data
      }
    } catch (e) {
      console.warn('[useAccounting] Failed to fetch ledger accounts, using existing state.')
    }
  }

  const fetchAccountItems = async () => {
    try {
      const data = await api.request<AccountItem[]>('/account-items')
      if (Array.isArray(data) && data.length > 0) {
        accountItems.value = data
      }
    } catch (e) {
      console.warn('[useAccounting] Failed to fetch account items, using existing state.')
    }
  }

  const fetchJournalEntries = async () => {
    try {
      const data = await api.request<LedgerAccountItem[]>('/journal-entries')
      if (Array.isArray(data) && data.length > 0) {
        journalEntries.value = data
      }
    } catch (e) {
      console.warn('[useAccounting] Failed to fetch journal entries, using existing state.')
    }
  }

  const fetchAll = async () => {
    isLoading.value = true
    await Promise.allSettled([
      fetchFundAccounts(),
      fetchLedgerAccounts(),
      fetchAccountItems(),
      fetchJournalEntries(),
    ])
    isLoading.value = false
  }

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

  // Fund Account CRUD
  const addFundAccount = async (fund: Omit<FundAccount, 'id' | 'created_at'>) => {
    try {
      const response = await api.request<{ data: FundAccount }>('/fund-accounts', {
        method: 'POST',
        body: fund,
      })
      const created = response.data || response
      await fetchFundAccounts()
      return created
    } catch (e) {
      // Offline / Fallback handling
      const newId = Math.max(...fundAccounts.value.map(f => f.id), 0) + 1
      const newFund: FundAccount = {
        ...fund,
        id: newId,
        created_at: new Date().toISOString().split('T')[0],
      }
      fundAccounts.value.push(newFund)
      return newFund
    }
  }

  // Ledger Account CRUD
  const addLedgerAccount = async (acc: Omit<LedgerAccount, 'id' | 'created_at'>) => {
    try {
      const response = await api.request<{ data: LedgerAccount }>('/ledger-accounts', {
        method: 'POST',
        body: acc,
      })
      const created = response.data || response
      await fetchLedgerAccounts()
      return created
    } catch (e) {
      const newId = Math.max(...ledgerAccounts.value.map(a => a.id), 0) + 1
      const newAcc: LedgerAccount = {
        ...acc,
        id: newId,
        created_at: new Date().toISOString().split('T')[0],
      }
      ledgerAccounts.value.push(newAcc)
      return newAcc
    }
  }

  // Account Item CRUD
  const addAccountItem = async (item: Omit<AccountItem, 'id'>) => {
    try {
      const response = await api.request<{ data: AccountItem }>('/account-items', {
        method: 'POST',
        body: item,
      })
      const created = response.data || response
      await fetchAccountItems()
      return created
    } catch (e) {
      const newId = Math.max(...accountItems.value.map(i => i.id), 0) + 1
      const newItem: AccountItem = { ...item, id: newId }
      accountItems.value.push(newItem)
      return newItem
    }
  }

  // Journal Entry CRUD
  const addJournalEntry = async (entry: Omit<LedgerAccountItem, 'id' | 'created_at'>) => {
    try {
      const response = await api.request<{ data: LedgerAccountItem }>('/journal-entries', {
        method: 'POST',
        body: entry,
      })
      const created = response.data || response
      await fetchJournalEntries()
      return created
    } catch (e) {
      const newId = Math.max(...journalEntries.value.map(e => e.id), 0) + 1
      const now = new Date()
      const formattedDate = `${now.toISOString().split('T')[0]} ${now.toTimeString().split(' ')[0]}`
      const newEntry: LedgerAccountItem = {
        ...entry,
        id: newId,
        created_at: formattedDate,
      }
      journalEntries.value.unshift(newEntry)
      return newEntry
    }
  }

  return {
    fundAccounts,
    ledgerAccounts,
    accountItems,
    journalEntries,
    isLoading,
    totalDebits,
    totalCredits,
    netLedgerBalance,
    fetchFundAccounts,
    fetchLedgerAccounts,
    fetchAccountItems,
    fetchJournalEntries,
    fetchAll,
    addFundAccount,
    addLedgerAccount,
    addAccountItem,
    addJournalEntry,
  }
}
