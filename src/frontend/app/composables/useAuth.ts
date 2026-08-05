export interface Role {
  id?: number
  name: string
  guard_name?: string
}

export interface User {
  id: number
  email: string
  name?: string
  person_id?: number
  person_affiliations_id?: number
  person?: Person
  person_affiliation?: PersonAffiliation
  roles?: (Role | string)[]
}

export interface Person {
  id?: number
  first_name: string
  last_name: string
  middle_name?: string
  name_extension?: string
  birth_date?: string
  birth_place?: string
  civil_status: 'Single' | 'Married' | 'Widowed' | 'Separated'
  gender?: 'Male' | 'Female'
  citizenship_id?: number
}

export interface Company {
  id?: number
  business_name: string
  business_description: string
  city_id: number
  business_classification?: string
  business_scope?: 'National' | 'Regional' | 'City/Municipality' | 'Barangay'
  street?: string
  building_number?: string
  barangay?: string
  zip?: string
  date_started?: string
  date_ended?: string
  is_government: boolean
}

export interface Position {
  id?: number
  title: string
  industry: string
  salary_grade?: number
}

export interface PersonAffiliation {
  id?: number
  person_id?: number
  company_id?: number
  affiliation_level: string
  employment_status: string
  employee_id: string
  position_id?: number
  is_head: boolean
  company?: Company
  position?: Position
}

export const useAuth = () => {
  const api = useApi()
  
  const currentUser = useState<User | null>('auth_user', () => null)
  
  const currentPerson = useState<Person>('auth_person', () => ({
    first_name: '',
    last_name: '',
    middle_name: '',
    civil_status: 'Single',
  }))

  const currentCompany = useState<Company>('auth_company', () => ({
    business_name: '',
    business_description: '',
    city_id: 1,
    is_government: false,
  }))

  const currentPosition = useState<Position>('auth_position', () => ({
    title: '',
    industry: 'Finance',
  }))

  const currentAffiliation = useState<PersonAffiliation>('auth_affiliation', () => ({
    affiliation_level: 'Rank and File',
    employment_status: 'Regular',
    employee_id: '',
    is_head: false,
  }))

  const isAuthenticated = computed(() => !!currentUser.value)

  const isSuperAdmin = computed(() => {
    if (!currentUser.value) return false
    if (Array.isArray(currentUser.value.roles)) {
      return currentUser.value.roles.some((r: any) =>
        typeof r === 'string' ? r === 'super_admin' : r?.name === 'super_admin'
      )
    }
    return false
  })

  const syncUserData = (user: User) => {
    currentUser.value = user
    if (user.person) {
      currentPerson.value = user.person
    }
    if (user.person_affiliation) {
      currentAffiliation.value = user.person_affiliation
      if (user.person_affiliation.company) {
        currentCompany.value = user.person_affiliation.company
      }
      if (user.person_affiliation.position) {
        currentPosition.value = user.person_affiliation.position
      }
    }
  }

  const login = async (email: string, password: string) => {
    const res = await api.request<{ token: string; user: User }>('/auth/login', {
      method: 'POST',
      body: { email, password },
    })
    api.token.value = res.token
    syncUserData(res.user)
    return res
  }

  const registerUser = async (data: {
    user: { email: string; password: string }
    person: Person
    company: Company
    position: Position
    affiliation: PersonAffiliation
  }) => {
    const res = await api.request<{ token: string; user: User; data?: any }>('/auth/register', {
      method: 'POST',
      body: data,
    })
    api.token.value = res.token
    syncUserData(res.user)
    return res
  }

  const fetchUser = async () => {
    try {
      const res = await api.request<{ user: User }>('/auth/user')
      if (res.user) {
        syncUserData(res.user)
      }
    } catch (e) {
      currentUser.value = null
      throw e
    }
  }

  const updateProfile = async (profileData: Partial<Person>) => {
    const res = await api.request<{ person: Person }>('/settings/profile', {
      method: 'PUT',
      body: profileData,
    })
    currentPerson.value = res.person
    return res
  }

  const updateCompany = async (companyData: Partial<Company>) => {
    const res = await api.request<{ company: Company }>('/settings/company', {
      method: 'PUT',
      body: companyData,
    })
    currentCompany.value = res.company
    return res
  }

  const logout = () => {
    api.token.value = null
    currentUser.value = null
    navigateTo('/login')
  }

  return {
    currentUser,
    currentPerson,
    currentCompany,
    currentPosition,
    currentAffiliation,
    isAuthenticated,
    isSuperAdmin,
    login,
    registerUser,
    fetchUser,
    updateProfile,
    updateCompany,
    logout,
  }
}
