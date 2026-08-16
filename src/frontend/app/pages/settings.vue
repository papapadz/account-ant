<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="glass-card p-5 rounded-xl border border-[var(--border-color)]">
      <h1 class="text-lg font-bold text-[var(--text-main)] tracking-tight flex items-center gap-2">
        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
        System Settings & Entity Profile
      </h1>
      <p class="text-xs text-[var(--text-muted)] mt-0.5">Manage personal profile, company details, affiliation role, and database backup settings</p>
    </div>

    <!-- Tab Navigation -->
    <UiTabs v-model="activeTab" :items="settingsTabs" variant="underline" />

    <!-- Tab 1: Profile -->
    <div v-if="activeTab === 'profile'" class="glass-card p-6 rounded-xl border border-[var(--border-color)] space-y-4 max-w-2xl">
      <h3 class="text-sm font-bold text-[var(--text-main)] uppercase tracking-wider">Personal Profile Details (`Person` Model)</h3>

      <!-- <div v-if="!auth.isSuperAdmin.value" class="p-3 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <span>Only Super Admin accounts have permission to update profile details.</span>
      </div> -->

      <ClientOnly>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">First Name</label>
            <input v-model="auth.currentPerson.value.first_name" type="text" class="input-field" :disabled="!auth.isSuperAdmin.value" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Last Name</label>
            <input v-model="auth.currentPerson.value.last_name" type="text" class="input-field" :disabled="!auth.isSuperAdmin.value" />
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Middle Name</label>
            <input v-model="auth.currentPerson.value.middle_name" type="text" class="input-field" :disabled="!auth.isSuperAdmin.value" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Civil Status</label>
            <select v-model="auth.currentPerson.value.civil_status" class="input-field" :disabled="!auth.isSuperAdmin.value">
              <option value="Single">Single</option>
              <option value="Married">Married</option>
              <option value="Widowed">Widowed</option>
              <option value="Separated">Separated</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Gender</label>
            <select v-model="auth.currentPerson.value.gender" class="input-field" :disabled="!auth.isSuperAdmin.value">
              <option value="Male">Male</option>
              <option value="Female">Female</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Birth Date</label>
            <input v-model="auth.currentPerson.value.birth_date" type="date" class="input-field" :disabled="!auth.isSuperAdmin.value" />
          </div>
        </div>
      </ClientOnly>

      <div class="pt-4 border-t border-[var(--border-color)] flex justify-end">
        <UiButton variant="primary" size="sm" :disabled="!auth.isSuperAdmin.value" @click="handleUpdateProfile">Update Profile Details</UiButton>
      </div>
    </div>

    <!-- Tab 2: Company -->
    <div v-if="activeTab === 'company'" class="glass-card p-6 rounded-xl border border-[var(--border-color)] space-y-4 max-w-2xl">
      <h3 class="text-sm font-bold text-[var(--text-main)] uppercase tracking-wider">Company Profile</h3>

      <!-- <div v-if="!auth.isSuperAdmin.value" class="p-3 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <span>Only Super Admin accounts have permission to update company settings.</span>
      </div> -->

      <ClientOnly>
        <div>
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Business Name</label>
          <input v-model="auth.currentCompany.value.business_name" type="text" class="input-field" :disabled="!auth.isSuperAdmin.value" />
        </div>

        <div>
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Business Description</label>
          <textarea v-model="auth.currentCompany.value.business_description" rows="2" class="input-field" :disabled="!auth.isSuperAdmin.value"></textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Business Scope</label>
            <select v-model="auth.currentCompany.value.business_scope" class="input-field" :disabled="!auth.isSuperAdmin.value">
              <option value="National">National</option>
              <option value="Regional">Regional</option>
              <option value="City/Municipality">City / Municipality</option>
              <option value="Barangay">Barangay</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">City ID</label>
            <input v-model.number="auth.currentCompany.value.city_id" type="number" class="input-field" :disabled="!auth.isSuperAdmin.value" />
          </div>
        </div>

        <div class="flex items-center gap-2 pt-2">
          <input id="govt_setting" v-model="auth.currentCompany.value.is_government" type="checkbox" class="rounded bg-[var(--bg-input)] border-[var(--border-color)] text-emerald-500" :disabled="!auth.isSuperAdmin.value" />
          <label for="govt_setting" class="text-xs text-[var(--text-main)]">Government Enterprise Entity</label>
        </div>
      </ClientOnly>

      <div class="pt-4 border-t border-[var(--border-color)] flex justify-end">
        <UiButton variant="primary" size="sm" :disabled="!auth.isSuperAdmin.value" @click="handleUpdateCompany">Save Company Settings</UiButton>
      </div>
    </div>

    <!-- Tab 3: Affiliation -->
    <div v-if="activeTab === 'affiliation'" class="glass-card p-6 rounded-xl border border-[var(--border-color)] space-y-4 max-w-2xl">
      <h3 class="text-sm font-bold text-[var(--text-main)] uppercase tracking-wider">Position & Employment Affiliation</h3>

      <!-- <div v-if="!auth.isSuperAdmin.value" class="p-3 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <span>Only Super Admin accounts have permission to update affiliation details.</span>
      </div> -->

      <ClientOnly>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Position Title</label>
            <input v-model="auth.currentPosition.value.title" type="text" class="input-field" :disabled="!auth.isSuperAdmin.value" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Industry</label>
            <select v-model="auth.currentPosition.value.industry" class="input-field" :disabled="!auth.isSuperAdmin.value">
              <option value="Finance">Finance</option>
              <option value="Information Technology">Information Technology</option>
              <option value="Health">Health</option>
              <option value="Education">Education</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Affiliation Level</label>
            <select v-model="auth.currentAffiliation.value.affiliation_level" class="input-field" :disabled="!auth.isSuperAdmin.value">
              <option value="Executive">Executive</option>
              <option value="Managerial">Managerial</option>
              <option value="Supervisory">Supervisory</option>
              <option value="Rank and File">Rank and File</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Employee ID</label>
            <input v-model="auth.currentAffiliation.value.employee_id" type="text" class="input-field font-mono" :disabled="!auth.isSuperAdmin.value" />
          </div>
        </div>
      </ClientOnly>

      <div class="pt-4 border-t border-[var(--border-color)] flex justify-end">
        <UiButton variant="primary" size="sm" :disabled="!auth.isSuperAdmin.value" @click="showSaveToast = true">Update Affiliation</UiButton>
      </div>
    </div>

    <!-- Tab 4: API Settings -->
    <!-- Tab 4: Database Backup & Storage -->
    <div v-if="activeTab === 'backup'" class="glass-card p-6 rounded-xl border border-[var(--border-color)] space-y-6 max-w-3xl">
      <div>
        <h3 class="text-sm font-bold text-[var(--text-main)] uppercase tracking-wider flex items-center gap-2">
          <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
          </svg>
          SQLite Database Backup & Maintenance
        </h3>
        <p class="text-xs text-[var(--text-muted)] mt-1">Export, download, and safeguard your enterprise accounting database records.</p>
      </div>

      <!-- Database Status Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="p-4 rounded-xl bg-[var(--bg-surface)] border border-[var(--border-color)] space-y-2">
          <div class="flex items-center justify-between text-xs">
            <span class="text-[var(--text-muted)] font-medium">Database Engine:</span>
            <span class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-mono text-[11px] font-bold">SQLite3 WAL</span>
          </div>
          <div class="flex items-center justify-between text-xs">
            <span class="text-[var(--text-muted)] font-medium">Active Target:</span>
            <span class="font-mono text-[11px] text-[var(--text-main)]">nativephp.sqlite</span>
          </div>
          <div class="flex items-center justify-between text-xs">
            <span class="text-[var(--text-muted)] font-medium">Health Status:</span>
            <span class="text-emerald-500 font-bold text-xs flex items-center gap-1.5">
              <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
              Healthy / Ready
            </span>
          </div>
        </div>

        <div class="p-4 rounded-xl bg-[var(--bg-surface)] border border-[var(--border-color)] space-y-2">
          <div class="flex items-center justify-between text-xs">
            <span class="text-[var(--text-muted)] font-medium">Fund Accounts:</span>
            <span class="font-mono text-xs font-bold text-emerald-400">{{ accounting.fundAccounts.value.length }}</span>
          </div>
          <div class="flex items-center justify-between text-xs">
            <span class="text-[var(--text-muted)] font-medium">Journal Entries:</span>
            <span class="font-mono text-xs font-bold text-emerald-400">{{ accounting.journalEntries.value.length }}</span>
          </div>
          <div class="flex items-center justify-between text-xs">
            <span class="text-[var(--text-muted)] font-medium">Active Projects:</span>
            <span class="font-mono text-xs font-bold text-emerald-400">{{ projectsStore.projects.value.length }}</span>
          </div>
        </div>
      </div>

      <!-- Backup Options -->
      <div class="space-y-4 pt-2">
       
        <!-- Option 1: SQLite File Download -->
        <!-- <div class="p-4 rounded-xl border border-[var(--border-color)] bg-[var(--bg-surface)] flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
          <div class="space-y-1">
            <h5 class="text-xs font-bold text-[var(--text-main)] flex items-center gap-2">
              <span>Full SQLite Database File (.sqlite)</span>
              <span class="px-2 py-0.5 rounded text-[10px] bg-blue-500/10 text-blue-400 border border-blue-500/20 font-mono font-bold">Binary File</span>
            </h5>
            <p class="text-[11px] text-[var(--text-muted)]">
              Download the physical SQLite database file for full system migration or offline storage.
            </p>
          </div>
          <UiButton variant="primary" size="sm" :disabled="isDownloading" @click="downloadSqliteBackup">
            <svg v-if="!isDownloading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            <span v-if="isDownloading">Generating...</span>
            <span v-else>Download .sqlite File</span>
          </UiButton>
        </div> -->

        <!-- Option 2: Structured JSON Data Dump -->
        <div class="p-4 rounded-xl border border-[var(--border-color)] bg-[var(--bg-surface)] flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
          <div class="space-y-1">
            <h5 class="text-xs font-bold text-[var(--text-main)] flex items-center gap-2">
              <span>Structured Data Archive (.json)</span>
              <span class="px-2 py-0.5 rounded text-[10px] bg-purple-500/10 text-purple-400 border border-purple-500/20 font-mono font-bold">JSON Archive</span>
            </h5>
            <p class="text-[11px] text-[var(--text-muted)]">
              Export all system records (Fund Accounts, Journal Entries, Projects, People) as readable JSON for audit reporting.
            </p>
          </div>
          <UiButton variant="secondary" size="sm" :disabled="isDownloadingJson" @click="downloadJsonBackup">
            <svg v-if="!isDownloadingJson" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span v-if="isDownloadingJson">Exporting...</span>
            <span v-else>Export .json Backup</span>
          </UiButton>
        </div>

        <!-- Option 3: Wipe Database Data -->
        <div class="p-4 rounded-xl border border-rose-500/30 bg-rose-950/10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
          <div class="space-y-1">
            <h5 class="text-xs font-bold text-rose-400 flex items-center gap-2">
              <span>Wipe System Data</span>
              <span class="px-2 py-0.5 rounded text-[10px] bg-rose-500/20 text-rose-300 border border-rose-500/30 font-mono font-bold">Destructive</span>
            </h5>
            <p class="text-[11px] text-[var(--text-muted)]">
              Permanently purges all transaction, ledger, journal entry, and project records. Preserves Address, HR, Person, User, and Device Info.
            </p>
          </div>
          <div class="flex items-center gap-2">
            <UiButton v-if="!showWipeConfirm" variant="danger" size="sm" :disabled="isWiping" @click="showWipeConfirm = true">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
              <span>Wipe Database Data</span>
            </UiButton>
            <template v-else>
              <UiButton variant="danger" size="sm" :loading="isWiping" @click="handleWipeData">
                Confirm Wipe
              </UiButton>
              <UiButton variant="ghost" size="sm" :disabled="isWiping" @click="showWipeConfirm = false">
                Cancel
              </UiButton>
            </template>
          </div>
        </div>
      </div>
    </div>

    <!-- Toast Notification -->
    <div v-if="showSaveToast" class="fixed bottom-6 right-6 bg-emerald-500 text-slate-950 px-4 py-2.5 rounded-lg shadow-xl font-bold text-xs flex items-center gap-2 z-50 animate-bounce">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
      </svg>
      Action completed successfully!
    </div>
  </div>
