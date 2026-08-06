<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 glass-card p-5 rounded-xl border border-[var(--border-color)]">
      <div>
        <h1 class="text-lg font-bold text-[var(--text-main)] tracking-tight flex items-center gap-2">
          <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          Posted Ledger Entries
        </h1>
        <p class="text-xs text-[var(--text-muted)] mt-0.5">Centralized tabular ledger view across all projects, fund accounts, and general accounts</p>
      </div>

      <div class="flex items-center gap-2.5">
        <UiButton variant="primary" size="sm" @click="isPostJournalModalOpen = true">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
          </svg>
          Post Journal Entry
        </UiButton>
      </div>
    </div>

    <!-- Financial Aggregations Bar -->
    <ClientOnly>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Outflow -->
        <KpiCard
          title="Total Outflow"
          :value="currencyStore.formatCurrency(summaryTotals.debits)"
          subtitle="Cumulative posted outflows"
          type="blue"
        >
          <template #icon>
            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
            </svg>
          </template>
        </KpiCard>

        <!-- 2. Total Inflow -->
        <KpiCard
          title="Total Inflow"
          :value="currencyStore.formatCurrency(summaryTotals.credits)"
          subtitle="Cumulative posted inflows"
          type="amber"
        >
          <template #icon>
            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
          </template>
        </KpiCard>

        <!-- 3. Net Ledger Balance -->
        <KpiCard
          title="Net Ledger Balance"
          :value="currencyStore.formatCurrency(summaryTotals.net)"
          subtitle="Total outflows minus inflows"
          type="emerald"
        >
          <template #icon>
            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </template>
        </KpiCard>

        <!-- 4. Total Accounts Payable Balance -->
        <KpiCard
          title="Accounts Payable"
          :value="currencyStore.formatCurrency(accountsPayableBalance)"
          subtitle="Total unpaid balance"
          type="rose"
        >
          <template #icon>
            <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </template>
        </KpiCard>
      </div>
    </ClientOnly>

    <!-- Dropdown Filters Bar -->
    <div class="glass-card p-4 rounded-xl border border-[var(--border-color)] flex flex-wrap items-center justify-between gap-4">
      <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
        <!-- Project Filter Dropdown -->
        <div class="flex items-center gap-2 min-w-[200px] flex-1 sm:flex-none">
          <label class="text-xs font-semibold text-[var(--text-muted)] whitespace-nowrap">Project:</label>
          <select v-model="selectedProjectId" class="input-field text-xs py-1.5 font-medium">
            <option :value="null">All Projects</option>
            <option v-for="proj in projectsStore.projects.value" :key="proj.id" :value="proj.id">
              {{ proj.name || (proj as any).project_name }}
            </option>
          </select>
        </div>

        <!-- Ledger Account Filter Dropdown -->
        <div class="flex items-center gap-2 min-w-[220px] flex-1 sm:flex-none">
          <label class="text-xs font-semibold text-[var(--text-muted)] whitespace-nowrap">Account:</label>
          <select v-model="selectedAccountId" class="input-field text-xs py-1.5 font-medium">
            <option :value="null">All Ledger Accounts</option>
            <option v-for="acc in accounting.ledgerAccounts.value" :key="acc.id" :value="acc.id">
              {{ acc.account_code }} - {{ acc.account_name }}
            </option>
          </select>
        </div>

        <!-- Year Filter Dropdown -->
        <div class="flex items-center gap-2 min-w-[140px] flex-1 sm:flex-none">
          <label class="text-xs font-semibold text-[var(--text-muted)] whitespace-nowrap">Year:</label>
          <select v-model="selectedYear" class="input-field text-xs py-1.5 font-medium cursor-pointer">
            <option :value="null">All Years</option>
            <option v-for="yr in availableYears" :key="yr" :value="yr">
              {{ yr }}
            </option>
          </select>
        </div>

        <!-- Status Filter Dropdown -->
        <!-- <div class="flex items-center gap-2 min-w-[170px] flex-1 sm:flex-none">
          <label class="text-xs font-semibold text-[var(--text-muted)] whitespace-nowrap">Status:</label>
          <select v-model="selectedStatus" class="input-field text-xs py-1.5 font-medium">
            <option value="posted">Posted Only</option>
            <option value="all">All Statuses</option>
            <option value="reconciled">Reconciled</option>
            <option value="void">Void</option>
          </select>
        </div> -->
      </div>

      <div class="flex items-center gap-2">
        <UiButton
          v-if="selectedProjectId !== null || selectedAccountId !== null || selectedYear !== null || selectedStatus !== 'posted' || filterType !== 'all'"
          variant="ghost"
          size="sm"
          class="text-xs text-rose-400 hover:text-rose-300"
          @click="resetFilters"
        >
          Reset Filters
        </UiButton>
      </div>
    </div>

    <!-- Responsive Data Table -->
    <ClientOnly>
      <UiDataTable
        :items="filteredEntries"
        :columns="ledgerColumns"
        :searchable="true"
        search-placeholder="Search description, item, account, fund, or project..."
        :search-fields="['description', 'search_text']"
        default-sort-key="id"
        default-sort-order="desc"
        :default-page-size="10"
        @row-click="openDetailModal"
      >
        
        <!-- Cell: Date -->
        <template #cell-posting_date="{ item }">
          <span class="text-[var(--text-main)] font-medium">
            {{ item.posting_date || item.created_at?.slice(0, 10) || 'N/A' }}
          </span>
        </template>

        <!-- Cell: Project -->
        <template #cell-project_id="{ item }">
          <span v-if="item.project_id || item.project" class="text-[var(--text-main)] font-medium">
            {{ getProjectName(item) }}
          </span>
          <span class="text-[var(--text-main)] font-medium" v-else>General Ledger</span>
        </template>

        <!-- Cell: Fund Account -->
        <template #cell-fund_account_id="{ value }">
          <span v-if="value" class="text-[var(--text-main)] font-medium">
            {{ getFundCode(value) }}
          </span>
          <span v-else class="text-[var(--text-main)] font-medium">—</span>
        </template>

        <!-- Cell: Ledger Account -->
        <template #cell-ledger_account_id="{ value }">
          <span class="text-[var(--text-main)] font-medium">
            {{ getAccountCode(value) }}
          </span>
        </template>

        <!-- Cell: Account Item -->
        <template #cell-account_item_id="{ value }">
          <span class="text-[var(--text-main)] font-medium">
            {{ getItemName(value) }}
          </span>
        </template>

        <!-- Cell: Debit -->
        <template #cell-debit="{ item }">
          <span v-if="item.transaction_type === 'debit'" class="font-mono font-bold text-blue-400 whitespace-nowrap">
            {{ currencyStore.formatCurrency(item.amount) }}
          </span>
          <span v-else class="text-[var(--text-sub)] flex items-center justify-center">—</span>
        </template>

        <!-- Cell: Credit -->
        <template #cell-credit="{ item }">
          <span v-if="item.transaction_type === 'credit'" class="font-mono font-bold text-amber-400 whitespace-nowrap">
            {{ currencyStore.formatCurrency(item.amount) }}
          </span>
          <span v-else class="text-[var(--text-sub)] flex items-center justify-center">—</span>
        </template>

        <!-- Cell: Payment Status -->
        <template #cell-is_paid="{ item }">
          <div class="flex flex-col items-center gap-0.5">
            <button
              v-if="item.is_paid === false"
              type="button"
              class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider inline-flex items-center gap-1 bg-amber-500/10 text-amber-400 border border-amber-500/20 hover:bg-amber-500/20 hover:border-amber-500/40 transition-all cursor-pointer shadow-sm hover:scale-105"
              title="Click to Settle Payment"
              @click.stop="openMarkPaidModal(item)"
            >
              <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse" />
              Settle Payment
            </button>
            <button
              v-else
              type="button"
              class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider inline-flex items-center gap-1 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500/20 hover:border-emerald-500/40 transition-all cursor-pointer shadow-sm hover:scale-105"
              title="Click to View Payment Details"
              @click.stop="openPaymentDetailsModal(item)"
            >
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-400" />
              Paid Details
            </button>
            <span v-if="item.is_paid === false && (item.accounts_payable?.name || item.accounts_payable_name)" class="text-[10px] text-rose-400 font-medium truncate max-w-[130px]">
              {{ item.accounts_payable?.name || item.accounts_payable_name }}
            </span>
          </div>
        </template>

        <!-- Cell: Status -->
        <!-- <template #cell-status="{ value }">
          <span
            class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider"
            :class="[
              (value || 'posted') === 'posted' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' :
              value === 'reconciled' ? 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20' :
              'bg-slate-500/10 text-slate-400 border border-slate-500/20'
            ]"
          >
            {{ value || 'posted' }}
          </span>
        </template> -->

        <!-- Cell: Description -->
        <template #cell-description="{ value }">
          <span class="text-[var(--text-main)] max-w-xs truncate block">{{ value || 'N/A' }}</span>
        </template>

       
      </UiDataTable>
    </ClientOnly>

    <!-- Entry Detail & Action Modal -->
    <Modal :isOpen="isModalOpen" title="Ledger Entry Details & Actions" @close="isModalOpen = false">
      <div v-if="selectedEntry" class="space-y-5">
        <!-- Top Entry Meta Box -->
        <div class="bg-[var(--bg-surface)] p-4 rounded-xl border border-[var(--border-color)] space-y-3">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <span class="font-mono text-sm font-bold text-emerald-400">Entry #{{ selectedEntry.id }}</span>
              <span
                class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider"
                :class="selectedEntry.transaction_type === 'debit' ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20'"
              >
                {{ selectedEntry.transaction_type }}
              </span>
            </div>

            <div class="text-right font-mono font-bold text-lg" :class="selectedEntry.transaction_type === 'debit' ? 'text-blue-400' : 'text-amber-400'">
              {{ currencyStore.formatCurrency(selectedEntry.amount) }}
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3 text-xs border-t border-[var(--border-color)]/60 pt-3">
            <div>
              <span class="text-[var(--text-muted)] block text-[10px] uppercase font-semibold">Ledger Account</span>
              <span class="font-bold text-[var(--text-main)]">{{ getAccountCode(selectedEntry.ledger_account_id) }}</span>
            </div>

            <div>
              <span class="text-[var(--text-muted)] block text-[10px] uppercase font-semibold">Catalog Item</span>
              <span class="font-medium text-[var(--text-main)]">{{ getItemName(selectedEntry.account_item_id) }}</span>
            </div>

            <div>
              <span class="text-[var(--text-muted)] block text-[10px] uppercase font-semibold">Fund Source</span>
              <span class="font-medium text-[var(--text-main)]">{{ getFundCode(selectedEntry.fund_account_id) }}</span>
            </div>

            <div>
              <span class="text-[var(--text-muted)] block text-[10px] uppercase font-semibold">Project</span>
              <span class="font-medium text-[var(--text-main)]">{{ getProjectName(selectedEntry.project_id) }}</span>
            </div>

            <div>
              <span class="text-[var(--text-muted)] block text-[10px] uppercase font-semibold">Posting Date</span>
              <span class="font-mono text-[var(--text-main)]">{{ selectedEntry.posting_date || selectedEntry.created_at }}</span>
            </div>

            <div v-if="selectedEntry.accounts_payable?.name || selectedEntry.accounts_payable_name">
              <span class="text-[var(--text-muted)] block text-[10px] uppercase font-semibold">Accounts Payable Name</span>
              <span class="font-bold text-rose-400">{{ selectedEntry.accounts_payable?.name || selectedEntry.accounts_payable_name }}</span>
            </div>
          </div>

          <div v-if="selectedEntry.description" class="border-t border-[var(--border-color)]/60 pt-2 text-xs">
            <span class="text-[var(--text-muted)] block text-[10px] uppercase font-semibold">Memo / Description</span>
            <p class="text-[var(--text-main)] italic mt-0.5">{{ selectedEntry.description }}</p>
          </div>
        </div>

        <!-- Itemized Breakdown (if sub-items exist) -->
        <div v-if="selectedEntry.items && selectedEntry.items.length > 0" class="space-y-2">
          <h4 class="text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider">Itemized Line Breakdown</h4>
          <div class="border border-[var(--border-color)] rounded-lg overflow-hidden divide-y divide-[var(--border-color)] text-xs">
            <div
              v-for="(sub, idx) in selectedEntry.items"
              :key="idx"
              class="p-2.5 flex items-center justify-between bg-[var(--bg-surface)]/50"
            >
              <div>
                <div class="font-semibold text-[var(--text-main)]">{{ sub.description }}</div>
                <div class="text-[10px] text-[var(--text-muted)] font-mono">
                  {{ sub.quantity }} {{ sub.unit }} @ {{ currencyStore.formatCurrency(sub.price) }}
                </div>
              </div>
              <div class="font-mono font-bold text-[var(--text-main)]">
                {{ currencyStore.formatCurrency(sub.subtotal) }}
              </div>
            </div>
          </div>
        </div>

        <!-- Interactive Quick Actions -->
        <div class="space-y-3 pt-2 border-t border-[var(--border-color)]">
          <h4 class="text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider">Interactive Entry Actions</h4>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <!-- Payment Toggle -->
            <div class="bg-[var(--bg-surface)] p-3 rounded-lg border border-[var(--border-color)] space-y-2">
              <div class="flex items-center justify-between">
                <div>
                  <span class="text-xs font-semibold text-[var(--text-main)] block">Payment Status</span>
                  <span class="text-[10px] text-[var(--text-muted)] block">Mark entry as Paid or Unpaid</span>
                </div>
                <UiButton
                  size="sm"
                  :variant="selectedEntry.is_paid !== false ? 'secondary' : 'primary'"
                  :class="selectedEntry.is_paid !== false ? '!text-rose-400 border-rose-500/30' : '!bg-emerald-600 hover:!bg-emerald-500 !text-white'"
                  @click="togglePaymentStatus"
                >
                  {{ selectedEntry.is_paid !== false ? 'Mark Unpaid' : 'Settle Payment' }}
                </UiButton>
              </div>

              <!-- Option to set/update AP name if entry is unpaid -->
              <div v-if="selectedEntry.is_paid === false" class="pt-2 border-t border-[var(--border-color)]/60 space-y-1">
                <label class="text-[10px] font-semibold text-[var(--text-muted)] block">Accounts Payable Name (Vendor / Creditor):</label>
                <div class="flex items-center gap-2">
                  <input
                    v-model="editApName"
                    type="text"
                    placeholder="Enter AP Creditor Name..."
                    class="input-field text-xs py-1"
                  />
                  <UiButton size="sm" variant="secondary" @click="saveApName">Save Name</UiButton>
                </div>
              </div>

              <!-- Display Payment Date & Remarks if entry is Paid -->
              <div v-else class="pt-2 border-t border-[var(--border-color)]/60 space-y-1.5 text-[11px]">
                <div class="flex items-center justify-between text-[var(--text-muted)]">
                  <span>Payment Date:</span>
                  <span class="font-mono text-[var(--text-main)] font-semibold">{{ selectedEntry.payment_date || selectedEntry.posting_date || 'N/A' }}</span>
                </div>
                <div v-if="selectedEntry.payment_remarks" class="text-[var(--text-muted)]">
                  <span class="block text-[10px] font-semibold uppercase tracking-wider">Payment Remarks:</span>
                  <p class="text-[var(--text-main)] bg-[var(--bg-app)] p-1.5 rounded border border-[var(--border-color)] mt-0.5 whitespace-pre-wrap font-mono">{{ selectedEntry.payment_remarks }}</p>
                </div>
                <UiButton size="sm" variant="outline" class="w-full mt-1.5 text-[10px]" @click="openPaymentDetailsModal(selectedEntry)">
                  View Full Settlement Details &rarr;
                </UiButton>
              </div>
            </div>

            <!-- Posting Status Selector -->
            <div class="bg-[var(--bg-surface)] p-3 rounded-lg border border-[var(--border-color)] space-y-1">
              <label class="text-xs font-semibold text-[var(--text-main)] block">Posting Status</label>
              <select
                :value="selectedEntry.status || 'posted'"
                class="input-field text-xs py-1 font-medium"
                @change="updateEntryStatus(($event.target as HTMLSelectElement).value)"
              >
                <option value="posted">Posted</option>
                <option value="reconciled">Reconciled</option>
                <option value="void">Void</option>
              </select>
            </div>
          </div>
        </div>

        <div class="pt-3 flex justify-end border-t border-[var(--border-color)]">
          <UiButton variant="secondary" size="sm" @click="isModalOpen = false">Close</UiButton>
        </div>
      </div>
    </Modal>

    <!-- Post New Journal Entry Modal -->
    <Modal :isOpen="isPostJournalModalOpen" title="Post Double-Entry Journal Transaction" @close="isPostJournalModalOpen = false">
      <form @submit.prevent="handleCreateJournalEntry" class="space-y-4">
        <!-- 1. Project Selection -->
        <div>
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Project (Optional)</label>
          <select v-model="newJournalEntry.project_id" class="input-field text-xs py-2 font-medium">
            <option :value="null">General Ledger (No Specific Project)</option>
            <option v-for="proj in projectsStore.projects.value" :key="proj.id" :value="proj.id">
              {{ proj.name || (proj as any).project_name }}
            </option>
          </select>
        </div>

        <!-- 2. Fund Source Account -->
        <div>
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Fund Source Account *</label>
          <select v-model="newJournalEntry.fund_account_id" required class="input-field text-xs py-2 font-medium">
            <option :value="undefined" disabled>-- Select Fund Account --</option>
            <option v-for="fund in accounting.fundAccounts.value" :key="fund.id" :value="fund.id">
              {{ fund.fund_code }} - {{ fund.fund_name }} (Available: {{ currencyStore.formatCurrency(accounting.getFundAccountRemaining(fund.id)) }})
            </option>
          </select>
        </div>

        <!-- 3. Ledger Account -->
        <div>
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Ledger Account *</label>
          <select v-model="newJournalEntry.ledger_account_id" required class="input-field text-xs py-2 font-medium">
            <option :value="undefined" disabled>-- Select Ledger Account --</option>
            <option v-for="acc in accounting.ledgerAccounts.value" :key="acc.id" :value="acc.id">
              {{ acc.account_code }} - {{ acc.account_name }}
            </option>
          </select>
        </div>

        <!-- 4. Ledger Account Item -->
        <div>
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Ledger Account Item *</label>
          <select v-model="newJournalEntry.account_item_id" required class="input-field text-xs py-2 font-medium">
            <option :value="undefined" disabled>-- Select Account Item --</option>
            <option v-for="item in filteredAccountItems" :key="item.id" :value="item.id">
              {{ item.item_code }} - {{ item.item_name }} ({{ item.transaction_type === 'credit' ? 'Inflow' : 'Outflow' }})
            </option>
          </select>
        </div>

        <!-- 5. Amount & 6. Transaction Date -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Amount*</label>
            <input
              v-model.number="newJournalEntry.amount"
              type="number"
              step="0.01"
              required
              placeholder="1000.00"
              class="input-field font-mono font-bold text-base text-emerald-400"
            />
          </div>
          <div>
            <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Transaction Date *</label>
            <input
              v-model="newJournalEntry.posting_date"
              type="date"
              required
              class="input-field font-mono text-xs"
            />
          </div>
        </div>

        <!-- 7. Note / Description -->
        <div>
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Note / Description</label>
          <textarea v-model="newJournalEntry.description" rows="2" placeholder="Record purpose of journal entry..." class="input-field text-xs"></textarea>
        </div>

        <!-- 8. Payment status (is_paid) -->
        <div class="bg-[var(--bg-surface)] p-3 rounded-lg border border-[var(--border-color)] space-y-3">
          <div class="flex items-center justify-between">
            <div>
              <span class="text-xs font-semibold text-[var(--text-main)] block">Payment Status</span>
              <span class="text-[10px] text-[var(--text-muted)] block">
                {{ paymentStatusSubtitle }}
              </span>
            </div>
            <UiButton
              type="button"
              size="sm"
              :variant="newJournalEntry.is_paid ? 'primary' : 'secondary'"
              :class="newJournalEntry.is_paid ? '!bg-emerald-600 hover:!bg-emerald-500' : (newJournalEntry.transaction_type === 'credit' ? '!text-amber-400 border-amber-500/30' : '!text-rose-400 border-rose-500/30')"
              @click="newJournalEntry.is_paid = !newJournalEntry.is_paid"
            >
              {{ paymentStatusButtonLabel }}
            </UiButton>
          </div>

          <!-- Accounts Payable Name input when unpaid -->
          <div v-if="!newJournalEntry.is_paid" class="pt-2 border-t border-[var(--border-color)]/60 space-y-1">
            <label class="block text-xs font-semibold text-rose-400 uppercase tracking-wider">
              Accounts Payable Name (Creditor / Vendor) *
            </label>
            <input
              v-model="newJournalEntry.accounts_payable_name"
              type="text"
              required
              placeholder="e.g. Acme Supplies, Contractor Name..."
              class="input-field text-xs py-2 font-medium border-rose-500/30"
            />
          </div>
        </div>

        <div class="pt-4 flex items-center justify-end gap-3 border-t border-[var(--border-color)]">
          <UiButton type="button" variant="secondary" size="sm" @click="isPostJournalModalOpen = false">Cancel</UiButton>
          <UiButton type="submit" variant="primary" size="sm" :loading="isPostingEntry">Post Transaction to Ledger</UiButton>
        </div>
      </form>
    </Modal>

    <!-- Shared Mark Paid Modal -->
    <MarkPaidModal
      :is-open="isMarkPaidModalOpen"
      :entry="selectedEntryForMarkPaid"
      :fund-accounts="accounting.fundAccounts.value"
      :is-submitting="isMarkPaidSubmitting"
      @close="isMarkPaidModalOpen = false"
      @confirm="handleMarkPaidConfirm"
    />

    <!-- Shared Payment Details Modal -->
    <PaymentDetailsModal
      :is-open="isPaymentDetailsModalOpen"
      :entry="selectedEntryForPaymentDetails"
      :fund-accounts="accounting.fundAccounts.value"
      @close="isPaymentDetailsModalOpen = false"
      @unmark-paid="handleUnmarkPaidFromDetails"
    />
  </div>
