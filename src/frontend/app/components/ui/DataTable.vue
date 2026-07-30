<template>
  <div class="w-full space-y-3">
    <!-- Top Toolbar (Search & Filters) -->
    <div v-if="searchable || $slots['header-actions']" class="px-0.5 sm:px-1 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pb-1">
      <!-- Search Input -->
      <div v-if="searchable" class="relative flex-1 min-w-[240px]">
        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-[var(--text-muted)] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <input
          v-model="localSearch"
          type="text"
          :placeholder="searchPlaceholder || 'Search records...'"
          class="w-full pl-9 pr-3.5 py-1.5 rounded-lg bg-[var(--bg-surface)] border border-[var(--border-color)]/70 text-xs text-[var(--text-main)] placeholder-[var(--text-muted)] focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500/50 transition-all"
        />
      </div>

      <!-- Header actions slot (for filters/toggles) -->
      <div v-if="$slots['header-actions']" class="flex flex-wrap items-center gap-2">
        <slot name="header-actions" />
      </div>
    </div>

    <!-- DESKTOP TABLE VIEW (Medium screens and up) -->
    <div :class="responsive ? 'hidden md:block overflow-x-auto rounded-lg border border-[var(--border-color)]/70 bg-[var(--bg-surface)]/30' : 'overflow-x-auto rounded-lg border border-[var(--border-color)]/70 bg-[var(--bg-surface)]/30'">
      <table class="w-full text-left border-collapse min-w-full">
        <thead>
          <tr class="bg-[var(--bg-sidebar)]/50 border-b border-[var(--border-color)] text-xs font-semibold text-[var(--text-muted)] select-none">
            <th
              v-for="col in columns"
              :key="col.key"
              class="py-3.5 px-4 sm:px-5 first:pl-6 sm:first:pl-7 last:pr-6 sm:last:pr-7 whitespace-nowrap"
              :class="[
                col.align === 'right' ? 'text-right' : col.align === 'center' ? 'text-center' : 'text-left',
                col.sortable !== false ? 'cursor-pointer hover:text-emerald-400' : '',
                col.width || ''
              ]"
              @click="col.sortable !== false ? handleSort(col.key) : null"
            >
              <div class="inline-flex items-center gap-1.5" :class="col.align === 'right' ? 'flex-row-reverse' : ''">
                <span>{{ col.label }}</span>
                <span v-if="col.sortable !== false" class="text-xs">
                  <svg v-if="sortKey === col.key && sortOrder === 'asc'" class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7" />
                  </svg>
                  <svg v-else-if="sortKey === col.key && sortOrder === 'desc'" class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                  </svg>
                  <svg v-else class="w-3.5 h-3.5 opacity-30 text-[var(--text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                  </svg>
                </span>
              </div>
            </th>
          </tr>
        </thead>

        <tbody v-if="paginatedItems.length > 0" class="divide-y divide-[var(--border-color)]/60 text-xs sm:text-sm font-medium">
          <tr
            v-for="(item, index) in paginatedItems"
            :key="item.id || index"
            class="hover:bg-[var(--bg-sidebar)]/50 transition-colors duration-150 group cursor-pointer"
            @click="$emit('row-click', item)"
          >
            <td
              v-for="col in columns"
              :key="col.key"
              class="py-3.5 px-4 sm:px-5 first:pl-6 sm:first:pl-7 last:pr-6 sm:last:pr-7 align-middle text-[var(--text-main)]"
              :class="col.align === 'right' ? 'text-right' : col.align === 'center' ? 'text-center' : 'text-left'"
            >
              <slot :name="`cell-${col.key}`" :item="item" :index="index" :value="getItemValue(item, col.key)">
                {{ getItemValue(item, col.key) }}
              </slot>
            </td>
          </tr>
        </tbody>

        <tbody v-else>
          <tr>
            <td :colspan="columns.length" class="py-12 px-4 text-center text-[var(--text-muted)]">
              <slot name="empty">
                <div class="space-y-1">
                  <p class="text-sm font-semibold text-[var(--text-main)]">No records found</p>
                  <p class="text-xs text-[var(--text-muted)]">Try adjusting your search query or filter</p>
                </div>
              </slot>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- MOBILE STACKED VIEW -->
    <div v-if="responsive" class="block md:hidden rounded-lg border border-[var(--border-color)]/70 bg-[var(--bg-surface)]/30 divide-y divide-[var(--border-color)]/60 overflow-hidden">
      <template v-if="paginatedItems.length > 0">
        <div
          v-for="(item, index) in paginatedItems"
          :key="item.id || index"
          class="p-3.5 space-y-2.5 transition-colors hover:bg-[var(--bg-sidebar)]/40 cursor-pointer"
          @click="$emit('row-click', item)"
        >
          <!-- Primary Card Header Row -->
          <div class="flex items-start justify-between gap-3 pb-2 border-b border-[var(--border-color)]/40">
            <div class="flex-1 min-w-0">
              <slot :name="`cell-${primaryColumn.key}`" :item="item" :index="index" :value="getItemValue(item, primaryColumn.key)">
                <div class="font-semibold text-xs sm:text-sm text-[var(--text-main)] truncate">
                  {{ getItemValue(item, primaryColumn.key) }}
                </div>
              </slot>
            </div>

            <!-- Status / Badge column if present -->
            <div v-if="badgeColumn && badgeColumn.key !== primaryColumn.key" class="shrink-0">
              <slot :name="`cell-${badgeColumn.key}`" :item="item" :index="index" :value="getItemValue(item, badgeColumn.key)">
                {{ getItemValue(item, badgeColumn.key) }}
              </slot>
            </div>
          </div>

          <!-- Card Body: Grid of non-primary, non-action fields -->
          <div v-if="bodyColumns.length > 0" class="grid grid-cols-2 gap-2 text-xs">
            <div
              v-for="col in bodyColumns"
              :key="col.key"
              class="flex flex-col justify-between py-1 px-2 rounded bg-[var(--bg-sidebar)]/30"
            >
              <span class="text-[10px] font-medium text-[var(--text-muted)] uppercase tracking-wider mb-0.5 block">
                {{ col.label }}
              </span>
              <div class="text-[var(--text-main)] font-medium truncate">
                <slot :name="`cell-${col.key}`" :item="item" :index="index" :value="getItemValue(item, col.key)">
                  {{ getItemValue(item, col.key) }}
                </slot>
              </div>
            </div>
          </div>

          <!-- Card Footer Actions -->
          <div v-if="actionColumn" class="pt-2 flex items-center justify-end gap-2 border-t border-[var(--border-color)]/40">
            <slot :name="`cell-${actionColumn.key}`" :item="item" :index="index" :value="getItemValue(item, actionColumn.key)" />
          </div>
        </div>
      </template>

      <!-- Empty state for mobile view -->
      <div v-else class="py-10 px-4 text-center text-[var(--text-muted)]">
        <slot name="empty">
          <div class="space-y-1">
            <p class="text-sm font-semibold text-[var(--text-main)]">No records found</p>
            <p class="text-xs text-[var(--text-muted)]">Try adjusting your search query or filter</p>
          </div>
        </slot>
      </div>
    </div>

    <!-- Bottom Pagination Controls -->
    <div v-if="totalItems > 0" class="pt-2 px-0.5 sm:px-1 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-[var(--text-muted)]">
      <!-- Showing count info -->
      <div class="flex flex-wrap items-center gap-3 justify-center sm:justify-start">
        <span class="font-mono text-xs">
          Showing <strong class="text-[var(--text-main)]">{{ totalItems > 0 ? (currentPage - 1) * currentPageSize + 1 : 0 }}</strong>
          to <strong class="text-[var(--text-main)]">{{ Math.min(currentPage * currentPageSize, totalItems) }}</strong>
          of <strong class="text-[var(--text-main)]">{{ totalItems }}</strong> entries
        </span>

        <!-- Page size selector -->
        <div class="flex items-center gap-1.5">
          <span>Rows:</span>
          <select
            v-model="currentPageSize"
            class="rounded-md bg-[var(--bg-surface)] border border-[var(--border-color)] text-[var(--text-main)] text-xs px-2.5 py-1 focus:outline-none focus:ring-1 focus:ring-emerald-500 font-mono"
          >
            <option v-for="size in pageSizeOptions || [5, 10, 25, 50]" :key="size" :value="size">
              {{ size }}
            </option>
          </select>
        </div>
      </div>

      <!-- Pagination Buttons -->
      <div v-if="totalPages > 1" class="flex items-center gap-1.5 select-none">
        <UiButton
          variant="secondary"
          size="sm"
          :disabled="currentPage === 1"
          @click="currentPage--"
        >
          Previous
        </UiButton>

        <UiButton
          v-for="page in visiblePages"
          :key="page"
          :variant="currentPage === page ? 'primary' : 'secondary'"
          size="sm"
          class="font-mono font-bold px-3"
          @click="currentPage = page"
        >
          {{ page }}
        </UiButton>

        <UiButton
          variant="secondary"
          size="sm"
          :disabled="currentPage === totalPages"
          @click="currentPage++"
        >
          Next
        </UiButton>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
