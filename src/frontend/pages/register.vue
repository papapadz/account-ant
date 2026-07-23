<template>
  <NuxtLayout name="auth">
    <div class="glass-card p-8 rounded-2xl border border-[#1E293B] shadow-2xl space-y-6">
      <!-- Step Header Progress -->
      <div>
        <div class="flex items-center justify-between text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">
          <span>Step {{ currentStep }} of 4: {{ stepTitle }}</span>
          <span class="text-emerald-400 font-mono">{{ Math.round((currentStep / 4) * 100) }}% Completed</span>
        </div>
        <div class="w-full h-1.5 bg-[#0F172A] rounded-full overflow-hidden border border-[#1E293B]">
          <div class="h-full bg-emerald-500 transition-all duration-300" :style="{ width: `${(currentStep / 4) * 100}%` }"></div>
        </div>
      </div>

      <!-- Registration Form -->
      <form @submit.prevent="handleNext">
        <!-- STEP 1: Account Credentials -->
        <div v-if="currentStep === 1" class="space-y-4">
          <h3 class="text-sm font-bold text-slate-200 uppercase tracking-wider">User Account Credentials</h3>
          <div>
            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Email Address *</label>
            <input v-model="formData.user.email" type="email" required placeholder="user@company.com" class="input-field" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Password *</label>
            <input v-model="formData.user.password" type="password" required placeholder="••••••••••••" class="input-field" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Confirm Password *</label>
            <input v-model="confirmPassword" type="password" required placeholder="••••••••••••" class="input-field" />
          </div>
        </div>

        <!-- STEP 2: Personal Profile (Person Model) -->
        <div v-if="currentStep === 2" class="space-y-4">
          <h3 class="text-sm font-bold text-slate-200 uppercase tracking-wider">Personal Profile Details</h3>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">First Name *</label>
              <input v-model="formData.person.first_name" type="text" required placeholder="Alexander" class="input-field" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Last Name *</label>
              <input v-model="formData.person.last_name" type="text" required placeholder="Sterling" class="input-field" />
            </div>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Middle Name</label>
              <input v-model="formData.person.middle_name" type="text" placeholder="Vance" class="input-field" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Civil Status *</label>
              <select v-model="formData.person.civil_status" class="input-field">
                <option value="Single">Single</option>
                <option value="Married">Married</option>
                <option value="Widowed">Widowed</option>
                <option value="Separated">Separated</option>
              </select>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Gender</label>
              <select v-model="formData.person.gender" class="input-field">
                <option value="Male">Male</option>
                <option value="Female">Female</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Birth Date</label>
              <input v-model="formData.person.birth_date" type="date" class="input-field" />
            </div>
          </div>
        </div>

        <!-- STEP 3: Company Setup (Company Model) -->
        <div v-if="currentStep === 3" class="space-y-4">
          <h3 class="text-sm font-bold text-slate-200 uppercase tracking-wider">Company / Entity Profile</h3>
          <div>
            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Business Name *</label>
            <input v-model="formData.company.business_name" type="text" required placeholder="Apex Financial Technologies Inc." class="input-field" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Business Description *</label>
            <input v-model="formData.company.business_description" type="text" required placeholder="Automated Ledger & Enterprise Asset Management" class="input-field" />
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Business Scope</label>
              <select v-model="formData.company.business_scope" class="input-field">
                <option value="National">National</option>
                <option value="Regional">Regional</option>
                <option value="City/Municipality">City / Municipality</option>
                <option value="Barangay">Barangay</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">City ID / Municipality</label>
              <input v-model.number="formData.company.city_id" type="number" required placeholder="101" class="input-field" />
            </div>
          </div>
          <div class="flex items-center gap-2 pt-2">
            <input id="govt" v-model="formData.company.is_government" type="checkbox" class="rounded bg-[#0F172A] border-[#334155] text-emerald-500" />
            <label for="govt" class="text-xs text-slate-300 font-medium">This company is a government entity</label>
          </div>
        </div>

        <!-- STEP 4: Position & Affiliation (Position & PersonAffiliation Models) -->
        <div v-if="currentStep === 4" class="space-y-4">
          <h3 class="text-sm font-bold text-slate-200 uppercase tracking-wider">Position & Employment Affiliation</h3>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Position Title *</label>
              <input v-model="formData.position.title" type="text" required placeholder="Chief Financial Officer" class="input-field" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Industry *</label>
              <select v-model="formData.position.industry" class="input-field">
                <option value="Finance">Finance</option>
                <option value="Information Technology">Information Technology</option>
                <option value="Health">Health</option>
                <option value="Education">Education</option>
                <option value="Retail">Retail</option>
                <option value="Manufacturing">Manufacturing</option>
              </select>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Affiliation Level *</label>
              <select v-model="formData.affiliation.affiliation_level" class="input-field">
                <option value="Executive">Executive</option>
                <option value="Managerial">Managerial</option>
                <option value="Supervisory">Supervisory</option>
                <option value="Rank and File">Rank and File</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Employment Status *</label>
              <select v-model="formData.affiliation.employment_status" class="input-field">
                <option value="Regular">Regular</option>
                <option value="Contractual">Contractual</option>
                <option value="Part-time">Part-time</option>
                <option value="Internship">Internship</option>
              </select>
            </div>
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Employee ID Code *</label>
            <input v-model="formData.affiliation.employee_id" type="text" required placeholder="EMP-2026-901" class="input-field font-mono" />
          </div>
          <div class="flex items-center gap-2 pt-2">
            <input id="is_head" v-model="formData.affiliation.is_head" type="checkbox" class="rounded bg-[#0F172A] border-[#334155] text-emerald-500" />
            <label for="is_head" class="text-xs text-slate-300 font-medium">Head of Department / Unit Executive</label>
          </div>
        </div>

        <!-- Action Controls -->
        <div class="pt-6 border-t border-[#1E293B] flex items-center justify-between gap-3">
          <button
            v-if="currentStep > 1"
            type="button"
            @click="currentStep--"
            class="btn-secondary py-2.5 px-4 text-xs"
          >
            &larr; Back
          </button>
          <div v-else></div>

          <button
            type="submit"
            :disabled="isSubmitting"
            class="btn-primary py-2.5 px-6 text-xs font-bold shadow-lg shadow-emerald-500/20"
          >
            <span v-if="isSubmitting">Registering Entity...</span>
            <span v-else-if="currentStep < 4">Continue &rarr;</span>
            <span v-else>Complete Onboarding & Launch &rarr;</span>
          </button>
        </div>
      </form>
    </div>
  </NuxtLayout>
