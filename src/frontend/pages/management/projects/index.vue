<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 glass-card p-5 rounded-xl border border-[#1E293B]">
      <div>
        <h1 class="text-lg font-bold text-slate-100 tracking-tight flex items-center gap-2">
          <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m3 0h1m-4-8a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2a1 1 0 01-1-1v-4z" />
          </svg>
          Project Ledger Management
        </h1>
        <p class="text-xs text-slate-400 mt-0.5">Manage projects, address specifications, fund allocations, and running budget metrics</p>
      </div>

      <button @click="isCreateModalOpen = true" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
        </svg>
        <span>+ Create New Project</span>
      </button>
    </div>

    <!-- Top KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
      <div class="bg-[#0F172A] border border-[#1E293B] p-4 rounded-xl flex items-center justify-between">
        <div>
          <span class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider block">Total Projects</span>
          <span class="text-xl font-bold font-mono-num text-slate-100">{{ projectStore.projects.value.length }}</span>
        </div>
        <div class="w-8 h-8 rounded bg-emerald-500/10 text-emerald-400 flex items-center justify-center font-bold text-xs">PRJ</div>
      </div>

      <div class="bg-[#0F172A] border border-[#1E293B] p-4 rounded-xl flex items-center justify-between">
        <div>
          <span class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider block">Total Project Budget</span>
          <span class="text-xl font-bold font-mono-num text-emerald-400">${{ formatCurrency(totalBudgetSum) }}</span>
        </div>
        <div class="w-8 h-8 rounded bg-blue-500/10 text-blue-400 flex items-center justify-center font-bold text-xs">$</div>
      </div>

      <div class="bg-[#0F172A] border border-[#1E293B] p-4 rounded-xl flex items-center justify-between">
        <div>
          <span class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider block">Allocated Fund Pool</span>
          <span class="text-xl font-bold font-mono-num text-blue-400">${{ formatCurrency(totalAllocatedSum) }}</span>
        </div>
        <div class="w-8 h-8 rounded bg-amber-500/10 text-amber-400 flex items-center justify-center font-bold text-xs">FND</div>
      </div>

      <div class="bg-[#0F172A] border border-[#1E293B] p-4 rounded-xl flex items-center justify-between">
        <div>
          <span class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider block">Gov vs Private Ratio</span>
          <span class="text-xl font-bold font-mono-num text-purple-400">{{ govCount }} : {{ privateCount }}</span>
        </div>
        <div class="w-8 h-8 rounded bg-purple-500/10 text-purple-400 flex items-center justify-center font-bold text-xs">GOV</div>
      </div>
    </div>

    <!-- Filter & Table -->
    <div class="glass-card rounded-xl border border-[#1E293B] overflow-hidden">
      <div class="p-4 border-b border-[#1E293B] flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="flex items-center gap-2">
          <button
            @click="filterType = 'all'"
            class="px-3 py-1 rounded text-xs font-semibold transition-colors"
            :class="filterType === 'all' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'text-slate-400 hover:text-slate-200'"
          >
            All Projects ({{ projectStore.projects.value.length }})
          </button>
          <button
            @click="filterType = 'gov'"
            class="px-3 py-1 rounded text-xs font-semibold transition-colors"
            :class="filterType === 'gov' ? 'bg-purple-500/20 text-purple-400 border border-purple-500/30' : 'text-slate-400 hover:text-slate-200'"
          >
            Government ({{ govCount }})
          </button>
          <button
            @click="filterType = 'private'"
            class="px-3 py-1 rounded text-xs font-semibold transition-colors"
            :class="filterType === 'private' ? 'bg-blue-500/20 text-blue-400 border border-blue-500/30' : 'text-slate-400 hover:text-slate-200'"
          >
            Private Corporate ({{ privateCount }})
          </button>
        </div>

        <input
          v-model="searchQuery"
          type="text"
          placeholder="Filter by project, client, address..."
          class="input-field max-w-xs py-1.5 text-xs"
        />
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="text-[11px] text-slate-400 uppercase tracking-wider bg-[#0B1120] border-b border-[#1E293B]">
              <th class="p-3.5 font-semibold">Project Name & Client</th>
              <th class="p-3.5 font-semibold">Location Address</th>
              <th class="p-3.5 font-semibold">Sector</th>
              <th class="p-3.5 font-semibold text-right">Total Budget</th>
              <th class="p-3.5 font-semibold text-right">Allocated Funds</th>
              <th class="p-3.5 font-semibold">Budget Utilization</th>
              <th class="p-3.5 font-semibold text-center">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#1E293B]/60 text-xs">
            <tr v-for="proj in filteredProjects" :key="proj.id" class="hover:bg-[#1E293B]/40 transition-colors">
              <td class="p-3.5">
                <NuxtLink :to="`/management/projects/${proj.id}`" class="font-bold text-slate-100 hover:text-emerald-400 transition-colors block text-sm">
                  {{ proj.name }}
                </NuxtLink>
                <div class="text-[11px] text-slate-400 flex items-center gap-1.5 mt-0.5">
                  <span class="font-semibold text-slate-300">Client:</span> {{ proj.client_name }}
                  <span class="text-slate-600">•</span>
                  <span>{{ proj.start_date }} to {{ proj.end_date || 'Ongoing' }}</span>
                </div>
              </td>

              <td class="p-3.5 text-slate-300 max-w-xs">
                <div class="flex items-center gap-1 text-[11px] text-emerald-400 font-semibold mb-0.5">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                  {{ proj.city_name || 'Quezon City' }} (Zip: {{ proj.zip }})
                </div>
                <div class="text-[11px] text-slate-400 truncate">
                  {{ proj.street ? proj.street + ', ' : '' }}{{ proj.barangay }}
                </div>
              </td>

              <td class="p-3.5">
                <span
                  class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border"
                  :class="proj.is_government ? 'bg-purple-500/10 text-purple-400 border-purple-500/30' : 'bg-blue-500/10 text-blue-400 border-blue-500/30'"
                >
                  {{ proj.is_government ? 'Government' : 'Private' }}
                </span>
              </td>

              <td class="p-3.5 text-right font-mono font-bold text-slate-200">
                ${{ formatCurrency(proj.budget) }}
              </td>

              <td class="p-3.5 text-right font-mono font-bold text-blue-400">
                ${{ formatCurrency(getMetrics(proj.id)?.totalAllocatedFunds || 0) }}
              </td>

              <td class="p-3.5 min-w-[160px]">
                <div class="flex items-center justify-between text-[11px] font-mono mb-1">
                  <span class="text-slate-400">${{ formatCurrency(getMetrics(proj.id)?.netExpenses || 0) }} spent</span>
                  <span
                    class="font-bold"
                    :class="(getMetrics(proj.id)?.budgetUtilizedPercentage || 0) > 100 ? 'text-rose-400' : 'text-emerald-400'"
                  >
                    {{ getMetrics(proj.id)?.budgetUtilizedPercentage || 0 }}%
                  </span>
                </div>
                <div class="w-full bg-slate-800 rounded-full h-1.5 overflow-hidden">
                  <div
                    class="h-1.5 rounded-full transition-all duration-500"
                    :class="(getMetrics(proj.id)?.budgetUtilizedPercentage || 0) > 100 ? 'bg-rose-500' : 'bg-emerald-500'"
                    :style="{ width: Math.min(getMetrics(proj.id)?.budgetUtilizedPercentage || 0, 100) + '%' }"
                  ></div>
                </div>
              </td>

              <td class="p-3.5 text-center">
                <NuxtLink :to="`/management/projects/${proj.id}`" class="btn-secondary py-1.5 px-3 text-[11px] inline-flex items-center gap-1.5">
                  <span>Manage Ledgers</span>
                  <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                  </svg>
                </NuxtLink>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Create Project Modal (Step 1) -->
    <Modal :isOpen="isCreateModalOpen" title="Step 1: Create New Project Record" @close="isCreateModalOpen = false">
      <form @submit.prevent="handleCreateProject" class="space-y-4 max-h-[75vh] overflow-y-auto pr-1">
        <div>
          <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Project Name *</label>
          <input
            v-model="newProj.name"
            type="text"
            required
            placeholder="e.g., Regional Highway Digital Toll Gate Ledger"
            class="input-field"
          />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Client Name *</label>
            <input
              v-model="newProj.client_name"
              type="text"
              required
              placeholder="e.g., Department of Public Works"
              class="input-field"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Project Budget ($ USD) *</label>
            <input
              v-model.number="newProj.budget"
              type="number"
              step="0.01"
              required
              placeholder="500000.00"
              class="input-field font-mono font-bold text-emerald-400"
            />
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Start Date *</label>
            <input v-model="newProj.start_date" type="date" required class="input-field text-xs" />
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Estimated End Date</label>
            <input v-model="newProj.end_date" type="date" class="input-field text-xs" />
          </div>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Sector & Compliance</label>
          <div class="flex items-center gap-3 p-3 bg-[#0F172A] border border-[#334155] rounded-lg">
            <input id="gov_check" v-model="newProj.is_government" type="checkbox" class="w-4 h-4 rounded text-emerald-500 focus:ring-emerald-500" />
            <label for="gov_check" class="text-xs font-semibold text-slate-200 cursor-pointer">
              Is Government Project (Subject to Public Sector Auditing Compliance)
            </label>
          </div>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Project Scope / Description</label>
          <textarea v-model="newProj.description" rows="2" placeholder="Record purpose and operational scope of the project..." class="input-field"></textarea>
        </div>

        <!-- Address Section (Consuming Address Models) -->
        <div class="pt-3 border-t border-[#1E293B] space-y-3">
          <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider block">Location & Address Models Integration</span>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">City / Municipality (Address Model) *</label>
              <select v-model="newProj.city_id" required class="input-field">
                <option v-for="c in projectStore.cities.value" :key="c.id" :value="c.id">
                  {{ c.name }} ({{ c.province }})
                </option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Barangay *</label>
              <input v-model="newProj.barangay" type="text" required placeholder="e.g. Barangay Central" class="input-field" />
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
              <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Zip Code *</label>
              <input v-model="newProj.zip" type="text" required placeholder="1100" class="input-field font-mono" />
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Street Name</label>
              <input v-model="newProj.street" type="text" placeholder="e.g. Elliptical Road" class="input-field" />
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">House / Bldg #</label>
              <input v-model="newProj.house_number" type="text" placeholder="Bldg 4, Suite 101" class="input-field" />
            </div>
          </div>
        </div>

        <div class="pt-4 flex items-center justify-end gap-3 border-t border-[#1E293B]">
          <button type="button" @click="isCreateModalOpen = false" class="btn-secondary py-2 px-4 text-xs">Cancel</button>
          <button type="submit" class="btn-primary py-2 px-5 text-xs font-bold">Create Project Record</button>
        </div>
      </form>
    </Modal>
  </div>
</template>

<script setup lang="ts">
const projectStore = useProjects()

const isCreateModalOpen = ref(false)
const filterType = ref<'all' | 'gov' | 'private'>('all')
const searchQuery = ref('')

const newProj = reactive({
  name: '',
  description: '',
  budget: 500000.00,
  start_date: new Date().toISOString().split('T')[0],
  end_date: '',
  client_name: '',
  is_government: true,
  city_id: 1,
  house_number: '',
  street: '',
  village: '',
  barangay: 'Barangay Central',
  zip: '1100',
})

const getMetrics = (id: number) => projectStore.getProjectMetrics(id)

const totalBudgetSum = computed(() => {
  return projectStore.projects.value.reduce((sum, p) => sum + Number(p.budget), 0)
})

const totalAllocatedSum = computed(() => {
  return projectStore.projects.value.reduce((sum, p) => sum + (getMetrics(p.id)?.totalAllocatedFunds || 0), 0)
})

const govCount = computed(() => projectStore.projects.value.filter(p => p.is_government).length)
const privateCount = computed(() => projectStore.projects.value.filter(p => !p.is_government).length)

const filteredProjects = computed(() => {
  return projectStore.projects.value.filter(p => {
    if (filterType.value === 'gov' && !p.is_government) return false
    if (filterType.value === 'private' && p.is_government) return false
    if (searchQuery.value) {
      const q = searchQuery.value.toLowerCase()
      const name = p.name.toLowerCase()
      const client = p.client_name.toLowerCase()
      const barangay = (p.barangay || '').toLowerCase()
      return name.includes(q) || client.includes(q) || barangay.includes(q)
    }
    return true
  })
})

onMounted(async () => {
  await projectStore.fetchAll()
})

const handleCreateProject = async () => {
  const created = await projectStore.createProject({ ...newProj })
  isCreateModalOpen.value = false
  // Reset form defaults
  newProj.name = ''
  newProj.client_name = ''
  newProj.description = ''
  if (created && created.id) {
    useRouter().push(`/management/projects/${created.id}`)
  }
}

const formatCurrency = (val: number) => {
  return new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(val)
}
</script>
