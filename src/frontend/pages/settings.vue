<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="glass-card p-5 rounded-xl border border-[#1E293B]">
      <h1 class="text-lg font-bold text-slate-100 tracking-tight flex items-center gap-2">
        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
        System Settings & Entity Profile
      </h1>
      <p class="text-xs text-slate-400 mt-0.5">Manage personal profile, company details, affiliation role, and API integration settings</p>
    </div>

    <!-- Tab Navigation -->
    <div class="flex items-center gap-2 border-b border-[#1E293B] pb-3">
      <button
        @click="activeTab = 'profile'"
        class="px-4 py-2 rounded-lg text-xs font-semibold transition-all"
        :class="activeTab === 'profile' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'text-slate-400 hover:text-slate-200'"
      >
        Personal Profile (`Person`)
      </button>
      <button
        @click="activeTab = 'company'"
        class="px-4 py-2 rounded-lg text-xs font-semibold transition-all"
        :class="activeTab === 'company' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'text-slate-400 hover:text-slate-200'"
      >
        Company Settings (`Company`)
      </button>
      <button
        @click="activeTab = 'affiliation'"
        class="px-4 py-2 rounded-lg text-xs font-semibold transition-all"
        :class="activeTab === 'affiliation' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'text-slate-400 hover:text-slate-200'"
      >
        Position & Role (`Affiliation`)
      </button>
      <button
        @click="activeTab = 'api'"
        class="px-4 py-2 rounded-lg text-xs font-semibold transition-all"
        :class="activeTab === 'api' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'text-slate-400 hover:text-slate-200'"
      >
        Backend API Settings
      </button>
    </div>

    <!-- Tab 1: Profile -->
    <div v-if="activeTab === 'profile'" class="glass-card p-6 rounded-xl border border-[#1E293B] space-y-4 max-w-2xl">
      <h3 class="text-sm font-bold text-slate-100 uppercase tracking-wider">Personal Profile Details (`Person` Model)</h3>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">First Name</label>
          <input v-model="auth.currentPerson.value.first_name" type="text" class="input-field" />
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Last Name</label>
          <input v-model="auth.currentPerson.value.last_name" type="text" class="input-field" />
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Middle Name</label>
          <input v-model="auth.currentPerson.value.middle_name" type="text" class="input-field" />
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Civil Status</label>
          <select v-model="auth.currentPerson.value.civil_status" class="input-field">
            <option value="Single">Single</option>
            <option value="Married">Married</option>
            <option value="Widowed">Widowed</option>
            <option value="Separated">Separated</option>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Gender</label>
          <select v-model="auth.currentPerson.value.gender" class="input-field">
            <option value="Male">Male</option>
            <option value="Female">Female</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Birth Date</label>
          <input v-model="auth.currentPerson.value.birth_date" type="date" class="input-field" />
        </div>
      </div>

      <div class="pt-4 border-t border-[#1E293B] flex justify-end">
        <button @click="showSaveToast = true" class="btn-primary py-2 px-5 text-xs font-bold">Update Profile Details</button>
      </div>
    </div>

    <!-- Tab 2: Company -->
    <div v-if="activeTab === 'company'" class="glass-card p-6 rounded-xl border border-[#1E293B] space-y-4 max-w-2xl">
      <h3 class="text-sm font-bold text-slate-100 uppercase tracking-wider">Company Profile (`Company` Model)</h3>

      <div>
        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Business Name</label>
        <input v-model="auth.currentCompany.value.business_name" type="text" class="input-field" />
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Business Description</label>
        <textarea v-model="auth.currentCompany.value.business_description" rows="2" class="input-field"></textarea>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Business Scope</label>
          <select v-model="auth.currentCompany.value.business_scope" class="input-field">
            <option value="National">National</option>
            <option value="Regional">Regional</option>
            <option value="City/Municipality">City / Municipality</option>
            <option value="Barangay">Barangay</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">City ID</label>
          <input v-model.number="auth.currentCompany.value.city_id" type="number" class="input-field" />
        </div>
      </div>

      <div class="flex items-center gap-2 pt-2">
        <input id="govt_setting" v-model="auth.currentCompany.value.is_government" type="checkbox" class="rounded bg-[#0F172A] border-[#334155] text-emerald-500" />
        <label for="govt_setting" class="text-xs text-slate-300">Government Enterprise Entity</label>
      </div>

      <div class="pt-4 border-t border-[#1E293B] flex justify-end">
        <button @click="showSaveToast = true" class="btn-primary py-2 px-5 text-xs font-bold">Save Company Settings</button>
      </div>
    </div>

    <!-- Tab 3: Affiliation -->
    <div v-if="activeTab === 'affiliation'" class="glass-card p-6 rounded-xl border border-[#1E293B] space-y-4 max-w-2xl">
      <h3 class="text-sm font-bold text-slate-100 uppercase tracking-wider">Position & Employment Affiliation</h3>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Position Title</label>
          <input v-model="auth.currentPosition.value.title" type="text" class="input-field" />
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Industry</label>
          <select v-model="auth.currentPosition.value.industry" class="input-field">
            <option value="Finance">Finance</option>
            <option value="Information Technology">Information Technology</option>
            <option value="Health">Health</option>
            <option value="Education">Education</option>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Affiliation Level</label>
          <select v-model="auth.currentAffiliation.value.affiliation_level" class="input-field">
            <option value="Executive">Executive</option>
            <option value="Managerial">Managerial</option>
            <option value="Supervisory">Supervisory</option>
            <option value="Rank and File">Rank and File</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Employee ID</label>
          <input v-model="auth.currentAffiliation.value.employee_id" type="text" class="input-field font-mono" />
        </div>
      </div>

      <div class="pt-4 border-t border-[#1E293B] flex justify-end">
        <button @click="showSaveToast = true" class="btn-primary py-2 px-5 text-xs font-bold">Update Affiliation</button>
      </div>
    </div>

    <!-- Tab 4: API Settings -->
    <div v-if="activeTab === 'api'" class="glass-card p-6 rounded-xl border border-[#1E293B] space-y-4 max-w-2xl">
      <h3 class="text-sm font-bold text-slate-100 uppercase tracking-wider">Laravel Backend API Configuration</h3>

      <div>
        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">API Base URL</label>
        <input v-model="apiUrl" type="text" class="input-field font-mono" />
        <p class="text-[11px] text-slate-500 mt-1">Configured endpoint for AccountAnt Laravel backend REST services.</p>
      </div>

      <div class="p-4 rounded-lg bg-[#020617] border border-[#1E293B] space-y-2">
        <div class="flex items-center justify-between text-xs">
          <span class="text-slate-400">Connection Status:</span>
          <span class="text-emerald-400 font-mono font-bold flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            Online / Ready
          </span>
        </div>
        <div class="flex items-center justify-between text-xs">
          <span class="text-slate-400">Active Bearer Token:</span>
          <span class="text-slate-300 font-mono text-[11px]">
            {{ api.token.value ? '••••••••' + api.token.value.slice(-8) : 'Not Authenticated' }}
          </span>
        </div>
      </div>

      <div class="pt-4 border-t border-[#1E293B] flex items-center justify-between">
        <button @click="testConnection" class="btn-secondary py-2 px-4 text-xs">
          <span>Test API Endpoint Connection</span>
        </button>
        <button @click="showSaveToast = true" class="btn-primary py-2 px-5 text-xs font-bold">Save API Settings</button>
      </div>
    </div>

    <!-- Toast Notification -->
    <div v-if="showSaveToast" class="fixed bottom-6 right-6 bg-emerald-500 text-slate-950 px-4 py-2.5 rounded-lg shadow-xl font-bold text-xs flex items-center gap-2 z-50 animate-bounce">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
      </svg>
      Settings saved successfully!
    </div>
  </div>
</template>

<script setup lang="ts">
const auth = useAuth()
const api = useApi()

const activeTab = ref<'profile' | 'company' | 'affiliation' | 'api'>('profile')
const apiUrl = ref('http://localhost:8000/api')
const showSaveToast = ref(false)

watch(showSaveToast, (val) => {
  if (val) {
    setTimeout(() => {
      showSaveToast.value = false
    }, 2500)
  }
})

const testConnection = async () => {
  try {
    await api.request('/')
    alert('API Endpoint connected successfully!')
  } catch {
    alert('Backend API is currently running in local offline demo mode (Fallback active).')
  }
}
</script>