</template>

<script setup lang="ts">
const auth = useAuth()
const api = useApi()
const accounting = useAccounting()
const projectsStore = useProjects()

const activeTab = ref<'profile' | 'company' | 'affiliation' | 'backup'>('profile')

const settingsTabs = [
  { value: 'profile', label: 'Personal Profile (Person)' },
  { value: 'company', label: 'Company Settings (Company)' },
  { value: 'affiliation', label: 'Position & Role (Affiliation)' },
  { value: 'backup', label: 'Database Backup & Storage' },
]
const apiUrl = ref('http://localhost:8000/api')
const showSaveToast = ref(false)
const isDownloading = ref(false)
const isDownloadingJson = ref(false)
const isWiping = ref(false)
const showWipeConfirm = ref(false)

watch(showSaveToast, (val) => {
  if (val) {
    setTimeout(() => {
      showSaveToast.value = false
    }, 2500)
  }
})

const downloadSqliteBackup = async () => {
  isDownloading.value = true
  try {
    const config = useRuntimeConfig()
    const baseUrl = config.public.apiBase || '/api'
    const token = api.token.value
    
    const response = await fetch(`${baseUrl}/settings/backup?format=sqlite`, {
      headers: token ? { Authorization: `Bearer ${token}` } : {},
    })

    if (!response.ok) throw new Error('Backup download failed.')

    const blob = await response.blob()
    const url = window.URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `accountant-sqlite-backup-${new Date().toISOString().split('T')[0]}.sqlite`
    document.body.appendChild(a)
    a.click()
    document.body.removeChild(a)
    window.URL.revokeObjectURL(url)
    showSaveToast.value = true
  } catch (err: any) {
    alert('Failed to download SQLite backup file. Please ensure backend is running.')
  } finally {
    isDownloading.value = false
  }
}