export interface DataTableColumn {
  key: string
  label: string
  sortable?: boolean
  align?: 'left' | 'right' | 'center'
  width?: string
}

const props = withDefaults(defineProps<{
  items: any[]
  columns: DataTableColumn[]
  searchable?: boolean
  searchPlaceholder?: string
  searchFields?: string[]
  pageSizeOptions?: number[]
  defaultPageSize?: number
  defaultSortKey?: string
  defaultSortOrder?: 'asc' | 'desc'
  customSortValue?: (item: any, key: string) => any
  responsive?: boolean
}>(), {
  responsive: true,
  searchable: false,
  defaultPageSize: 5
})

defineEmits(['row-click'])

const localSearch = ref('')
const sortKey = ref<string>(props.defaultSortKey || (props.columns[0]?.key || ''))
const sortOrder = ref<'asc' | 'desc'>(props.defaultSortOrder || 'asc')
const currentPage = ref(1)
const currentPageSize = ref(props.defaultPageSize || 5)

// Reset to page 1 when search or sort changes
watch([localSearch, sortKey, sortOrder, currentPageSize], () => {
  currentPage.value = 1
})

const getItemValue = (item: any, key: string) => {
  if (props.customSortValue) {
    const val = props.customSortValue(item, key)
    if (val !== undefined) return val
  }
  return item[key]
}

