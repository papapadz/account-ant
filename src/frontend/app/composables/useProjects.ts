export interface ProjectAddress {
  street: string
  city: string
  zip_code: string
}

export interface Project {
  id: number
  name: string
  description?: string
  budget?: number
  address?: ProjectAddress | string
  client_name: string
  start_date: string
  status: 'active' | 'on-hold' | 'completed'
  created_at: string
}

export interface FundSource {
  id: number
  project_id: number
  name: string
  amount: number // Total funds allocated/received
  date_received: string
  created_at: string
}

export interface ExpenseCategory {
  id: number
  name: string
  code?: string
  status: 'active' | 'archived'
  created_at: string
}

export interface Transaction {
  id: number
  project_id: number
  fund_source_id: number
  category_id: number
  type: 'debit' | 'credit' // debit = spent/expense, credit = added/refund
  amount: number
  date: string
  note?: string
  created_at: string
}

export const useProjects = () => {
  const api = useApi()

  // State management initialized to empty arrays
  const projects = useState<Project[]>('construction_projects', () => [])
  const fundSources = useState<FundSource[]>('construction_fund_sources', () => [])
  const categories = useState<ExpenseCategory[]>('construction_categories', () => [])
  const transactions = useState<Transaction[]>('construction_transactions', () => [])

  const mapProjectFromBackend = (p: any): Project => {
    return {
      id: p.id,
      name: p.name,
      description: p.description,
      budget: Number(p.budget),
      address: {
        street: p.street || '',
        city: p.barangay || '',
        zip_code: p.zip || '',
      },
      client_name: p.client_name,
      start_date: p.start_date,
      status: p.status || 'active',
      created_at: p.created_at,
    }
  }

  // Fetch actions
  const fetchProjects = async () => {
    const res = await api.request<any>('/projects')
    const rawProjects = res.data || res

    projects.value = rawProjects.map(mapProjectFromBackend)

    // Extract project funds & journal entries (transactions) from response
    const mappedFunds: FundSource[] = []
    const mappedTxs: Transaction[] = []

    for (const rp of rawProjects) {
      if (rp.project_funds) {
        for (const pf of rp.project_funds) {
          mappedFunds.push({
            id: pf.fund_account_id, // Map fund_account_id as the ID for consistency
            project_id: pf.project_id,
            name: pf.fund_account?.fund_name || 'Allocated Fund',
            amount: Number(pf.initial_amount),
            date_received: pf.created_at?.split('T')[0] || new Date().toISOString().split('T')[0],
            created_at: pf.created_at,
          })
        }
      }

      if (rp.journal_entries) {
        for (const je of rp.journal_entries) {
          mappedTxs.push({
            id: je.id,
            project_id: je.project_id,
            fund_source_id: je.fund_account_id,
            category_id: je.account_item_id,
            type: je.transaction_type,
            amount: Number(je.amount),
            date: je.created_at?.split(' ')[0] || je.created_at || new Date().toISOString().split('T')[0],
            note: je.description,
            created_at: je.created_at,
          })
        }
      }
    }

    fundSources.value = mappedFunds
    transactions.value = mappedTxs

    // Fetch account items and map to categories
    const accountingStore = useAccounting()
    if (accountingStore.accountItems.value.length === 0) {
      await accountingStore.fetchAccountItems()
    }
    categories.value = accountingStore.accountItems.value.map(item => ({
      id: item.id,
      name: item.item_name,
      code: item.item_code,
      status: 'active',
      created_at: new Date().toISOString(),
    }))
  }

  // --- Financial Calculations ---

  // Project Level Aggregations
  const getProjectTotalFunds = (projectId: number): number => {
    return fundSources.value
      .filter(f => f.project_id === projectId)
      .reduce((sum, f) => sum + Number(f.amount), 0)
  }

  const getProjectTotalSpent = (projectId: number): number => {
    return transactions.value
      .filter(t => t.project_id === projectId && t.type === 'debit')
      .reduce((sum, t) => sum + Number(t.amount), 0)
  }

  const getProjectTotalDebits = (projectId: number): number => {
    return getProjectTotalSpent(projectId)
  }

  const getProjectTotalCredits = (projectId: number): number => {
    return transactions.value
      .filter(t => t.project_id === projectId && t.type === 'credit')
      .reduce((sum, t) => sum + Number(t.amount), 0)
  }

  const getProjectNetLedgerBalance = (projectId: number): number => {
    const totalFunds = getProjectTotalFunds(projectId)
    const totalCredits = getProjectTotalCredits(projectId)
    const totalDebits = getProjectTotalDebits(projectId)
    return (totalFunds + totalCredits) - totalDebits
  }

  const getProjectActiveFundBalance = (projectId: number): number => {
    return getProjectTotalFunds(projectId) - getProjectTotalSpent(projectId)
  }

  const getProjectRemainingBalance = (projectId: number): number => {
    return getProjectActiveFundBalance(projectId)
  }

  // Fund Source Level Aggregations
  const getFundSourceSpent = (fundSourceId: number): number => {
    return transactions.value
      .filter(t => t.fund_source_id === fundSourceId && t.type === 'debit')
      .reduce((sum, t) => sum + Number(t.amount), 0)
  }

  const getFundSourceRemaining = (fundSourceId: number): number => {
    const fund = fundSources.value.find(f => f.id === fundSourceId)
    if (!fund) return 0
    return Number(fund.amount) - getFundSourceSpent(fundSourceId)
  }

  const getFundSourceUsagePercentage = (fundSourceId: number): number => {
    const fund = fundSources.value.find(f => f.id === fundSourceId)
    if (!fund || fund.amount <= 0) return 0
    return Math.min(Math.round((getFundSourceSpent(fundSourceId) / fund.amount) * 100), 100)
  }

  // Overall App Metrics Across All Projects
  const totalAppManagedFunds = computed(() => {
    return fundSources.value.reduce((sum, f) => sum + Number(f.amount), 0)
  })

  const totalAppSpent = computed(() => {
    return transactions.value
      .filter(t => t.type === 'debit')
      .reduce((sum, t) => sum + Number(t.amount), 0)
  })

  const totalAppRemainingBalance = computed(() => {
    return totalAppManagedFunds.value - totalAppSpent.value
  })

  // --- Mutations ---

  const addProject = async (project: Omit<Project, 'id' | 'created_at'>) => {
    const addr = typeof project.address === 'object' ? project.address : { street: '', city: '', zip_code: '' }
    
    // Simple city mapping logic to link city string to seeded city IDs
    let matchedCityId = 1
    const cityLower = (addr.city || '').toLowerCase()
    if (cityLower.includes('taguig')) matchedCityId = 2
    else if (cityLower.includes('pasig')) matchedCityId = 3
    else if (cityLower.includes('cebu')) matchedCityId = 4
    else if (cityLower.includes('davao')) matchedCityId = 5

    const body = {
      name: project.name,
      description: project.description,
      budget: project.budget,
      start_date: project.start_date,
      client_name: project.client_name,
      is_government: false,
      city_id: matchedCityId,
      street: addr.street,
      barangay: addr.city || 'Barangay Central',
      zip: addr.zip_code || '1000',
    }

    const res = await api.request<any>('/projects', {
      method: 'POST',
      body,
    })
    const created = res.data || res
    const mapped = mapProjectFromBackend(created)
    projects.value.unshift(mapped)
    return mapped
  }

  const updateProject = async (id: number, data: Partial<Omit<Project, 'id'>>) => {
    await api.request(`/projects/${id}`, {
      method: 'PUT',
      body: data,
    })
    await fetchProjects()
  }

  const addFundSource = async (fund: { project_id: number; name: string; amount: number }) => {
    const accountingStore = useAccounting()
    if (accountingStore.fundAccounts.value.length === 0) {
      await accountingStore.fetchFundAccounts()
    }

    let fundAccountId = accountingStore.fundAccounts.value.find(
      f => f.fund_name.toLowerCase() === fund.name.toLowerCase()
    )?.id

    if (!fundAccountId) {
      // Dynamic fund creation
      const generatedCode = `FND-${Math.floor(100 + Math.random() * 900)}`
      const newFund = await api.request<any>('/fund-accounts', {
        method: 'POST',
        body: {
          fund_code: generatedCode,
          fund_name: fund.name,
          amount: fund.amount,
          description: `Automatically created fund source for project #${fund.project_id}`,
        }
      })
      const createdData = newFund.data || newFund
      fundAccountId = createdData.id
      await accountingStore.fetchFundAccounts()
    }

    await api.request(`/projects/${fund.project_id}/funds`, {
      method: 'POST',
      body: {
        fund_account_id: fundAccountId,
        initial_amount: fund.amount,
      }
    })

    await fetchProjects()
  }

  const addTransaction = async (tx: {
    project_id: number
    fund_source_id: number
    category_id: number
    type: 'debit' | 'credit'
    amount: number
    date: string
    note: string
  }) => {
    const accountingStore = useAccounting()
    if (accountingStore.accountItems.value.length === 0) {
      await accountingStore.fetchAccountItems()
    }
    const accountItem = accountingStore.accountItems.value.find(i => i.id === tx.category_id)
    const ledgerAccountId = accountItem?.ledger_account_id || 10

    await api.request('/journal-entries', {
      method: 'POST',
      body: {
        ledger_account_id: ledgerAccountId,
        fund_account_id: tx.fund_source_id,
        project_id: tx.project_id,
        account_item_id: tx.category_id,
        amount: tx.amount,
        transaction_type: tx.type,
        description: tx.note,
      }
    })

    await fetchProjects()
    await accountingStore.fetchJournalEntries()
  }

  // Frontend helper stubs (no-ops for backend mode)
  const addCategory = () => {}
  const updateCategory = () => {}
  const toggleArchiveCategory = () => {}

  return {
    projects,
    fundSources,
    categories,
    transactions,
    getProjectTotalFunds,
    getProjectTotalSpent,
    getProjectTotalDebits,
    getProjectTotalCredits,
    getProjectNetLedgerBalance,
    getProjectActiveFundBalance,
    getProjectRemainingBalance,
    getFundSourceSpent,
    getFundSourceRemaining,
    getFundSourceUsagePercentage,
    totalAppManagedFunds,
    totalAppSpent,
    totalAppRemainingBalance,
    fetchProjects,
    addProject,
    updateProject,
    addFundSource,
    addTransaction,
    addCategory,
    updateCategory,
    toggleArchiveCategory,
  }
}