const downloadJsonBackup = async () => {
  isDownloadingJson.value = true
  try {
    const config = useRuntimeConfig()
    const baseUrl = config.public.apiBase || '/api'
    const token = api.token.value

    const response = await fetch(`${baseUrl}/settings/backup?format=json`, {
      headers: token ? { Authorization: `Bearer ${token}` } : {},
    })

    if (response.ok) {
      const blob = await response.blob()
      const url = window.URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = `accountant-backup-${new Date().toISOString().split('T')[0]}.json`
      document.body.appendChild(a)
      a.click()
      document.body.removeChild(a)
      window.URL.revokeObjectURL(url)
    } else {
      // Fallback: client-side Nuxt state dump
      const clientDump = {
        exported_at: new Date().toISOString(),
        system: 'AccountAnt Ledger System (Client State Export)',
        fund_accounts: accounting.fundAccounts.value,
        ledger_accounts: accounting.ledgerAccounts.value,
        account_items: accounting.accountItems.value,
        journal_entries: accounting.journalEntries.value,
        projects: projectsStore.projects.value,
      }
      const dataStr = 'data:text/json;charset=utf-8,' + encodeURIComponent(JSON.stringify(clientDump, null, 2))
      const a = document.createElement('a')
      a.href = dataStr
      a.download = `accountant-backup-${new Date().toISOString().split('T')[0]}.json`
      document.body.appendChild(a)
      a.click()
      document.body.removeChild(a)
    }
    showSaveToast.value = true
  } catch (err: any) {
    // Client-side fallback dump
    const clientDump = {
      exported_at: new Date().toISOString(),
      system: 'AccountAnt Ledger System (Client State Export)',
      fund_accounts: accounting.fundAccounts.value,
      ledger_accounts: accounting.ledgerAccounts.value,
      account_items: accounting.accountItems.value,
      journal_entries: accounting.journalEntries.value,
      projects: projectsStore.projects.value,
    }
    const dataStr = 'data:text/json;charset=utf-8,' + encodeURIComponent(JSON.stringify(clientDump, null, 2))
    const a = document.createElement('a')
    a.href = dataStr
    a.download = `accountant-backup-${new Date().toISOString().split('T')[0]}.json`
    document.body.appendChild(a)
    a.click()
    document.body.removeChild(a)
    showSaveToast.value = true
  } finally {
    isDownloadingJson.value = false
  }
}

const testConnection = async () => {
  try {
    await api.request('/')
    alert('API Endpoint connected successfully!')
  } catch {
    alert('Backend API is currently running in local offline demo mode (Fallback active).')
  }
}

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

const handleWipeData = async () => {
  isWiping.value = true
  try {
    await api.request('/settings/wipe-data', {
      method: 'POST',
    })
    showWipeConfirm.value = false
    showSaveToast.value = true

    // Refresh application stores to reflect wiped state
    await Promise.allSettled([
      accounting.fetchFundAccounts(),
      accounting.fetchLedgerAccounts(),
      accounting.fetchAccountItems(),
      accounting.fetchJournalEntries(),
      projectsStore.fetchProjects(),
    ])
  } catch (err: any) {
    alert(err?.data?.message || err?.message || 'Failed to wipe database data.')
  } finally {
    isWiping.value = false
  }
}
</script>
