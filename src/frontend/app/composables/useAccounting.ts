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
  account_item_id: number
  amount: number
  transaction_type: 'debit' | 'credit'
  description?: string
  user_id: number
  created_at: string
}

export const useAccounting = () => {
  const api = useApi()

  // State management with seed data matching backend schema
  const fundAccounts = useState<FundAccount[]>('accounting_fund_accounts', () => [
    {
      id: 1,
      company_id: 1,
      fund_code: 'FND-101',
      fund_name: 'General Operating Fund',
      description: 'Primary corporate liquidity and daily operational expenses',
      user_id: 1,
      ledger_account_id: 10,
      created_at: '2026-01-10',
    },
    {
      id: 2,
      company_id: 1,
      fund_code: 'FND-202',
      fund_name: 'Capital Expenditure & R&D Fund',
      description: 'Reserved capital for software automation & infrastructure expansion',
      user_id: 1,
      ledger_account_id: 20,
      created_at: '2026-02-01',
    },
    {
      id: 3,
      company_id: 1,
      fund_code: 'FND-303',
      fund_name: 'Payroll Reserve Fund',
      description: 'Dedicated fund for bi-weekly employee compensation & tax withholding',
      user_id: 1,
      ledger_account_id: 30,
      created_at: '2026-02-15',
    },
  ])

  const ledgerAccounts = useState<LedgerAccount[]>('accounting_ledger_accounts', () => [
    {
      id: 10,
      account_code: '1010-CASH',
      account_name: 'Cash & Cash Equivalents',
      description: 'Operating bank deposits and liquid treasury holdings',
      fund_account_id: 1,
      user_id: 1,
      ledger_account_id: 1,
      created_at: '2026-01-10',
    },
    {
      id: 11,
      account_code: '1020-AR',
      account_name: 'Accounts Receivable',
      description: 'Invoiced customer subscriptions & enterprise contracts',
      fund_account_id: 1,
      user_id: 1,
      ledger_account_id: 1,
      created_at: '2026-01-12',
    },
    {
      id: 20,
      account_code: '1500-EQUIP',
      account_name: 'Server & IT Infrastructure Assets',
      description: 'Hardware, high-performance compute nodes & software licenses',
      fund_account_id: 2,
      user_id: 1,
      ledger_account_id: 2,
      created_at: '2026-02-02',
    },
    {
      id: 30,
      account_code: '5010-SALARY',
      account_name: 'Salaries & Staff Expenses',
      description: 'Direct compensation, payroll tax & health benefits',
      fund_account_id: 3,
      user_id: 1,
      ledger_account_id: 3,
      created_at: '2026-02-16',
    },
    {
      id: 40,
      account_code: '4010-REV',
      account_name: 'SaaS Subscription Revenue',
      description: 'Recurring platform API usage revenue',
      fund_account_id: 1,
      user_id: 1,
      ledger_account_id: 1,
      created_at: '2026-01-15',
    },
  ])

  const accountItems = useState<AccountItem[]>('accounting_account_items', () => [
    { id: 1, item_code: 'ITEM-ACC-01', item_name: 'Client Subscription Payment', description: 'Enterprise tier automated ledger subscription' },
    { id: 2, item_code: 'ITEM-EXP-02', item_name: 'Cloud Hosting Infrastructure', description: 'AWS/GCP GPU cluster monthly compute fee' },
    { id: 3, item_code: 'ITEM-PAY-03', item_name: 'Engineering Staff Payroll', description: 'Monthly engineering team salary disbursement' },
    { id: 4, item_code: 'ITEM-TAX-04', item_name: 'Corporate Tax Withholding', description: 'Quarterly state and federal tax remittance' },
    { id: 5, item_code: 'ITEM-LIC-05', item_name: 'Database Security License', description: 'Annual database encryption key management service' },
  ])

  const journalEntries = useState<LedgerAccountItem[]>('accounting_journal_entries', () => [
    {
      id: 1001,
      ledger_account_id: 10,
      fund_account_id: 1,
      account_item_id: 1,
      amount: 125000.00,
      transaction_type: 'debit',
      description: 'Enterprise license payment received from FinCorp Ltd',
      user_id: 1,
      created_at: '2026-07-20 09:30:00',
    },
    {
      id: 1002,
      ledger_account_id: 40,
      fund_account_id: 1,
      account_item_id: 1,
      amount: 125000.00,
      transaction_type: 'credit',
      description: 'Recognized SaaS revenue from FinCorp contract',
      user_id: 1,
      created_at: '2026-07-20 09:30:00',
    },
    {
      id: 1003,
      ledger_account_id: 20,
      fund_account_id: 2,
      account_item_id: 2,
      amount: 18500.50,
      transaction_type: 'debit',
      description: 'Purchased dedicated AI inference node servers',
      user_id: 1,
      created_at: '2026-07-21 14:15:00',
    },
    {
      id: 1004,
      ledger_account_id: 10,
      fund_account_id: 1,
      account_item_id: 2,
      amount: 18500.50,
      transaction_type: 'credit',
      description: 'Cash payment for cloud compute invoice #AWS-9921',
      user_id: 1,
      created_at: '2026-07-21 14:15:00',
    },
    {
      id: 1005,
      ledger_account_id: 30,
      fund_account_id: 3,
      account_item_id: 3,
      amount: 45000.00,
      transaction_type: 'debit',
      description: 'Mid-month payroll distribution for core dev team',
      user_id: 1,
      created_at: '2026-07-22 11:00:00',
    },
  ])

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
  const addFundAccount = (fund: Omit<FundAccount, 'id' | 'created_at'>) => {
    const newId = Math.max(...fundAccounts.value.map(f => f.id), 0) + 1
    const newFund: FundAccount = {
      ...fund,
      id: newId,
      created_at: new Date().toISOString().split('T')[0],
    }
    fundAccounts.value.push(newFund)
    return newFund
  }

  // Ledger Account CRUD
  const addLedgerAccount = (acc: Omit<LedgerAccount, 'id' | 'created_at'>) => {
    const newId = Math.max(...ledgerAccounts.value.map(a => a.id), 0) + 1
    const newAcc: LedgerAccount = {
      ...acc,
      id: newId,
      created_at: new Date().toISOString().split('T')[0],
    }
    ledgerAccounts.value.push(newAcc)
    return newAcc
  }

  // Account Item CRUD
  const addAccountItem = (item: Omit<AccountItem, 'id'>) => {
    const newId = Math.max(...accountItems.value.map(i => i.id), 0) + 1
    const newItem: AccountItem = { ...item, id: newId }
    accountItems.value.push(newItem)
    return newItem
  }

  // Journal Entry CRUD
  const addJournalEntry = (entry: Omit<LedgerAccountItem, 'id' | 'created_at'>) => {
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

  return {
    fundAccounts,
    ledgerAccounts,
    accountItems,
    journalEntries,
    totalDebits,
    totalCredits,
    netLedgerBalance,
    addFundAccount,
    addLedgerAccount,
    addAccountItem,
    addJournalEntry,
  }
}
