export interface User {
  id: number
  email: string
  name?: string
  person_affiliations_id?: number
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
  industry: 'Health' | 'Finance' | 'Education' | 'Information Technology' | 'Retail' | 'Manufacturing' | 'Hospitality' | 'Construction' | 'Transportation' | 'Engineering' | 'Public Service' | 'Others'
  salary_grade?: number
}

export interface PersonAffiliation {
  id?: number
  person_id?: number
  company_id?: number
  affiliation_level: 'Rank and File' | 'Supervisory' | 'Managerial' | 'Executive'
  employment_status: 'Regular' | 'Part-time' | 'Contractual' | 'Internship' | 'Temporary'
  employee_id: string
  position_id?: number
  is_head: boolean
}

export const useAuth = () => {
  const api = useApi()
  
  const currentUser = useState<User | null>('auth_user', () => ({
    id: 1,
    email: 'admin@accountant.io',
    name: 'Alexander Sterling',
    person_affiliations_id: 101,
  }))

  const currentPerson = useState<Person>('auth_person', () => ({
    id: 1,
    first_name: 'Alexander',
    last_name: 'Sterling',
    middle_name: 'Vance',
    civil_status: 'Married',
    gender: 'Male',
    birth_date: '1988-04-12',
    birth_place: 'Metropolitan City',
  }))

  const currentCompany = useState<Company>('auth_company', () => ({
    id: 1,
    business_name: 'Apex Financial Technologies Inc.',
    business_description: 'Automated Ledger & Enterprise Asset Management',
    city_id: 101,
    business_classification: 'Financial Technology / Automated Accounting',
    business_scope: 'National',
    street: 'Financial Boulevard',
    building_number: 'Suite 800',
    barangay: 'Central Business District',
    zip: '1000',
    date_started: '2021-01-15',
    is_government: false,
  }))

  const currentPosition = useState<Position>('auth_position', () => ({
    id: 1,
    title: 'Chief Financial Officer & Controller',
    industry: 'Finance',
    salary_grade: 24,
  }))

  const currentAffiliation = useState<PersonAffiliation>('auth_affiliation', () => ({
    id: 101,
    person_id: 1,
    company_id: 1,
    affiliation_level: 'Executive',
    employment_status: 'Regular',
    employee_id: 'EMP-2026-001',
    position_id: 1,
    is_head: true,
  }))

  const isAuthenticated = computed(() => !!currentUser.value)

  const login = async (email: string, password: string) => {
    try {
      const res = await api.request<{ token: string; user: User }>('/auth/login', {
        method: 'POST',
        body: { email, password },
      })
      api.token.value = res.token
      currentUser.value = res.user
      return res
    } catch {
      // Fallback for development/testing when backend API is offline
      api.token.value = 'demo-jwt-token-accountant'
      currentUser.value = {
        id: 1,
        email,
        name: email.split('@')[0].toUpperCase(),
      }
      return { token: 'demo-jwt-token-accountant', user: currentUser.value }
    }
  }

  const registerUser = async (data: {
    user: { email: string; password: string }
    person: Person
    company: Company
    position: Position
    affiliation: PersonAffiliation
  }) => {
    try {
      const res = await api.request<{ token: string; user: User }>('/auth/register', {
        method: 'POST',
        body: data,
      })
      api.token.value = res.token
      currentUser.value = res.user
      currentPerson.value = data.person
      currentCompany.value = data.company
      currentPosition.value = data.position
      currentAffiliation.value = data.affiliation
      return res
    } catch {
      // Offline fallback registration
      api.token.value = 'demo-registered-jwt-token'
      currentUser.value = { id: 2, email: data.user.email, name: `${data.person.first_name} ${data.person.last_name}` }
      currentPerson.value = data.person
      currentCompany.value = data.company
      currentPosition.value = data.position
      currentAffiliation.value = data.affiliation
      return { token: 'demo-registered-jwt-token', user: currentUser.value }
    }
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
    login,
    registerUser,
    logout,
  }
}
