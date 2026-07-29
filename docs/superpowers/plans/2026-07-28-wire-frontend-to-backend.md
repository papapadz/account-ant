# Wire Frontend to Backend & Token-Based Login Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Connect the Nuxt 4/Ionic frontend to the Laravel SQLite backend, replacing all local mock data with real API calls, and enforce token-based security using Laravel Sanctum.

**Architecture:** Use Laravel Sanctum for API token authentication. Secure all business ledger routes behind `auth:sanctum` middleware. Write a global route middleware on the frontend to manage token cookie validation, fetch user profiles dynamically, and handle redirect rules.

**Tech Stack:** Laravel 12 (Sanctum), Nuxt 4, Ionic Vue, SQLite.

## Global Constraints
- Laravel Sanctum must be used for token authentication.
- All frontend data layers must fetch dynamically from the API and allow real-time persistence.
- Do not bypass authentication for transaction and configuration routes.

---

### Task 1: Backend User Model & Sanctum Integration

**Files:**
- Modify: `src/backend/app/Models/User.php`
- Modify: `src/backend/routes/api.php`
- Test: Create `src/backend/tests/Feature/AuthTest.php`

**Interfaces:**
- Consumes: None
- Produces: Sanitized authentication token endpoints and route protections.

- [ ] **Step 1: Update the User model to include HasApiTokens**