// Columns computation for mobile card view
const primaryColumn = computed(() => props.columns[0] || { key: '', label: '' })

const badgeColumn = computed(() => {
  return props.columns.find(c =>
    c.key === 'status' || c.key.includes('type') || c.key.includes('code') || c.key.includes('date')
  ) || props.columns[1]
})

const actionColumn = computed(() => {
  return props.columns.find(c => c.key === 'actions' || c.key === 'action')
})

const bodyColumns = computed(() => {
  const pKey = primaryColumn.value.key
  const bKey = badgeColumn.value?.key
  const aKey = actionColumn.value?.key
  return props.columns.filter(c => c.key !== pKey && c.key !== bKey && c.key !== aKey)
})

// Filtered & Sorted Items
const filteredItems = computed(() => {
  let list = [...(props.items || [])]

  // Text search filter
  if (localSearch.value.trim() && props.searchFields && props.searchFields.length > 0) {
    const q = localSearch.value.toLowerCase().trim()
    list = list.filter(item => {
      return props.searchFields!.some(field => {
        const val = String(getItemValue(item, field) ?? '').toLowerCase()
        return val.includes(q)
      })
    })
  }

  // Sort
  if (sortKey.value) {
    list.sort((a, b) => {
      let valA = getItemValue(a, sortKey.value)
      let valB = getItemValue(b, sortKey.value)

      if (valA === undefined || valA === null) valA = ''
      if (valB === undefined || valB === null) valB = ''

      if (typeof valA === 'number' && typeof valB === 'number') {
        return sortOrder.value === 'asc' ? valA - valB : valB - valA
      }

      const strA = String(valA).toLowerCase()
      const strB = String(valB).toLowerCase()

      if (strA < strB) return sortOrder.value === 'asc' ? -1 : 1
      if (strA > strB) return sortOrder.value === 'asc' ? 1 : -1
      return 0
    })
  }

  return list
})

const totalItems = computed(() => filteredItems.value.length)
const totalPages = computed(() => Math.ceil(totalItems.value / currentPageSize.value) || 1)

const paginatedItems = computed(() => {
  const start = (currentPage.value - 1) * currentPageSize.value
  return filteredItems.value.slice(start, start + currentPageSize.value)
})

const visiblePages = computed(() => {
  const pages: number[] = []
  const max = totalPages.value
  let start = Math.max(1, currentPage.value - 2)
  let end = Math.min(max, start + 4)

  if (end - start < 4) {
    start = Math.max(1, end - 4)
  }

  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  return pages
})

const handleSort = (key: string) => {
  if (sortKey.value === key) {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortKey.value = key
    sortOrder.value = 'asc'
  }
}
</script>
