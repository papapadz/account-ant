export interface CityAddress {
  id: number
  name: string
  province?: string
}

export interface ProjectFundAllocation {
  id: number
  project_id: number
  fund_account_id: number
  initial_amount: number
  created_at?: string
  fund_account?: {
    id: number
    fund_code: string
    fund_name: string
  }
}

export interface Project {
  id: number
  name: string
  description?: string
  budget: number
  start_date: string
  end_date?: string
  client_name: string
  is_government: boolean
  city_id?: number
  city_name?: string
  house_number?: string
  street?: string
  village?: string
  barangay: string
  zip: string
  created_at?: string
  project_funds?: ProjectFundAllocation[]
}

export const useProjects = () => {
  const api = useApi()
  const accounting = useAccounting()

  // State management
  const cities = useState<CityAddress[]>('project_cities', () => [])
  const projectFunds = useState<ProjectFundAllocation[]>('project_fund_allocations', () => [])
  const projects = useState<Project[]>('projects_list', () => [])
  const isLoading = useState<boolean>('projects_loading', () => false)

  // Fetch Cities
  const fetchCities = async () => {
    try {
      const res = await api.request<{ data: CityAddress[] }>('/cities')
      if (res && Array.isArray(res.data) && res.data.length > 0) {
        cities.value = res.data
      }
    } catch (e) {
      console.warn('[useProjects] Failed to fetch cities, using fallback.')
    }
  }

  // Fetch Projects & Allocations
  const fetchProjects = async () => {
    try {
      const res = await api.request<{ data: any[] }>('/projects')
      if (res && Array.isArray(res.data) && res.data.length > 0) {
        projects.value = res.data.map(p => ({
          ...p,
          budget: Number(p.budget),
          city_name: p.city ? p.city.name : 'Unknown City',
          is_government: Boolean(p.is_government),
        }))

        // Extract fund allocations
        const allFunds: ProjectFundAllocation[] = []
        res.data.forEach(p => {
          if (p.project_funds && Array.isArray(p.project_funds)) {
            p.project_funds.forEach((pf: any) => {
              allFunds.push({
                id: pf.id,
                project_id: pf.project_id,
                fund_account_id: pf.fund_account_id,
                initial_amount: Number(pf.initial_amount),
                created_at: pf.created_at,
                fund_account: pf.fund_account,
              })
            })
          }
        })
        if (allFunds.length > 0) {
          projectFunds.value = allFunds
        }
      }
    } catch (e) {
      console.warn('[useProjects] Failed to fetch projects, using existing state.')
    }
  }

  const fetchAll = async () => {
    isLoading.value = true
    await Promise.allSettled([
      accounting.fetchAll(),
      fetchCities(),
      fetchProjects(),
    ])
    isLoading.value = false
  }

  // Project Creation Method
  const createProject = async (data: Omit<Project, 'id' | 'created_at'>) => {
    try {
      const res = await api.request<{ data: Project }>('/projects', {
        method: 'POST',
        body: data,
      })
      await fetchProjects()
      return res.data || res
    } catch (e) {
      const newId = Math.max(...projects.value.map(p => p.id), 0) + 1
      const city = cities.value.find(c => c.id === data.city_id)
      const newProject: Project = {
        ...data,
        id: newId,
        city_name: city ? city.name : 'Unknown City',
        created_at: new Date().toISOString().split('T')[0],
      }
      projects.value.unshift(newProject)
      return newProject
    }
  }

  // Add Fund Source Allocation with Initial Amount to Project
  const addProjectFund = async (projectId: number, fundAccountId: number, initialAmount: number) => {
    try {
      const res = await api.request<{ data: ProjectFundAllocation }> (`/projects/${projectId}/funds`, {
        method: 'POST',
        body: {
          fund_account_id: fundAccountId,
          initial_amount: Number(initialAmount),
        },
      })
      await fetchProjects()
      return res.data || res
    } catch (e) {
      const existing = projectFunds.value.find(pf => pf.project_id === projectId && pf.fund_account_id === fundAccountId)
      if (existing) {
        existing.initial_amount = Number(initialAmount)
        return existing
      }

      const newId = Math.max(...projectFunds.value.map(pf => pf.id), 0) + 1
      const newAlloc: ProjectFundAllocation = {
        id: newId,
        project_id: projectId,
        fund_account_id: fundAccountId,
        initial_amount: Number(initialAmount),
        created_at: new Date().toISOString().split('T')[0],
      }
      projectFunds.value.push(newAlloc)
      return newAlloc
    }
  }

  // Calculate Project Metrics & Fund Breakdown
  const getProjectMetrics = (projectId: number) => {
    const project = projects.value.find(p => p.id === projectId)
    if (!project) return null

    const funds = projectFunds.value.filter(pf => pf.project_id === projectId)
    const totalAllocatedFunds = funds.reduce((sum, f) => sum + Number(f.initial_amount), 0)

    const entries = accounting.journalEntries.value.filter(e => Number(e.project_id) === Number(projectId))
    const totalDebits = entries.filter(e => e.transaction_type === 'debit').reduce((sum, e) => sum + Number(e.amount), 0)
    const totalCredits = entries.filter(e => e.transaction_type === 'credit').reduce((sum, e) => sum + Number(e.amount), 0)
    
    // Net expense posted to project
    const netExpenses = totalDebits - totalCredits
    const runningBalance = totalAllocatedFunds - netExpenses
    const budgetUtilizedPercentage = project.budget > 0 ? Math.round((netExpenses / project.budget) * 100) : 0
    const isOverBudget = netExpenses > project.budget

    // Fund level breakdown
    const fundBreakdown = funds.map(pf => {
      const fundObj = accounting.fundAccounts.value.find(fa => fa.id === pf.fund_account_id) || pf.fund_account
      const fundEntries = entries.filter(e => Number(e.fund_account_id) === Number(pf.fund_account_id))
      const fDebits = fundEntries.filter(e => e.transaction_type === 'debit').reduce((sum, e) => sum + Number(e.amount), 0)
      const fCredits = fundEntries.filter(e => e.transaction_type === 'credit').reduce((sum, e) => sum + Number(e.amount), 0)
      const fNetExpense = fDebits - fCredits
      const fRunningBalance = Number(pf.initial_amount) - fNetExpense

      return {
        fund_account_id: pf.fund_account_id,
        fund_code: fundObj ? (fundObj as any).fund_code : `FND-${pf.fund_account_id}`,
        fund_name: fundObj ? (fundObj as any).fund_name : `Fund #${pf.fund_account_id}`,
        initial_amount: Number(pf.initial_amount),
        total_debits: fDebits,
        total_credits: fCredits,
        net_expense: fNetExpense,
        running_balance: fRunningBalance,
      }
    })

    return {
      project,
      funds,
      totalAllocatedFunds,
      totalDebits,
      totalCredits,
      netExpenses,
      runningBalance,
      budgetUtilizedPercentage,
      isOverBudget,
      fundBreakdown,
      journalEntries: entries,
    }
  }

  return {
    projects,
    projectFunds,
    cities,
    isLoading,
    fetchCities,
    fetchProjects,
    fetchAll,
    createProject,
    addProjectFund,
    getProjectMetrics,
  }
}
