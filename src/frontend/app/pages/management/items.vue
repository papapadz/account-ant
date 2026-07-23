<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 glass-card p-5 rounded-xl border border-[var(--border-color)]">
      <div>
        <h1 class="text-lg font-bold text-[var(--text-main)] tracking-tight flex items-center gap-2">
          <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 11h.01M7 15h.01M11 7h7M11 11h7M11 15h7M4 5h16a1 1 0 011 1v12a1 1 0 01-1 1H4a1 1 0 01-1-1V6a1 1 0 011-1z" />
          </svg>
          Master Account Items Catalog
        </h1>
        <p class="text-xs text-[var(--text-muted)] mt-0.5">Standardized catalog of line items (`AccountItem` model) used in journal entries</p>
      </div>

      <button @click="isModalOpen = true" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
        </svg>
        <span>+ Add Account Item</span>
      </button>
    </div>

    <!-- Catalog Grid -->
    <ClientOnly>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div
          v-for="item in accounting.accountItems.value"
          :key="item.id"
          class="glass-card p-5 rounded-xl border border-[var(--border-color)] relative overflow-hidden flex flex-col justify-between"
        >
          <div>
            <div class="flex items-center justify-between mb-2">
              <span class="px-2 py-0.5 rounded bg-amber-500/10 text-amber-500 border border-amber-500/20 font-mono text-xs font-semibold">
                {{ item.item_code }}
              </span>
              <span class="text-[10px] text-[var(--text-muted)] font-mono">ID: #{{ item.id }}</span>
            </div>

            <h3 class="text-sm font-bold text-[var(--text-main)] mt-1">
              {{ item.item_name }}
            </h3>
            <p class="text-xs text-[var(--text-muted)] mt-1.5 leading-relaxed">
              {{ item.description || 'Standard accounting item.' }}
            </p>
          </div>

          <div class="pt-3 mt-4 border-t border-[var(--border-color)] flex items-center justify-between text-[11px] text-[var(--text-muted)]">
            <span>Catalog Item</span>
            <span class="text-emerald-500 font-semibold">Ready for Posting</span>
          </div>
        </div>
      </div>
    </ClientOnly>

    <!-- Create Modal -->
    <Modal :isOpen="isModalOpen" title="Create Account Catalog Item" @close="isModalOpen = false">
      <form @submit.prevent="handleCreateItem" class="space-y-4">
        <div>
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Item Code *</label>
          <input v-model="newItem.item_code" type="text" required placeholder="ITEM-SRV-06" class="input-field font-mono" />
        </div>

        <div>
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Item Name *</label>
          <input v-model="newItem.item_name" type="text" required placeholder="API Data Stream Processing Service" class="input-field" />
        </div>

        <div>
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Description</label>
          <textarea v-model="newItem.description" rows="3" placeholder="Detailed description of the transaction line item..." class="input-field"></textarea>
        </div>

        <div class="pt-4 flex items-center justify-end gap-3 border-t border-[var(--border-color)]">
          <button type="button" @click="isModalOpen = false" class="btn-secondary py-2 px-4 text-xs">Cancel</button>
          <button type="submit" class="btn-primary py-2 px-5 text-xs font-bold">Save Account Item</button>
        </div>
      </form>
    </Modal>
  </div>
</template>

<script setup lang="ts">
const accounting = useAccounting()
const isModalOpen = ref(false)

const newItem = reactive({
  item_code: '',
  item_name: '',
  description: '',
})

const handleCreateItem = () => {
  accounting.addAccountItem({ ...newItem })
  isModalOpen.value = false
  newItem.item_code = ''
  newItem.item_name = ''
  newItem.description = ''
}
</script>