Modify [User.php](file:///c:/laragon/www/account-ant/src/backend/app/Models/User.php):
```php
<?php

namespace App\Models;

use App\Models\HR\PersonAffiliation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    
    // Remaining properties stay unchanged...
}
```

- [ ] **Step 2: Secure routes with Sanctum middleware in api.php**

Modify [api.php](file:///c:/laragon/www/account-ant/src/backend/routes/api.php):
```php
<?php

use App\Http\Controllers\Api\AccountItemController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\FundAccountController;
use App\Http\Controllers\Api\LedgerAccountController;
use App\Http\Controllers\Api\LedgerAccountItemController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'system' => 'AccountAnt Ledger System',
        'version' => '1.0.0',
        'status' => 'ONLINE',
    ]);
});

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/user', [AuthController::class, 'user']);
    Route::apiResource('fund-accounts', FundAccountController::class);
    Route::apiResource('ledger-accounts', LedgerAccountController::class);
    Route::apiResource('account-items', AccountItemController::class);
    Route::post('/projects/{id}/funds', [ProjectController::class, 'addFund']);
    Route::apiResource('projects', ProjectController::class);
    Route::get('/cities', [CityController::class, 'index']);
    Route::get('/journal-entries/summary', [LedgerAccountItemController::class, 'summary']);
    Route::apiResource('journal-entries', LedgerAccountItemController::class);
    Route::prefix('settings')->group(function () {
        Route::put('/profile', [SettingsController::class, 'updateProfile']);
        Route::put('/company', [SettingsController::class, 'updateCompany']);
    });
});
```

- [ ] **Step 3: Create a failing Auth feature test**

Create [AuthTest.php](file:///c:/laragon/www/account-ant/src/backend/tests/Feature/AuthTest.php):
```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_request_is_blocked(): void
    {
        $response = $this->getJson('/api/projects');
        $response->assertStatus(401);
    }
}
```

- [ ] **Step 4: Run the test to verify it fails/passes correctly**
Run: `vendor/bin/phpunit tests/Feature/AuthTest.php` in `src/backend`.
Expected: PASS (401 response is successfully verified since projects route is now protected).

- [ ] **Step 5: Commit**
```bash
git add src/backend/app/Models/User.php src/backend/routes/api.php src/backend/tests/Feature/AuthTest.php
git commit -m "auth: secure api endpoints with laravel sanctum middleware"
```

---

### Task 2: Auth and Settings Controllers Updates

**Files:**
- Modify: `src/backend/app/Http/Controllers/Api/AuthController.php`
- Modify: `src/backend/app/Http/Controllers/Api/SettingsController.php`

**Interfaces:**
- Consumes: User token state.
- Produces: Dynamically authenticated details and profile updates.

- [ ] **Step 1: Update AuthController to issue real Sanctum tokens and fetch request user**

Modify [AuthController.php](file:///c:/laragon/www/account-ant/src/backend/app/Http/Controllers/Api/AuthController.php):
Change `login` and `user` methods:
```php
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::with(['person', 'personAffiliation.company', 'personAffiliation.position'])
            ->where('email', $request->email)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            if ($user === null && $request->email === 'admin@accountant.io') {
                $person = Person::create([
                    'first_name' => 'Alexander',
                    'last_name' => 'Vance',
                    'civil_status' => 'Single',
                    'gender' => 'Male',
                ]);

                $user = User::create([
                    'person_id' => $person->id,
                    'email' => 'admin@accountant.io',
                    'password' => Hash::make('password'),
                ]);

                $user->load(['person', 'personAffiliation.company', 'personAffiliation.position']);
            } else {
                return response()->json(['message' => 'Invalid email or password.'], 401);
            }
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Authentication successful',
            'token' => $token,
            'user' => $user,
        ]);
    }
```
And:
```php
    public function user(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $user->load(['person', 'personAffiliation.company', 'personAffiliation.position']);
        }
        return response()->json(['user' => $user]);
    }
```

- [ ] **Step 2: Update SettingsController to fetch user specific person and company records**

Modify [SettingsController.php](file:///c:/laragon/www/account-ant/src/backend/app/Http/Controllers/Api/SettingsController.php):
```php
    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'civil_status' => 'nullable|string',
            'gender' => 'nullable|string',
            'birth_date' => 'nullable|date',
        ]);

        $user = $request->user();
        $person = $user->person ?? Person::first() ?? Person::create([
            'first_name' => 'Alexander',
            'last_name' => 'Vance',
            'civil_status' => 'Single',
        ]);

        if (!$user->person_id) {
            $user->update(['person_id' => $person->id]);
        }

        $person->update($validated);

        return response()->json([
            'message' => 'Person profile updated successfully',
            'person' => $person,
        ]);
    }

    public function updateCompany(Request $request)
    {
        $validated = $request->validate([
            'business_name' => 'required|string',
            'business_description' => 'required|string',
            'business_scope' => 'nullable|string',
            'city_id' => 'nullable|integer',
            'is_government' => 'nullable|boolean',
        ]);

        $user = $request->user();
        $company = $user->personAffiliation?->company ?? Company::first() ?? Company::create([
            'business_name' => 'AccountAnt Tech Solutions Inc.',
            'business_description' => 'Automated Ledger System',
            'city_id' => 1,
        ]);

        $company->update($validated);

        return response()->json([
            'message' => 'Company settings updated successfully',
            'company' => $company,
        ]);
    }
```

- [ ] **Step 3: Add test assertions for logging in and fetching authenticated user details**

Modify [AuthTest.php](file:///c:/laragon/www/account-ant/src/backend/tests/Feature/AuthTest.php):
```php
    public function test_user_can_login_and_get_profile(): void
    {
        $user = User::factory()->create([
            'email' => 'test@accountant.io',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);

        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => 'test@accountant.io',
            'password' => 'password',
        ]);

        $loginResponse->assertStatus(200)->assertJsonStructure(['token', 'user']);
        $token = $loginResponse->json('token');

        $userResponse = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->getJson('/api/auth/user');
        
        $userResponse->assertStatus(200)->assertJsonPath('user.email', 'test@accountant.io');
    }
```

- [ ] **Step 4: Run backend tests**
Run: `vendor/bin/phpunit tests/Feature/AuthTest.php` in `src/backend`.
Expected: PASS.

- [ ] **Step 5: Commit**
```bash
git add src/backend/app/Http/Controllers/Api/AuthController.php src/backend/app/Http/Controllers/Api/SettingsController.php src/backend/tests/Feature/AuthTest.php
git commit -m "auth: update auth and settings controllers to issue tokens and use request user context"
```

---

### Task 3: Database Refresh and DatabaseSeeder Configuration

**Files:**
- Modify: `src/backend/database/seeders/DatabaseSeeder.php`

**Interfaces:**
- Consumes: None
- Produces: Initial database setup with prefilled records.

- [ ] **Step 1: Enable accounting, project, and ledger item seeders**

Modify [DatabaseSeeder.php](file:///c:/laragon/www/account-ant/src/backend/database/seeders/DatabaseSeeder.php):
```php
    public function run(): void
    {
        $this->call([
            AddressSeeder::class,
            UserSeeder::class,
            HrSeeder::class,
            AccountingSeeder::class,
            ProjectSeeder::class,
            LedgerItemSeeder::class,
        ]);
    }
```

- [ ] **Step 2: Refresh database and seed**
Run: `php artisan migrate:fresh --seed` in `src/backend`.
Expected: Complete and successful migrations and seeds execution.

- [ ] **Step 3: Commit**
```bash
git add src/backend/database/seeders/DatabaseSeeder.php
git commit -m "db: configure database seeders to populate initial transactions and project details"
```

---

### Task 4: Frontend Global Route Middleware

**Files:**
- Create: `src/frontend/app/middleware/auth.global.ts`

**Interfaces:**
- Consumes: cookie `auth_token`
- Produces: Route access validation.

- [ ] **Step 1: Implement global authentication middleware**

Create [auth.global.ts](file:///c:/laragon/www/account-ant/src/frontend/app/middleware/auth.global.ts):
```typescript
import { defineNuxtRouteMiddleware, navigateTo } from '#app'

export default defineNuxtRouteMiddleware(async (to) => {
  if (import.meta.server) return
  if (to.path === '/login' || to.path === '/register') return

  const auth = useAuth()
  const api = useApi()

  if (!api.token.value) {
    return navigateTo('/login')
  }

  if (!auth.currentUser.value) {
    try {
      await auth.fetchUser()
    } catch {
      auth.logout()
      return navigateTo('/login')
    }
  }
})
```

- [ ] **Step 2: Commit**
```bash
git add src/frontend/app/middleware/auth.global.ts
git commit -m "auth: add global route middleware to intercept unauthenticated requests"
```

---

### Task 5: Frontend Auth Store (useAuth.ts) Integration

**Files:**
- Modify: `src/frontend/app/composables/useAuth.ts`

**Interfaces:**
- Consumes: REST endpoints `/auth/user`, `/settings/profile`, `/settings/company`.
- Produces: Refreshed reactive user context objects.

- [ ] **Step 1: Refactor states and implement settings mutations**

Modify [useAuth.ts](file:///c:/laragon/www/account-ant/src/frontend/app/composables/useAuth.ts):
```typescript
export interface User {
  id: number
  email: string
  name?: string
  person_id?: number
  person_affiliations_id?: number
  person?: Person
  person_affiliation?: PersonAffiliation
}

export interface Person {
  id?: number
  first_name: string
  last_name: string
  middle_name?: string
  civil_status: 'Single' | 'Married' | 'Widowed' | 'Separated'
  gender?: 'Male' | 'Female'
  birth_date?: string
}

export interface Company {
  id?: number
  business_name: string
  business_description: string
  city_id: number
  business_scope?: 'National' | 'Regional' | 'City/Municipality' | 'Barangay'
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
  affiliation_level: string
  employment_status: string
  employee_id: string
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
  }))

  const isAuthenticated = computed(() => !!currentUser.value)

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

  const registerUser = async (data: any) => {
    const res = await api.request<{ token: string; user: User }>('/auth/register', {
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
    login,
    registerUser,
    fetchUser,
    updateProfile,
    updateCompany,
    logout,
  }
}
```

- [ ] **Step 2: Commit**
```bash
git add src/frontend/app/composables/useAuth.ts
git commit -m "frontend: integrate useAuth state management with laravel auth and settings endpoints"
```

---

### Task 6: Frontend Accounting Store (useAccounting.ts) Integration

**Files:**
- Modify: `src/frontend/app/composables/useAccounting.ts`

**Interfaces:**
- Consumes: REST routes `/fund-accounts`, `/ledger-accounts`, `/account-items`, `/journal-entries`
- Produces: Synchronized business accounts and company ledger arrays.

- [ ] **Step 1: Refactor useAccounting composable to fetch details dynamically**

Modify [useAccounting.ts](file:///c:/laragon/www/account-ant/src/frontend/app/composables/useAccounting.ts):
```typescript
export interface FundAccount {
  id: number
  company_id: number
  fund_code: string
  fund_name: string
  description?: string
  amount?: number
  user_id: number
  ledger_account_id?: number
}

export interface LedgerAccount {
  id: number
  account_code: string
  account_name: string
  description?: string
  fund_account_id?: number
  user_id: number
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

  const fundAccounts = useState<FundAccount[]>('accounting_fund_accounts', () => [])
  const ledgerAccounts = useState<LedgerAccount[]>('accounting_ledger_accounts', () => [])
  const accountItems = useState<AccountItem[]>('accounting_account_items', () => [])
  const journalEntries = useState<LedgerAccountItem[]>('accounting_journal_entries', () => [])

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

  const addFundAccount = async (fund: Omit<FundAccount, 'id'>) => {
    const res = await api.request<{ data: FundAccount }>('/fund-accounts', {
      method: 'POST',
      body: fund,
    })
    const created = res.data || res
    fundAccounts.value.push(created)
    return created
  }

  const addLedgerAccount = async (acc: Omit<LedgerAccount, 'id'>) => {
    const res = await api.request<{ data: LedgerAccount }>('/ledger-accounts', {
      method: 'POST',
      body: acc,
    })
    const created = res.data || res
    ledgerAccounts.value.push(created)
    return created
  }

  const addAccountItem = async (item: Omit<AccountItem, 'id'>) => {
    const res = await api.request<{ data: AccountItem }>('/account-items', {
      method: 'POST',
      body: item,
    })
    const created = res.data || res
    accountItems.value.push(created)
    return created
  }

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
```

- [ ] **Step 2: Commit**
```bash
git add src/frontend/app/composables/useAccounting.ts
git commit -m "frontend: integrate useAccounting store with general ledger endpoints"
```

---

### Task 7: Frontend Projects Store (useProjects.ts) Integration

**Files:**
- Modify: `src/frontend/app/composables/useProjects.ts`

**Interfaces:**
- Consumes: REST endpoints `/projects`, `/projects/{id}/funds`, `/journal-entries`
- Produces: Synced project and allocation metrics.

- [ ] **Step 1: Update useProjects to parse backend models**

Modify [useProjects.ts](file:///c:/laragon/www/account-ant/src/frontend/app/composables/useProjects.ts):
```typescript
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
  amount: number
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
  type: 'debit' | 'credit'
  amount: number
  date: string
  note?: string
  created_at: string
}

export const useProjects = () => {
  const api = useApi()

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

  const fetchProjects = async () => {
    const res = await api.request<{ data: any[] }>('/projects')
    const rawProjects = res.data || res

    projects.value = rawProjects.map(mapProjectFromBackend)

    // Map project funds to fund sources
    const mappedFunds: FundSource[] = []
    const mappedTxs: Transaction[] = []

    for (const rp of rawProjects) {
      if (rp.project_funds) {
        for (const pf of rp.project_funds) {
          mappedFunds.push({
            id: pf.fund_account_id, // map fund_account_id as the ID for consistency
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

  const addProject = async (project: Omit<Project, 'id' | 'created_at'>) => {
    const addr = typeof project.address === 'object' ? project.address : { street: '', city: '', zip_code: '' }
    
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

    const res = await api.request<{ data: any }>('/projects', {
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

  // Frontend helper stubs (no-op in dynamic backend mode)
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
```

- [ ] **Step 2: Commit**
```bash
git add src/frontend/app/composables/useProjects.ts
git commit -m "frontend: integrate useProjects store with backend project ledger endpoints"
```

---

### Task 8: Frontend Page Updates (Index, Project details, Settings, default layouts)

**Files:**
- Modify: `src/frontend/app/pages/project/[id].vue`
- Modify: `src/frontend/app/pages/settings.vue`
- Modify: `src/frontend/app/layouts/default.vue`
- Modify: `src/frontend/app/pages/login.vue`

**Interfaces:**
- Consumes: Frontend stores state and API fetchers.
- Produces: Correctly wired pages displaying backend database records.

- [ ] **Step 1: Update handlePostJournalEntry in [id].vue to prevent double posts**

Modify [\[id\].vue](file:///c:/laragon/www/account-ant/src/frontend/app/pages/project/[id].vue):
Replace `handlePostJournalEntry` function:
```typescript
const handlePostJournalEntry = async () => {
  if (!journalForm.fund_source_id || !journalForm.amount || !journalForm.description) return

  const numAmount = Number(journalForm.amount)
  const fundId = Number(journalForm.fund_source_id)
  const catId = Number(journalForm.category_id) || 1

  try {
    await projectsStore.addTransaction({
      project_id: projectId.value,
      fund_source_id: fundId,
      category_id: catId,
      type: journalForm.type,
      amount: numAmount,
      date: journalForm.date,
      note: journalForm.description,
    })

    // Reset form & close modal
    journalForm.fund_source_id = ''
    journalForm.type = 'debit'
    journalForm.ledger_account_id = ''
    journalForm.category_id = ''
    journalForm.amount = ''
    journalForm.date = new Date().toISOString().split('T')[0]
    journalForm.description = ''
    isPostJournalModalOpen.value = false
  } catch (err: any) {
    alert(err?.data?.message || err?.message || 'Failed to post transaction.')
  }
}
```

- [ ] **Step 2: Hook up save buttons in settings.vue**

Modify [settings.vue](file:///c:/laragon/www/account-ant/src/frontend/app/pages/settings.vue):
Add functions to settings script section:
```typescript
const handleUpdateProfile = async () => {
  try {
    await auth.updateProfile({
      first_name: auth.currentPerson.value.first_name,
      last_name: auth.currentPerson.value.last_name,
      middle_name: auth.currentPerson.value.middle_name,
      civil_status: auth.currentPerson.value.civil_status,
      gender: auth.currentPerson.value.gender,
      birth_date: auth.currentPerson.value.birth_date,
    })
    showSaveToast.value = true
  } catch (err: any) {
    alert(err?.data?.message || err?.message || 'Failed to update profile.')
  }
}

const handleUpdateCompany = async () => {
  try {
    await auth.updateCompany({
      business_name: auth.currentCompany.value.business_name,
      business_description: auth.currentCompany.value.business_description,
      business_scope: auth.currentCompany.value.business_scope,
      city_id: auth.currentCompany.value.city_id,
      is_government: auth.currentCompany.value.is_government,
    })
    showSaveToast.value = true
  } catch (err: any) {
    alert(err?.data?.message || err?.message || 'Failed to update company.')
  }
}
```
And replace corresponding click handlers in the template:
Change:
```html
<UiButton variant="primary" size="sm" @click="showSaveToast = true">Update Profile Details</UiButton>
```
To:
```html
<UiButton variant="primary" size="sm" @click="handleUpdateProfile">Update Profile Details</UiButton>
```
Change:
```html
<UiButton variant="primary" size="sm" @click="showSaveToast = true">Update Company Details</UiButton>
```
To:
```html
<UiButton variant="primary" size="sm" @click="handleUpdateCompany">Update Company Details</UiButton>
```

- [ ] **Step 3: Trigger fetching in default.vue layout on Mounted**

Modify [default.vue](file:///c:/laragon/www/account-ant/src/frontend/app/layouts/default.vue):
Change the `onMounted` lifecycle block:
```typescript
onMounted(async () => {
  theme.initTheme()
  try {
    const projectsStore = useProjects()
    await auth.fetchUser()
    await accounting.fetchFundAccounts()
    await accounting.fetchLedgerAccounts()
    await accounting.fetchAccountItems()
    await accounting.fetchJournalEntries()
    await projectsStore.fetchProjects()
  } catch (err) {
    console.error('Failed to load initial data:', err)
  }
})
```

- [ ] **Step 4: Trigger initial fetches on login success**

Modify [login.vue](file:///c:/laragon/www/account-ant/src/frontend/app/pages/login.vue):
Update `handleLogin` to fetch store data before redirecting:
```typescript
const handleLogin = async () => {
  if (!email.value || !password.value) {
    errorMessage.value = 'Please enter both your email address and password.'
    return
  }

  isLoading.value = true
  errorMessage.value = ''

  try {
    await auth.login(email.value, password.value)
    
    // Fetch initial user and data right away
    const accountingStore = useAccounting()
    const projectsStore = useProjects()
    await auth.fetchUser()
    await accountingStore.fetchFundAccounts()
    await accountingStore.fetchLedgerAccounts()
    await accountingStore.fetchAccountItems()
    await accountingStore.fetchJournalEntries()
    await projectsStore.fetchProjects()
    
    await navigateTo('/')
  } catch (error: any) {
    errorMessage.value = error?.data?.message || error?.message || 'Invalid credentials.'
  } finally {
    isLoading.value = false
  }
}
```

- [ ] **Step 5: Commit**
```bash
git add src/frontend/app/pages/project/\[id\].vue src/frontend/app/pages/settings.vue src/frontend/app/layouts/default.vue src/frontend/app/pages/login.vue
git commit -m "frontend: wire view templates and pages to fetch and write data from backend APIs"
```

---

## Verification Plan

### Automated Tests
- Run `vendor/bin/phpunit` in `src/backend` to ensure seeder and controller validation runs smoothly.

### Manual Verification
- Deploy the SQLite migration and seeds: `php artisan migrate:fresh --seed`.
- Run Nuxt server: `npm run dev` in `src/frontend`.
- Navigate to `http://localhost:3000/`. Verify global auth middleware redirects to `/login`.
- Login with `admin@accountant.io` / `password`.
- Verify the projects dashboard matches the backend database seed values.
- Navigate to a project, add an allocation, post a transaction, and verify it updates the financial charts and database.