</template>

<script setup lang="ts">
definePageMeta({
  layout: false,
})

const auth = useAuth()
const currentStep = ref(1)
const confirmPassword = ref('')
const isSubmitting = ref(false)

const formData = reactive({
  user: {
    email: '',
    password: '',
  },
  person: {
    first_name: '',
    last_name: '',
    middle_name: '',
    civil_status: 'Single' as const,
    gender: 'Male' as const,
    birth_date: '1990-01-01',
  },
  company: {
    business_name: '',
    business_description: '',
    city_id: 101,
    business_scope: 'National' as const,
    is_government: false,
  },
  position: {
    title: '',
    industry: 'Finance' as const,
    salary_grade: 18,
  },
  affiliation: {
    affiliation_level: 'Executive' as const,
    employment_status: 'Regular' as const,
    employee_id: 'EMP-2026-001',
    is_head: true,
  },
})

const stepTitle = computed(() => {
  switch (currentStep.value) {
    case 1: return 'Account Credentials'
    case 2: return 'Personal Information'
    case 3: return 'Company Profile'
    case 4: return 'Position & Affiliation'
    default: return 'Onboarding'
  }
})

const handleNext = async () => {
  if (currentStep.value < 4) {
    currentStep.value++
    return
  }

  isSubmitting.value = true
  try {
    await auth.registerUser(formData)
    await navigateTo('/')
  } finally {
    isSubmitting.value = false
  }
}
</script>