</template>

<script setup lang="ts">
import type { DataTableColumn } from '~/components/ui/DataTable.vue'
import type { LedgerAccountItem } from '~/composables/useAccounting'

const currencyStore = useCurrency()
const accounting = useAccounting()
const projectsStore = useProjects()

const filterType = ref<'all' | 'debit' | 'credit'>('all')
const selectedProjectId = ref<number | null>(null)
const selectedAccountId = ref<number | null>(null)
const selectedYear = ref<number | null>(null)
const selectedStatus = ref<'posted' | 'all' | 'reconciled' | 'void'>('posted')

const availableYears = computed<number[]>(() => {
  const yearsSet = new Set<number>()
  const currentYr = new Date().getFullYear()
  yearsSet.add(currentYr)

  accounting.journalEntries.value.forEach((entry) => {
    const rawDate = entry.posting_date || entry.created_at
    if (rawDate) {
      const yr = parseInt(rawDate.slice(0, 4), 10)
      if (!isNaN(yr)) {
        yearsSet.add(yr)
      }
    }
  })

  return Array.from(yearsSet).sort((a, b) => b - a)
})

const isModalOpen = ref(false)
const selectedEntry = ref<any>(null)

const isPostJournalModalOpen = ref(false)
const isPostingEntry = ref(false)

