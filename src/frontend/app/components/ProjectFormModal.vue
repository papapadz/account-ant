<template>
  <Modal :isOpen="isOpen" title="Create New Project" @close="$emit('close')">
    <form @submit.prevent="handleSubmit" class="space-y-4">
      <div>
        <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Project Name *</label>
        <input
          v-model="form.name"
          type="text"
          required
          placeholder="Commercial Tower Alpha - Phase 2"
          class="w-full rounded-lg bg-[var(--bg-surface)] border border-[var(--border-color)] text-[var(--text-main)] text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
        />
      </div>

      <div>
        <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Client / Contracting Party *</label>
        <input
          v-model="form.client_name"
          type="text"
          required
          placeholder="Apex Global Infrastructure Ltd."
          class="w-full rounded-lg bg-[var(--bg-surface)] border border-[var(--border-color)] text-[var(--text-main)] text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
        />
      </div>

      <div>
        <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Project Description *</label>
        <textarea
          v-model="form.description"
          rows="3"
          required
          placeholder="Scope of works, engineering specifications, and fund allocations..."
          class="w-full rounded-lg bg-[var(--bg-surface)] border border-[var(--border-color)] text-[var(--text-main)] text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
        ></textarea>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Max Budget Cap</label>
          <input
            v-model.number="form.budget"
            type="number"
            step="1000"
            placeholder="500000"
            class="w-full rounded-lg bg-[var(--bg-surface)] border border-[var(--border-color)] text-[var(--text-main)] text-sm px-3 py-2 font-mono focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
          />
        </div>
        <div>
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Start Date *</label>
          <input
            v-model="form.start_date"
            type="date"
            required
            class="w-full rounded-lg bg-[var(--bg-surface)] border border-[var(--border-color)] text-[var(--text-main)] text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
          />
        </div>
      </div>

      <!-- Location / Address Dropdowns -->
      <div class="space-y-3 pt-2 border-t border-[var(--border-color)]">
        <div class="flex items-center justify-between">
          <label class="block text-xs font-bold text-emerald-500 uppercase tracking-wider">Site Location &amp; Address *</label>
          <span class="text-[10px] text-[var(--text-muted)] font-mono">Backend Model Aligned</span>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <!-- State Selection Dropdown -->
          <div>
            <label class="block text-[11px] font-medium text-[var(--text-muted)] mb-1">State / Region *</label>
            <select
              v-model="form.state_id"
              required
              class="w-full rounded-lg bg-[var(--bg-surface)] border border-[var(--border-color)] text-[var(--text-main)] text-xs px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 cursor-pointer"
              @change="handleStateChange"
            >
              <option :value="null" disabled>Select State / Region</option>
              <option
                v-for="st in projectsStore.states.value"
                :key="st.id"
                :value="st.id"
              >
                {{ st.name }}
              </option>
            </select>
          </div>

          <!-- City Selection Dropdown -->
          <div>
            <label class="block text-[11px] font-medium text-[var(--text-muted)] mb-1">City / Municipality *</label>
            <select
              v-model="form.city_id"
              required
              :disabled="!availableCities.length"
              class="w-full rounded-lg bg-[var(--bg-surface)] border border-[var(--border-color)] text-[var(--text-main)] text-xs px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 cursor-pointer disabled:opacity-50"
            >
              <option :value="null" disabled>Select City</option>
              <option
                v-for="ct in availableCities"
                :key="ct.id"
                :value="ct.id"
              >
                {{ ct.name }}
              </option>
            </select>
          </div>
        </div>

        <!-- Street & Zip Code Inputs -->
        <div>
          <label class="block text-[11px] font-medium text-[var(--text-muted)] mb-1">Street Address *</label>
          <input
            v-model="form.street"
            type="text"
            required
            placeholder="104 Financial Way, Block 5"
            class="w-full rounded-lg bg-[var(--bg-surface)] border border-[var(--border-color)] text-[var(--text-main)] text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
          />
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-[11px] font-medium text-[var(--text-muted)] mb-1">Barangay / District</label>
            <input
              v-model="form.barangay"
              type="text"
              placeholder="Brgy. Central"
              class="w-full rounded-lg bg-[var(--bg-surface)] border border-[var(--border-color)] text-[var(--text-main)] text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
            />
          </div>
          <div>
            <label class="block text-[11px] font-medium text-[var(--text-muted)] mb-1">Zip Code</label>
            <input
              v-model="form.zip_code"
              type="text"
              placeholder="1000"
              class="w-full rounded-lg bg-[var(--bg-surface)] border border-[var(--border-color)] text-[var(--text-main)] text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
            />
          </div>
        </div>
      </div>

      <div>
        <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Status</label>
        <select
          v-model="form.status"
          class="w-full rounded-lg bg-[var(--bg-surface)] border border-[var(--border-color)] text-[var(--text-main)] text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
        >
          <option value="active">Active</option>
          <option value="on-hold">On-Hold</option>
          <option value="completed">Completed</option>
        </select>
      </div>

      <div class="pt-4 border-t border-[var(--border-color)] flex justify-end gap-3">
        <UiButton type="button" variant="secondary" @click="$emit('close')">
          Cancel
        </UiButton>
        <UiButton type="submit" variant="primary" :disabled="isSubmitting">
          {{ isSubmitting ? 'Saving...' : 'Save Project Details' }}
        </UiButton>
      </div>
    </form>
  </Modal>
