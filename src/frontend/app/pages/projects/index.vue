<template>
  <div class="space-y-6">
    <!-- Top Greeting & Action Banner -->
    <div class="glass-panel p-6 rounded-2xl border border-[var(--border-color)] relative overflow-hidden flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
      <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

      <div>
        <div class="flex items-center gap-2 text-xs text-emerald-500 font-mono font-semibold uppercase tracking-wider mb-1">
          <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
          Enterprise Projects Register
        </div>
        <h1 class="text-2xl font-bold text-[var(--text-main)] tracking-tight">
          Projects Management
        </h1>
        <p class="text-xs text-[var(--text-muted)] mt-1">
          Track project statuses, location addresses, fund allocations, and budget usage for {{ companyName }}.
        </p>
      </div>

      <div class="flex flex-wrap items-center gap-3 shrink-0">
        <UiButton type="button" variant="primary" @click="isCreateModalOpen = true">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
          </svg>
          Create New Project
        </UiButton>
      </div>
    </div>

    <!-- Projects List Header & Filter Controls -->
    <div class="glass-card p-5 rounded-2xl border border-[var(--border-color)] space-y-4">
      <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 pb-4 border-b border-[var(--border-color)]">
        <div>
          <h2 class="text-lg font-bold text-[var(--text-main)] tracking-tight">Registered Projects Catalog</h2>
          <p class="text-xs text-[var(--text-muted)] mt-0.5">Filter by status, location address, or budget allocations</p>
        </div>

        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
          <!-- Search Field -->
          <div class="relative flex-1 md:w-64">
            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-[var(--text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search projects or location..."
              class="w-full bg-[var(--bg-surface)] border border-[var(--border-color)] text-[var(--text-main)] text-xs rounded-lg pl-9 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
            />
          </div>

          <!-- View Switcher Toggle -->
          <div class="flex items-center bg-[var(--bg-surface)] border border-[var(--border-color)] p-1 rounded-lg">
            <button
              type="button"
              class="p-1.5 rounded text-xs transition-colors cursor-pointer"
              :class="viewMode === 'cards' ? 'bg-emerald-500/20 text-emerald-400 font-semibold' : 'text-[var(--text-muted)] hover:text-[var(--text-main)]'"
              title="Cards View"
              @click="viewMode = 'cards'"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
              </svg>
            </button>
            <button
              type="button"
              class="p-1.5 rounded text-xs transition-colors cursor-pointer"
              :class="viewMode === 'table' ? 'bg-emerald-500/20 text-emerald-400 font-semibold' : 'text-[var(--text-muted)] hover:text-[var(--text-main)]'"
              title="Table View"
              @click="viewMode = 'table'"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Segmented Status Tabs Filter -->
      <UiTabs v-model="statusFilter" :items="statusFilterTabs" variant="segmented" size="sm" />

      <!-- CARDS VIEW MODE -->
      <div v-if="viewMode === 'cards'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 pt-2">
        <div
          v-for="project in filteredProjects"
          :key="project.id"
          class="glass-card rounded-2xl p-5 border border-[var(--border-color)] hover:border-emerald-500/40 transition-all duration-300 flex flex-col justify-between group cursor-pointer relative overflow-hidden"
          @click="handleRowClick(project)"
        >
          <div>
            <!-- Header Status & ID -->
            <div class="flex items-center justify-between mb-3">
              <span class="text-xs font-mono text-[var(--text-muted)] font-semibold">PRJ-#{{ project.id }}</span>
              <UiBadge
                :variant="project.status === 'active' ? 'emerald' : project.status === 'on-hold' ? 'amber' : 'ghost'"
                size="sm"
              >
                {{ project.status }}
              </UiBadge>
            </div>

            <!-- Title & Address -->
            <h3 class="text-base font-bold text-[var(--text-main)] group-hover:text-emerald-400 transition-colors line-clamp-1">
              {{ project.name }}
            </h3>
            <p class="text-xs text-[var(--text-muted)] mt-1 flex items-center gap-1.5 truncate">
              <svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <span>{{ formatAddress(project.address) }}</span>
            </p>
            <p class="text-xs text-[var(--text-muted)] mt-2 line-clamp-2">
              {{ project.description }}
            </p>
          </div>

          <!-- Financial Health Progress -->
          <div class="mt-4 space-y-3">
            
            <div class="grid grid-cols-2 gap-2 pt-1">
              <div class="bg-[var(--bg-surface)] p-2 rounded-lg border border-[var(--border-color)]">
                <span class="text-[10px] text-[var(--text-muted)] uppercase tracking-wider block font-semibold">Total Budget</span>
                <span class="text-xs font-bold font-mono text-[var(--text-main)]">{{ currencyStore.formatCurrency(projectsStore.getProjectBudget(project.id)) }}</span>
              </div>
              <div class="bg-[var(--bg-surface)] p-2 rounded-lg border border-[var(--border-color)]">
                <span class="text-[10px] text-[var(--text-muted)] uppercase tracking-wider block font-semibold">Total Spent</span>
                <span class="text-xs font-bold font-mono text-rose-400">{{ currencyStore.formatCurrency(projectsStore.getProjectTotalSpent(project.id)) }}</span>
              </div>
              <div class="bg-[var(--bg-surface)] p-2 rounded-lg border border-[var(--border-color)]">
                <span class="text-[10px] text-[var(--text-muted)] uppercase tracking-wider block font-semibold">Remaining Balance</span>
                <span class="text-xs font-bold font-mono text-[var(--text-main)]">{{ currencyStore.formatCurrency(projectsStore.getProjectRemainingBalance(project.id)) }}</span>
              </div>
              <div class="bg-[var(--bg-surface)] p-2 rounded-lg border border-[var(--border-color)]">
                <span class="text-[10px] text-[var(--text-muted)] uppercase tracking-wider block font-semibold">Budget Utilization</span>
                <span :class="['text-xs font-bold font-mono', getUsageTextColorClass(projectsStore.getProjectBudgetUtilization(project.id))]">{{ projectsStore.getProjectBudgetUtilization(project.id) }}%</span>
              </div>
            </div>

            <!-- Footer Action Button -->
            <div class="pt-2 flex items-center justify-between text-xs font-semibold text-emerald-500 group-hover:translate-x-1 transition-transform">
              <span>View Ledger &amp; Management</span>
              <span>&rarr;</span>
            </div>
          </div>
        </div>
      </div>

      <!-- TABLE VIEW MODE -->
      <div v-else-if="viewMode === 'table'">
        <UiDataTable
          :items="filteredProjects"
          :columns="projectColumns"
          :searchable="false"
          :custom-sort="getCustomSortValue"
          default-sort-key="id"
          default-sort-order="desc"
          :default-page-size="10"
          @row-click="handleRowClick"
        >
          <!-- Cell: Project & Location -->
          <template #cell-name="{ item }">
            <div>
              <div class="font-bold text-[var(--text-main)] hover:text-emerald-400 transition-colors">{{ item.name }}</div>
              <div class="text-xs text-[var(--text-muted)] flex items-center gap-1 mt-0.5">
                <span>{{ formatAddress(item.address) }}</span>
              </div>
            </div>
          </template>

          <!-- Cell: Client Name -->
          <template #cell-client_name="{ value }">
            <span class="text-[var(--text-main)] font-medium">{{ value }}</span>
          </template>

          <!-- Cell: Start Date -->
          <template #cell-start_date="{ value }">
            <span class="font-mono text-[var(--text-muted)] text-xs">{{ dateStore.formatISODate(value) }}</span>
          </template>

          <!-- Cell: Status -->
          <template #cell-status="{ value }">
            <span class="font-mono text-[var(--text-muted)] text-xs">{{ value?.toUpperCase() }}</span>
          </template>

          <!-- Cell: Budget -->
          <template #cell-budget="{ value }">
            <span class="font-mono text-[var(--text-muted)]">{{ currencyStore.formatCurrency(value || 0) }}</span>
          </template>

          <!-- Cell: Total Spent -->
          <template #cell-spent="{ item }">
            <span class="font-mono font-semibold text-rose-400">
              {{ currencyStore.formatCurrency(projectsStore.getProjectTotalSpent(item.id)) }}
            </span>
          </template>

          <!-- Cell: Remaining -->
          <template #cell-remaining="{ item }">
            <span class="font-mono font-semibold text-blue-400">
              {{ currencyStore.formatCurrency(projectsStore.getProjectBudgetBalance(item.id)) }}
            </span>
          </template>

          <!-- Cell: Usage Progress Bar -->
          <template #cell-usage="{ item }">
            <span
              class="font-mono font-semibold"
              :class="getUsageTextColorClass(projectsStore.getProjectBudgetUtilization(item.id))"
            >
              {{ projectsStore.getProjectBudgetUtilization(item.id) }}%
            </span>
          </template>

          <!-- Cell: Actions -->
          <template #cell-actions="{ item }">
            <UiButton
              variant="ghost"
              size="sm"
              class="!p-1 text-emerald-500 hover:text-emerald-400"
              title="Open Project Dashboard"
              @click.stop="handleRowClick(item)"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </UiButton>
          </template>
        </UiDataTable>
      </div>
    </div>

    <!-- Create Project Modal with Cascading Dropdowns -->
    <ProjectFormModal
      :isOpen="isCreateModalOpen"
      @close="isCreateModalOpen = false"
      @created="handleProjectCreated"
    />
  </div>
</template>

<script setup lang="ts">
import type { DataTableColumn } from '~/components/ui/DataTable.vue'
import type { TabItem } from '~/components/ui/Tabs.vue'

const router = useRouter()
const auth = useAuth()
const projectsStore = useProjects()
const currencyStore = useCurrency()
const dateStore = useDate()

const statusFilter = ref<'all' | 'active' | 'on-hold' | 'completed'>('all')
const searchQuery = ref('')
const viewMode = ref<'cards' | 'table'>('cards')
const isCreateModalOpen = ref(false)

const companyName = computed(() => auth.currentCompany.value?.business_name || 'Apex Technologies')

const statusFilterTabs = computed<TabItem[]>(() => [
  { value: 'all', label: 'All', badge: projectsStore.projects.value.length },
  { value: 'active', label: 'Active', badge: projectsStore.projects.value.filter(p => p.status === 'active').length },
  { value: 'on-hold', label: 'On-Hold', badge: projectsStore.projects.value.filter(p => p.status === 'on-hold').length },
  { value: 'completed', label: 'Completed', badge: projectsStore.projects.value.filter(p => p.status === 'completed').length },
])

const projectColumns: DataTableColumn[] = [
  
  { key: 'status', label: 'Status', sortable: true, align: 'center' },
  { key: 'name', label: 'Project & Location', sortable: true },
  { key: 'client_name', label: 'Client / Owner', sortable: true },
  { key: 'start_date', label: 'Start Date', sortable: true },
  { key: 'budget', label: 'Max Budget', sortable: true, align: 'right' },
  { key: 'spent', label: 'Total Spent', sortable: true, align: 'right' },
  { key: 'remaining', label: 'Remaining', sortable: true, align: 'right' },
  { key: 'usage', label: 'Usage', sortable: true, align: 'right' },
  { key: 'actions', label: 'Action', sortable: false, align: 'right' },
]

const filteredProjects = computed(() => {
  const query = searchQuery.value.trim().toLowerCase()
  return projectsStore.projects.value.filter(p => {
    if (statusFilter.value !== 'all' && p.status !== statusFilter.value) {
      return false
    }
    if (!query) return true
    const addrStr = formatAddress(p.address).toLowerCase()
    return p.name.toLowerCase().includes(query) ||
           p.client_name.toLowerCase().includes(query) ||
           (p.description && p.description.toLowerCase().includes(query)) ||
           addrStr.includes(query)
  })
})

const getCustomSortValue = (item: any, key: string) => {
  switch (key) {
    case 'budget': return item.budget || 0
    case 'spent': return projectsStore.getProjectTotalSpent(item.id)
    case 'remaining': return projectsStore.getProjectBudgetBalance(item.id)
    case 'usage': return getProjectUsagePercent(item.id)
    default: return undefined
  }
}

const getProjectUsagePercent = (projectId: number) => {
  const proj = projectsStore.projects.value.find(p => p.id === projectId)
  const baseLimit = (proj && proj.budget && proj.budget > 0) ? proj.budget : projectsStore.getProjectTotalFunds(projectId)
  if (baseLimit <= 0) return 0
  const spent = projectsStore.getProjectTotalSpent(projectId)
  return Math.min(Math.round((spent / baseLimit) * 100), 100)
}

const getUsageTextColorClass = (val: number) => {
  if (val >= 90) return 'text-rose-400'
  if (val >= 75) return 'text-amber-400'
  return 'text-emerald-400'
}

const formatAddress = (address: any) => {
  if (!address) return 'N/A'
  if (typeof address === 'string') return address
  return `${address.street}, ${address.city} ${address.zip_code ? `(${address.zip_code})` : ''}`
}

const handleRowClick = (item: any) => {
  router.push(`/project/${item.id}`)
}

const handleProjectCreated = (created: any) => {
  if (created?.id) {
    router.push(`/project/${created.id}`)
  }
}
</script>