const newJournalEntry = reactive({
  project_id: null as number | null,
  ledger_account_id: undefined as number | undefined,
  fund_account_id: undefined as number | undefined,
  account_item_id: undefined as number | undefined,
  amount: 1000.00,
  transaction_type: 'debit' as 'debit' | 'credit',
  description: '',
  posting_date: new Date().toISOString().slice(0, 10),
  is_paid: true,
  accounts_payable_name: '',
})

const filteredAccountItems = computed(() => {
  if (!newJournalEntry.ledger_account_id) return accounting.accountItems.value
  return accounting.accountItems.value.filter(item => item.ledger_account_id === newJournalEntry.ledger_account_id)
})

const paymentStatusSubtitle = computed(() => {
  const isCredit = newJournalEntry.transaction_type === 'credit'
  if (newJournalEntry.is_paid) {
    return isCredit 
      ? 'Immediate cash inflow to Fund Account' 
      : 'Immediate cash outflow from Fund Account'
  } else {
    return isCredit 
      ? 'Accrued asset / Accounts Receivable (Unpaid)' 
      : 'Accrued liability / Accounts Payable (Unpaid)'
  }
})

const paymentStatusButtonLabel = computed(() => {
  if (newJournalEntry.is_paid) {
    return 'Posted / Paid'
  } else {
    return newJournalEntry.transaction_type === 'credit' ? 'Receivable (Unpaid)' : 'Payable (Unpaid)'
  }
})