</template>

<script setup lang="ts">
const props = defineProps<{
  isOpen: boolean
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'created', project: any): void
}>()

const projectsStore = useProjects()
const isSubmitting = ref(false)

const form = reactive({
  name: '',
  client_name: '',
  description: '',
  budget: 500000.00,
  start_date: new Date().toISOString().split('T')[0],
  status: 'active' as 'active' | 'on-hold' | 'completed',
  state_id: null as number | null,
  city_id: null as number | null,
  street: '',
  barangay: '',
  zip_code: '',
})

onMounted(async () => {
  if (projectsStore.cities.value.length === 0) {
    await projectsStore.fetchCities()
  }
  initDefaults()
})

watch(() => props.isOpen, (newVal) => {
  if (newVal) {
    initDefaults()
  }
})

watch([() => projectsStore.states.value, () => projectsStore.cities.value], () => {
  initDefaults()
}, { deep: true })

const initDefaults = () => {
  if (projectsStore.states.value.length > 0 && !form.state_id) {
    form.state_id = projectsStore.states.value[0].id
  }
  if (availableCities.value.length > 0 && !form.city_id) {
    form.city_id = availableCities.value[0].id
  }
}

const availableCities = computed(() => {
  if (!form.state_id) return projectsStore.cities.value
  return projectsStore.cities.value.filter(c => c.state_id === form.state_id)
})

const handleStateChange = () => {
  const cities = availableCities.value
  if (cities.length > 0) {
    form.city_id = cities[0].id
  } else {
    form.city_id = null
  }
}

const handleSubmit = async () => {
  if (!form.name || !form.client_name || !form.description || !form.street) return
  isSubmitting.value = true

  try {
    const selectedCity = projectsStore.cities.value.find(c => c.id === form.city_id)
    const created = await projectsStore.addProject({
      name: form.name,
      description: form.description,
      budget: form.budget || 0,
      client_name: form.client_name,
      start_date: form.start_date,
      status: form.status,
      city_id: form.city_id || 1,
      state_id: form.state_id || 1,
      address: {
        street: form.street,
        city: selectedCity?.name || 'Central',
        zip_code: form.zip_code || '1000',
        barangay: form.barangay || 'Central',
        city_id: form.city_id || 1,
        state_id: form.state_id || 1,
      }
    })

    // Reset Form
    form.name = ''
    form.client_name = ''
    form.description = ''
    form.street = ''
    form.barangay = ''
    form.zip_code = ''
    form.status = 'active'
    form.budget = 500000.00
    form.start_date = new Date().toISOString().split('T')[0]

    emit('created', created)
    emit('close')
  } catch (err) {
    console.error('Failed to create project:', err)
  } finally {
    isSubmitting.value = false
  }
}
</script>
