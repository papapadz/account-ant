export interface StateModel {
  id: number
  name: string
  country_code?: string
}

export interface CityModel {
  id: number
  name: string
  state_id: number
  state_code?: string
  state?: StateModel
}

export interface ProjectAddress {
  street: string
  city: string
  zip_code: string
  state_id?: number
  city_id?: number
  house_number?: string
  village?: string
  barangay?: string
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
  city_id?: number
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
  ledger_account_id?: number
  transaction_type?: 'debit' | 'credit'
  created_at: string
}

export interface TransactionItem {
  description: string
  quantity: number
  unit: string
  price: number
  subtotal: number
}

export interface Transaction {
  id: number
  project_id: number
  fund_source_id: number
  category_id: number
  type: 'debit' | 'credit' // debit = spent/expense, credit = added/refund
  amount: number
  date: string
  posting_date?: string
  status?: 'posted' | 'void' | 'reconciled'
  note?: string
  items?: TransactionItem[]
  created_at: string
}

export const useProjects = () => {
  const api = useApi()

  // State management initialized to empty arrays
  const projects = useState<Project[]>('construction_projects', () => [])
  const fundSources = useState<FundSource[]>('construction_fund_sources', () => [])
  const categories = useState<ExpenseCategory[]>('construction_categories', () => [])
  const transactions = useState<Transaction[]>('construction_transactions', () => [])
  const cities = useState<CityModel[]>('address_cities', () => [])
  const states = useState<StateModel[]>('address_states', () => [])

  const fetchCities = async () => {
    try {
      const res = await api.request<any>('/cities')
      const rawCities = res.data || res || []
      cities.value = rawCities.map((c: any) => ({
        id: c.id,
        name: c.name,
        state_id: c.state_id,
        state_code: c.state_code,
        state: c.state ? { id: c.state.id, name: c.state.name, country_code: c.state.country_code } : undefined
      }))

      const stateMap = new Map<number, StateModel>()
      for (const c of rawCities) {
        if (c.state && c.state.id) {
          stateMap.set(c.state.id, { id: c.state.id, name: c.state.name, country_code: c.state.country_code })
        } else if (c.state_id) {
          stateMap.set(c.state_id, { id: c.state_id, name: c.state_code || `State #${c.state_id}` })
        }
      }
      if (stateMap.size === 0) {
        stateMap.set(1, { id: 1, name: 'Metro Manila', country_code: 'PH' })
      }
      states.value = Array.from(stateMap.values())
    } catch (err) {
      console.error('Failed to fetch cities:', err)
      states.value = [{ id: 1, name: 'Metro Manila', country_code: 'PH' }]
      cities.value = [
        { id: 1, name: 'Quezon City', state_id: 1, state_code: 'MM' },
        { id: 2, name: 'Taguig City (BGC)', state_id: 1, state_code: 'MM' },
        { id: 3, name: 'Pasig City (Ortigas)', state_id: 1, state_code: 'MM' },
        { id: 4, name: 'Cebu City', state_id: 1, state_code: 'MM' },
        { id: 5, name: 'Davao City', state_id: 1, state_code: 'MM' },
      ]
    }
  }

  const mapProjectFromBackend = (p: any): Project => {
    const cityName = p.city?.name || p.barangay || 'City Center'
    return {
      id: p.id,
      name: p.name,
      description: p.description,
      budget: Number(p.budget),
      city_id: p.city_id,
      address: {
        street: p.street || '',
        city: cityName,
        zip_code: p.zip || '',
        state_id: p.city?.state_id || 1,
        city_id: p.city_id,
        barangay: p.barangay || '',
        house_number: p.house_number || '',
        village: p.village || '',
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
            date_received: pf.date_received || pf.created_at?.split('T')[0] || new Date().toISOString().split('T')[0],
            created_at: pf.created_at,
          })
        }
      }

      if (rp.journal_entries) {
        for (const je of rp.journal_entries) {
          const rawItems = je.items || je.journal_entry_items || []
          const items: TransactionItem[] | undefined = rawItems.length > 0 ? rawItems.map((i: any) => ({
            description: i.description || i.item_name || 'Line Item',
            quantity: Number(i.quantity) || 1,
            unit: i.unit || 'pcs',
            price: Number(i.price ?? i.unit_price) || 0,
            subtotal: Number(i.subtotal ?? (Number(i.quantity || 1) * Number(i.price ?? i.unit_price ?? 0))),
          })) : undefined

          const postingDateVal = je.posting_date || je.created_at?.split(' ')[0] || je.created_at?.split('T')[0] || new Date().toISOString().split('T')[0]
          mappedTxs.push({
            id: je.id,
            project_id: je.project_id,
            fund_source_id: je.fund_account_id,
            category_id: je.account_item_id,
            type: je.transaction_type,
            amount: Number(je.amount),
            date: postingDateVal,
            posting_date: postingDateVal,
            status: je.status || 'posted',
            note: je.description,
            items,
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
      ledger_account_id: item.ledger_account_id,
      transaction_type: item.transaction_type || 'debit',
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
    const projectFunds = fundSources.value.filter(f => f.project_id === projectId)
    if (projectFunds.length === 0) {
      const totalFunds = getProjectTotalFunds(projectId)
      const totalCredits = getProjectTotalCredits(projectId)
      const totalDebits = getProjectTotalDebits(projectId)
      return (totalFunds + totalCredits) - totalDebits
    }
    return projectFunds.reduce((sum, f) => sum + getFundSourceRemaining(f.id), 0)
  }

  const getProjectRemainingBalance = (projectId: number): number => {
    return getProjectActiveFundBalance(projectId)
  }

  const getProjectBudget = (projectId: number): number => {
    const project = projects.value.find(p => p.id === projectId)
    return project?.budget || 0
  }

  const getProjectBudgetBalance = (projectId: number): number => {
    const project = projects.value.find(p => p.id === projectId)
    if (project?.budget)
      return project?.budget - getProjectTotalSpent(projectId) || 0
    else return 0
  }

  const getProjectBudgetUtilization = (projectId: number): number => {
    const project = projects.value.find(p => p.id === projectId)
    if (project?.budget)
      return Number(((project?.budget - getProjectBudgetBalance(projectId)) / project?.budget * 100).toFixed(2))
    else return 0
  }

  // Fund Source Level Aggregations
  const getFundSourceSpent = (fundSourceId: number): number => {
    return transactions.value
      .filter(t => t.fund_source_id === fundSourceId && t.type === 'debit')
      .reduce((sum, t) => sum + Number(t.amount), 0)
  }

  const getFundSourceCredits = (fundSourceId: number): number => {
    return transactions.value
      .filter(t => t.fund_source_id === fundSourceId && t.type === 'credit')
      .reduce((sum, t) => sum + Number(t.amount), 0)
  }

  const getFundSourceRemaining = (fundSourceId: number): number => {
    const fund = fundSources.value.find(f => f.id === fundSourceId)
    if (!fund) return 0
    return Number(fund.amount) + getFundSourceCredits(fundSourceId) - getFundSourceSpent(fundSourceId)
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

  const getFundProjectExpenses = (fundAccountId: number) => {
    // Aggregates expenses per project for the given fund
    if (!fundAccountId) return []
    const projectTxs = transactions.value.filter(t => t.fund_source_id === fundAccountId && t.type === 'debit')
    const totalSpent = projectTxs.reduce((sum, t) => sum + Number(t.amount), 0)

    const projectMap = new Map<number, number>()
    for (const tx of projectTxs) {
      const cur = projectMap.get(tx.project_id) || 0
      projectMap.set(tx.project_id, cur + Number(tx.amount))
    }

    return Array.from(projectMap.entries()).map(([pId, spent]) => {
      const proj = projects.value.find(p => p.id === pId)
      return {
        projectId: pId,
        projectName: proj ? proj.name : `Project #${pId}`,
        amount: spent,
        percentage: totalSpent > 0 ? Math.round((spent / totalSpent) * 100) : 0,
      }
    })
  }

  const getProjectMonthlyExpenses = (projectId: number) => {
    // Array of 12 months for selected project
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
    const monthlyData = months.map((month, idx) => ({ month, monthIndex: idx + 1, amount: 0 }))

    const projTxs = transactions.value.filter(t => t.project_id === projectId && t.type === 'debit')
    for (const tx of projTxs) {
      const txDateStr = tx.posting_date || tx.date
      if (!txDateStr) continue
      const dateObj = new Date(txDateStr)
      const mIdx = dateObj.getMonth()
      if (mIdx >= 0 && mIdx < 12) {
        monthlyData[mIdx].amount += Number(tx.amount)
      }
    }

    return monthlyData
  }

  // --- Mutations ---

  const addProject = async (project: Omit<Project, 'id' | 'created_at'> & { city_id?: number; state_id?: number }) => {
    const addr = typeof project.address === 'object' ? project.address : { street: '', city: '', zip_code: '' }

    let matchedCityId = project.city_id || addr.city_id || 1
    if (!project.city_id && !addr.city_id) {
      const cityLower = (addr.city || '').toLowerCase()
      if (cityLower.includes('taguig')) matchedCityId = 2
      else if (cityLower.includes('pasig')) matchedCityId = 3
      else if (cityLower.includes('cebu')) matchedCityId = 4
      else if (cityLower.includes('davao')) matchedCityId = 5
    }

    const body = {
      name: project.name,
      description: project.description,
      budget: project.budget,
      start_date: project.start_date,
      client_name: project.client_name,
      is_government: false,
      city_id: matchedCityId,
      house_number: addr.house_number || '',
      street: addr.street,
      barangay: addr.barangay || addr.city || 'Barangay Central',
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

  const addFundSource = async (fund: { project_id: number; name: string; amount: number; date_received?: string; date?: string }) => {
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
        date_received: fund.date_received || fund.date,
        date: fund.date_received || fund.date,
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
    items?: TransactionItem[]
  }) => {
    const accountingStore = useAccounting()
    if (accountingStore.accountItems.value.length === 0) {
      await accountingStore.fetchAccountItems()
    }
    const accountItem = accountingStore.accountItems.value.find(i => i.id === tx.category_id)
    const ledgerAccountId = accountItem?.ledger_account_id || 10

    try {
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
          posting_date: tx.date,
          date: tx.date,
          items: tx.items,
        }
      })
    } catch (err) {
      console.warn('[addTransaction] API call failed or running in offline mode, applying local fallback:', err)
    }

    // Push local transaction optimistically if not already present
    const txDateVal = tx.date || new Date().toISOString().split('T')[0]
    const newTx: Transaction = {
      id: Date.now(),
      project_id: tx.project_id,
      fund_source_id: tx.fund_source_id,
      category_id: tx.category_id,
      type: tx.type,
      amount: Number(tx.amount),
      date: txDateVal,
      posting_date: txDateVal,
      note: tx.note,
      items: tx.items ? [...tx.items] : undefined,
      created_at: new Date().toISOString(),
    }

    transactions.value.unshift(newTx)

    // Also sync with accounting store journal entries
    accountingStore.journalEntries.value.unshift({
      id: newTx.id,
      ledger_account_id: ledgerAccountId,
      fund_account_id: tx.fund_source_id,
      account_item_id: tx.category_id,
      amount: Number(tx.amount),
      transaction_type: tx.type,
      description: tx.note,
      user_id: 1,
      created_at: newTx.created_at,
    })

    try {
      await fetchProjects()
      await accountingStore.fetchJournalEntries()
      await accountingStore.fetchFundAccounts()
    } catch (e) {
      console.warn('[addTransaction] Background refresh warning:', e)
    }
  }

  // Frontend helper stubs (no-ops for backend mode)
  const addCategory = () => { }
  const updateCategory = () => { }
  const toggleArchiveCategory = () => { }

  return {
    projects,
    fundSources,
    categories,
    transactions,
    cities,
    states,
    fetchCities,
    getProjectTotalFunds,
    getProjectTotalSpent,
    getProjectTotalDebits,
    getProjectTotalCredits,
    getProjectNetLedgerBalance,
    getProjectActiveFundBalance,
    getProjectRemainingBalance,
    getFundSourceSpent,
    getFundSourceCredits,
    getFundSourceRemaining,
    getFundSourceUsagePercentage,
    getProjectBudget,
    getProjectBudgetBalance,
    getProjectBudgetUtilization,
    totalAppManagedFunds,
    totalAppSpent,
    totalAppRemainingBalance,
    getFundProjectExpenses,
    getProjectMonthlyExpenses,
    fetchProjects,
    addProject,
    updateProject,
    addFundSource,
    addTransaction,
    addCategory,
    updateCategory,
    toggleArchiveCategory,
    updateJournalEntryStatus,
    updateProjectStatus,
  }

  async function updateJournalEntryStatus(id: number, status: 'posted' | 'void' | 'reconciled') {
    const res = await api.request<any>(`/journal-entries/${id}/status`, {
      method: 'PATCH',
      body: { status },
    })
    // Optimistically update local transaction state
    const idx = transactions.value.findIndex(t => t.id === id)
    if (idx !== -1) transactions.value[idx] = { ...transactions.value[idx], status }
    return res.data || res
  }

  async function updateProjectStatus(id: number, status: 'active' | 'on-hold' | 'completed') {
    const res = await api.request<any>(`/projects/${id}/status`, {
      method: 'PATCH',
      body: { status },
    })
    const idx = projects.value.findIndex(p => p.id === id)
    if (idx !== -1) projects.value[idx] = { ...projects.value[idx], status }
    return res.data || res
  }
}