watch(() => newJournalEntry.ledger_account_id, (newAccountId) => {
  if (!newAccountId) {
    newJournalEntry.account_item_id = undefined
    return
  }
  if (newJournalEntry.account_item_id) {
    const item = accounting.accountItems.value.find(i => i.id === Number(newJournalEntry.account_item_id))
    if (item && item.ledger_account_id !== newAccountId) {
      newJournalEntry.account_item_id = undefined
    }
  }
})

watch(() => newJournalEntry.account_item_id, (newItemId) => {
  if (!newItemId) return
  const item = accounting.accountItems.value.find(i => i.id === Number(newItemId))
  if (item) {
    if (item.ledger_account_id && newJournalEntry.ledger_account_id !== item.ledger_account_id) {
      newJournalEntry.ledger_account_id = item.ledger_account_id
    }
    if (item.transaction_type) {
      newJournalEntry.transaction_type = item.transaction_type
    }
  }
})

const handleCreateJournalEntry = async () => {
  if (!newJournalEntry.ledger_account_id || !newJournalEntry.fund_account_id || !newJournalEntry.account_item_id) {
    alert('Please select a Fund Source Account, Master Account Item, and Target Ledger Account.')
    return
  }

  if (!newJournalEntry.is_paid && !newJournalEntry.accounts_payable_name) {
    alert('Please enter the Accounts Payable Name (Creditor / Vendor).')
    return
  }

  isPostingEntry.value = true
  try {
    const auth = useAuth()
    await accounting.addJournalEntry({
      ledger_account_id: newJournalEntry.ledger_account_id,
      fund_account_id: newJournalEntry.fund_account_id,
      project_id: newJournalEntry.project_id || undefined,
      account_item_id: newJournalEntry.account_item_id,
      amount: Number(newJournalEntry.amount),
      transaction_type: newJournalEntry.transaction_type,
      description: newJournalEntry.description,
      posting_date: newJournalEntry.posting_date,
      is_paid: newJournalEntry.is_paid,
      accounts_payable_name: newJournalEntry.is_paid ? undefined : newJournalEntry.accounts_payable_name,
      user_id: auth.currentUser.value?.id || 1,
    })
    isPostJournalModalOpen.value = false
    newJournalEntry.description = ''
    newJournalEntry.amount = 1000.00
    newJournalEntry.project_id = null
    newJournalEntry.fund_account_id = undefined
    newJournalEntry.ledger_account_id = undefined
    newJournalEntry.account_item_id = undefined
    newJournalEntry.transaction_type = 'debit'
    newJournalEntry.is_paid = true
    newJournalEntry.accounts_payable_name = ''
  } catch (err: any) {
    console.error('Failed to create journal entry:', err)
    alert(err?.data?.message || err?.message || 'Failed to post journal entry.')
  } finally {
    isPostingEntry.value = false
  }
}

const typeFilterTabs = computed<TabItem[]>(() => [
  { value: 'all', label: 'All Entries', badge: filteredEntries.value.length },
  { value: 'debit', label: 'Outflows' },
  { value: 'credit', label: 'Inflows' },
])

const ledgerColumns: DataTableColumn[] = [
  { key: 'posting_date', label: 'Date', width: 'w-24' },
  { key: 'project_id', label: 'Project', width: 'min-w-[140px]' },
  { key: 'fund_account_id', label: 'Fund Source', width: 'min-w-[130px]' },
  { key: 'ledger_account_id', label: 'Ledger Account', width: 'min-w-[140px]' },
  { key: 'account_item_id', label: 'Catalog Item', width: 'min-w-[160px]' },
  { key: 'transaction_type', label: 'Type', width: 'w-20' },
  { key: 'debit', label: 'Outflow', align: 'right', width: 'min-w-[120px]' },
  { key: 'credit', label: 'Inflow', align: 'right', width: 'min-w-[120px]' },
  { key: 'is_paid', label: 'Payment', align: 'center' },
  { key: 'description', label: 'Description / Memo', width: 'min-w-[180px]' },
]

const getFundCode = (id?: number) => {
  if (!id) return 'N/A'
  const fund = accounting.fundAccounts.value.find(f => f.id === id)
  return fund ? fund.fund_code : `FND-#${id}`
}

const getAccountCode = (id: number) => {
  const acc = accounting.ledgerAccounts.value.find(a => a.id === id)
  return acc ? `${acc.account_code} ${acc.account_name}` : `#${id}`
}

const getItemName = (id: number) => {
  const item = accounting.accountItems.value.find(i => i.id === id)
  return item ? item.item_name : `Item #${id}`
}

const getProjectName = (itemOrId?: any) => {
  if (!itemOrId) return 'General Ledger'
  if (typeof itemOrId === 'object' && itemOrId !== null) {
    if (itemOrId.project?.name) return itemOrId.project.name
    if (itemOrId.project?.project_name) return itemOrId.project.project_name
    if (!itemOrId.project_id) return 'General Ledger'
    const proj = projectsStore.projects.value.find(p => Number(p.id) === Number(itemOrId.project_id))
    return proj ? (proj.name || (proj as any).project_name) : `Project #${itemOrId.project_id}`
  }
  const id = Number(itemOrId)
  if (!id) return 'General Ledger'
  const proj = projectsStore.projects.value.find(p => Number(p.id) === id)
  return proj ? (proj.name || (proj as any).project_name) : `Project #${id}`
}

const filteredEntries = computed(() => {
  return accounting.journalEntries.value.filter(entry => {
    // Transaction type filter
    if (filterType.value !== 'all' && entry.transaction_type !== filterType.value) {
      return false
    }

    // Status filter
    // const currentStatus = entry.status || 'posted'
    // if (selectedStatus.value !== 'all' && currentStatus !== selectedStatus.value) {
    //   return false
    // }

    // Project filter
    if (selectedProjectId.value !== null && entry.project_id !== selectedProjectId.value) {
      return false
    }

    // Ledger Account filter
    if (selectedAccountId.value !== null && entry.ledger_account_id !== selectedAccountId.value) {
      return false
    }

    // Year filter
    if (selectedYear.value !== null) {
      const rawDate = entry.posting_date || entry.created_at
      if (!rawDate) return false
      const entryYr = parseInt(rawDate.slice(0, 4), 10)
      if (entryYr !== Number(selectedYear.value)) {
        return false
      }
    }

    return true
  }).map(entry => ({
    ...entry,
    search_text: `${getProjectName(entry.project_id)} ${getFundCode(entry.fund_account_id)} ${getAccountCode(entry.ledger_account_id)} ${getItemName(entry.account_item_id)} ${entry.accounts_payable?.name || entry.accounts_payable_name || ''}`
  }))
})

const summaryTotals = computed(() => {
  const debits = filteredEntries.value
    .filter(e => e.transaction_type === 'debit')
    .reduce((sum, e) => sum + Number(e.amount), 0)

  const credits = filteredEntries.value
    .filter(e => e.transaction_type === 'credit')
    .reduce((sum, e) => sum + Number(e.amount), 0)

  return {
    debits,
    credits,
    net: debits - credits
  }
})

const accountsPayableBalance = computed(() => {
  return filteredEntries.value
    .filter(e => e.transaction_type === 'debit' && e.is_paid === false)
    .reduce((sum, e) => sum + Number(e.amount), 0)
})

const resetFilters = () => {
  filterType.value = 'all'
  selectedProjectId.value = null
  selectedAccountId.value = null
  selectedYear.value = null
  selectedStatus.value = 'posted'
}

const editApName = ref('')
const isMarkPaidModalOpen = ref(false)
const selectedEntryForMarkPaid = ref<any>(null)
const isMarkPaidSubmitting = ref(false)

const isPaymentDetailsModalOpen = ref(false)
const selectedEntryForPaymentDetails = ref<any>(null)

const openPaymentDetailsModal = (item: any) => {
  selectedEntryForPaymentDetails.value = item
  isPaymentDetailsModalOpen.value = true
}

const handleUnmarkPaidFromDetails = async (entry: any) => {
  if (!entry) return
  if (!confirm('Are you sure you want to mark this entry as Unpaid (Accounts Payable)?')) return
  try {
    await accounting.updateJournalEntryPaymentStatus(entry.id, false)
    entry.is_paid = false
    if (selectedEntry.value && selectedEntry.value.id === entry.id) {
      selectedEntry.value.is_paid = false
    }
    isPaymentDetailsModalOpen.value = false
  } catch (err) {
    console.error('Failed to mark as unpaid:', err)
  }
}

const openMarkPaidModal = (item: any) => {
  selectedEntryForMarkPaid.value = item
  isMarkPaidModalOpen.value = true
}

const handleMarkPaidConfirm = async (payload: any) => {
  if (!selectedEntryForMarkPaid.value) return
  isMarkPaidSubmitting.value = true
  try {
    const res = await accounting.updateJournalEntryPaymentStatus(payload.id, true, {
      payment_date: payload.payment_date,
      payment_remarks: payload.payment_remarks,
      fund_account_id: payload.fund_account_id,
      accounts_payable_name: payload.accounts_payable_name,
    })
    
    if (selectedEntry.value && selectedEntry.value.id === payload.id) {
      selectedEntry.value.is_paid = true
      selectedEntry.value.payment_date = payload.payment_date
      selectedEntry.value.payment_remarks = payload.payment_remarks
      if (payload.fund_account_id) selectedEntry.value.fund_account_id = payload.fund_account_id
    }

    const targetIdx = accounting.journalEntries.value.findIndex(e => e.id === payload.id)
    if (targetIdx !== -1) {
      accounting.journalEntries.value[targetIdx] = {
        ...accounting.journalEntries.value[targetIdx],
        is_paid: true,
        payment_date: payload.payment_date,
        payment_remarks: payload.payment_remarks,
        ...(payload.fund_account_id ? { fund_account_id: payload.fund_account_id } : {}),
      }
    }
    isMarkPaidModalOpen.value = false
  } catch (err: any) {
    console.error('Failed to mark entry as paid:', err)
    alert(err?.data?.message || err?.message || 'Failed to settle payment')
  } finally {
    isMarkPaidSubmitting.value = false
  }
}

const togglePaymentStatus = async () => {
  if (!selectedEntry.value) return
  const currentPaid = selectedEntry.value.is_paid !== false

  if (!currentPaid) {
    openMarkPaidModal(selectedEntry.value)
  } else {
    if (!confirm('Are you sure you want to mark this entry as Unpaid (Accounts Payable)?')) return
    try {
      await accounting.updateJournalEntryPaymentStatus(selectedEntry.value.id, false)
      selectedEntry.value.is_paid = false
    } catch (err) {
      console.error('Failed to mark as unpaid:', err)
    }
  }
}

const saveApName = async () => {
  if (!selectedEntry.value) return
  try {
    const res = await accounting.updateJournalEntryPaymentStatus(selectedEntry.value.id, false, editApName.value)
    if (res?.accounts_payable) {
      selectedEntry.value.accounts_payable = res.accounts_payable
    }
    alert('Accounts Payable Name saved successfully!')
  } catch (err) {
    console.error('Failed to save AP Name:', err)
  }
}

const updateEntryStatus = async (newStatus: string) => {
  if (!selectedEntry.value) return

  try {
    const api = useApi()
    await api.request(`/journal-entries/${selectedEntry.value.id}/status`, {
      method: 'PATCH',
      body: { status: newStatus },
    })

    selectedEntry.value.status = newStatus
    const idx = accounting.journalEntries.value.findIndex(e => e.id === selectedEntry.value.id)
    if (idx !== -1) {
      accounting.journalEntries.value[idx].status = newStatus as any
    }
  } catch (err) {
    console.error('Failed to update entry status:', err)
  }
}

onMounted(async () => {
  try {
    if (projectsStore.projects.value.length === 0) {
      await projectsStore.fetchProjects()
    }
    if (accounting.journalEntries.value.length === 0) {
      await accounting.fetchJournalEntries()
    }
    if (accounting.ledgerAccounts.value.length === 0) {
      await accounting.fetchLedgerAccounts()
    }
    if (accounting.fundAccounts.value.length === 0) {
      await accounting.fetchFundAccounts()
    }
    if (accounting.accountItems.value.length === 0) {
      await accounting.fetchAccountItems()
    }
  } catch (err) {
    console.error('Error fetching ledger page data:', err)
  }
})
</script>
