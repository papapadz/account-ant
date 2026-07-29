<template>
  <div v-if="project" class="space-y-6">
    <!-- Back Link & Header -->
    <div class="space-y-3">
      <NuxtLink
        to="/"
        class="inline-flex items-center gap-1 text-xs font-semibold text-[var(--text-muted)] hover:text-emerald-400 transition-colors cursor-pointer"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Back to Projects Dashboard
      </NuxtLink>

      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-[var(--bg-surface)] p-5 rounded-xl border border-[var(--border-color)] shadow-sm">
        <div>
          <div class="flex items-center gap-2.5">
            <h1 class="text-2xl font-bold text-[var(--text-main)] tracking-tight">{{ project.name }}</h1>
            <UiBadge :status="project.status" />
          </div>
          <p v-if="project.description" class="text-xs text-[var(--text-main)] mt-1 font-medium">
            {{ project.description }}
          </p>
          <p class="text-xs text-[var(--text-muted)] mt-1.5 flex flex-wrap items-center gap-3">
            <span class="flex items-center gap-1">
              <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
              </svg>
              Address: <strong class="text-[var(--text-main)] font-semibold">{{ formatAddress(project.address) }}</strong>
            </span>
            <span>•</span>
            <span>Client: <strong class="text-[var(--text-main)] font-semibold">{{ project.client_name }}</strong></span>
            <span>•</span>
            <span class="font-mono">Started {{ project.start_date }}</span>
          </p>
        </div>

        <!-- Right action menu: Post Journal Entry, Add Fund Source & Print Balance Sheet -->
        <div class="flex flex-wrap items-center gap-2 shrink-0">
          <UiButton type="button" variant="primary" size="sm" @click="isPostJournalModalOpen = true">
            <template #icon-left>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
              </svg>
            </template>
            Post Journal Entry
          </UiButton>

          <UiButton type="button" variant="outline" size="sm" @click="isAddFundModalOpen = true">
            <template #icon-left>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
            </template>
            Add Fund Source
          </UiButton>

          <UiButton type="button" variant="outline" size="sm" @click="isPrintBalanceSheetModalOpen = true">
            <template #icon-left>
              <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
              </svg>
            </template>
            Print Accounting Ledger
          </UiButton>
        </div>
      </div>
    </div>

    <!-- Core 4 Financial KPI Cards Grid -->
    <ClientOnly>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Net Ledger Balance -->
        <KpiCard
          title="Net Ledger Balance"
          :value="`${currencyStore.formatCurrency(netLedgerBalance)}`"
          subtitle="Total Credits minus Total Debits"
          :type="netLedgerBalance >= 0 ? 'emerald' : 'rose'"
        >
          <template #icon>
            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </template>
        </KpiCard>

        <!-- 2. Total Debits (Dr) -->
        <KpiCard
          title="Total Debits (Dr)"
          :value="`${currencyStore.formatCurrency(totalProjectDebits)}`"
          subtitle="Cumulative debited expenses"
          type="blue"
        >
          <template #icon>
            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
          </template>
        </KpiCard>

        <!-- 3. Total Credits (Cr) -->
        <KpiCard
          title="Total Credits (Cr)"
          :value="`${currencyStore.formatCurrency(totalProjectCredits)}`"
          subtitle="Cumulative credited additions"
          type="amber"
        >
          <template #icon>
            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
            </svg>
          </template>
        </KpiCard>

        <!-- 4. Active Fund Accounts Balance -->
        <KpiCard
          title="Active Fund Accounts Balance"
          :value="`${currencyStore.formatCurrency(activeFundAccountsBalance)}`"
          :subtitle="isOverBudget ? '⚠️ Over Budget Warning' : 'Available fund liquidity'"
          :type="isOverBudget ? 'rose' : 'emerald'"
        >
          <template #icon>
            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
          </template>
        </KpiCard>
      </div>
    </ClientOnly>

    <!-- Main Section: Journal Transactions & Fund Sources Tabs -->
    <UiTabs v-model="activeTab" :items="projectTabs" variant="underline" />

    <!-- TAB 1: Recent Journal Transactions DATATABLE -->
    <div v-if="activeTab === 'transactions'" class="space-y-4">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-base font-bold text-[var(--text-main)]">Recent Journal Transactions</h2>
          <p class="text-xs text-[var(--text-muted)]">Real-time debit and credit line items recorded for {{ project.name }}</p>
        </div>

        <!-- <UiButton type="button" variant="primary" size="sm" @click="isPostJournalModalOpen = true">
          <template #icon-left>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
          </template>
          Post Journal Entry
        </UiButton> -->
      </div>

      <UiDataTable
        :items="projectTransactions"
        :columns="transactionColumns"
        :searchable="true"
        search-placeholder="Search note, ledger account, or item name..."
        :search-fields="['note', 'date']"
        default-sort-key="date"
        default-sort-order="desc"
        :default-page-size="5"
        :custom-sort-value="getTransactionSortValue"
      >
        <!-- Cell: Date -->
        <template #cell-date="{ value }">
          <span class="font-mono text-[var(--text-muted)] text-xs whitespace-nowrap">{{ value }}</span>
        </template>

        <!-- Cell: Type -->
        <template #cell-type="{ value }">
          <span
            class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border select-none"
            :class="value === 'debit' ? 'bg-blue-500/10 text-blue-400 border-blue-500/20' : 'bg-amber-500/10 text-amber-400 border-amber-500/20'"
          >
            {{ value }}
          </span>
        </template>

        <!-- Cell: Fund Source -->
        <template #cell-fund="{ item }">
          <span class="font-medium text-[var(--text-main)] text-xs whitespace-nowrap">
            {{ getFundSourceName(item.fund_source_id) }}
          </span>
        </template>

        <!-- Cell: Category / Account Item -->
        <template #cell-category="{ item }">
          <span class="font-bold text-[var(--text-main)] text-xs whitespace-nowrap">
            {{ getCategoryName(item.category_id) }}
          </span>
        </template>

        <!-- Cell: Note / Description -->
        <template #cell-note="{ value }">
          <span class="text-[var(--text-muted)] text-xs max-w-xs truncate block">{{ value || '—' }}</span>
        </template>

        <!-- Cell: Amount -->
        <template #cell-amount="{ item }">
          <span
            class="font-mono font-bold text-xs whitespace-nowrap"
            :class="item.type === 'debit' ? 'text-blue-400' : 'text-amber-400'"
          >
            {{ item.type === 'debit' ? '-' : '+' }}{{ currencyStore.formatCurrency(item.amount) }}
          </span>
        </template>

        <template #empty>
          <div class="space-y-1 py-4">
            <p class="text-sm font-semibold text-[var(--text-main)]">No journal entries recorded yet</p>
            <p class="text-xs text-[var(--text-muted)]">Click "Post Journal Entry" to record debits or credits for this project</p>
          </div>
        </template>
      </UiDataTable>
    </div>

    <!-- TAB 2: Fund Sources DATATABLE -->
    <div v-if="activeTab === 'funds'" class="space-y-4">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-base font-bold text-[var(--text-main)]">Project Fund Sources</h2>
          <p class="text-xs text-[var(--text-muted)]">Deposits, allocations, and draws backing {{ project.name }}</p>
        </div>

        <!-- <UiButton type="button" variant="primary" size="sm" @click="isAddFundModalOpen = true">
          <template #icon-left>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
          </template>
          Add Fund Source
        </UiButton> -->
      </div>

      <UiDataTable
        :items="projectFundSources"
        :columns="fundSourceColumns"
        :searchable="true"
        search-placeholder="Filter fund sources..."
        :search-fields="['name', 'date_received']"
        default-sort-key="name"
        :default-page-size="5"
        :custom-sort-value="getFundSourceSortValue"
      >
        <!-- Cell: Fund Name -->
        <template #cell-name="{ item }">
          <div class="font-bold text-xs text-[var(--text-main)]">{{ item.name }}</div>
          <div class="text-[10px] font-mono text-emerald-400 mt-0.5">ID #{{ item.id }}</div>
        </template>

        <!-- Cell: Date Received -->
        <template #cell-date_received="{ value }">
          <span class="font-mono text-[var(--text-muted)] text-xs whitespace-nowrap">{{ value }}</span>
        </template>

        <!-- Cell: Initial Allocation -->
        <template #cell-amount="{ value }">
          <span class="font-mono font-bold text-emerald-400 text-xs whitespace-nowrap">{{ currencyStore.formatCurrency(value) }}</span>
        </template>

        <!-- Cell: Amount Spent -->
        <template #cell-spent="{ item }">
          <span class="font-mono font-bold text-rose-400 text-xs whitespace-nowrap">
            {{ currencyStore.formatCurrency(projectsStore.getFundSourceSpent(item.id)) }}
          </span>
        </template>

        <!-- Cell: Remaining -->
        <template #cell-remaining="{ item }">
          <span class="font-mono font-bold text-xs whitespace-nowrap" :class="projectsStore.getFundSourceRemaining(item.id) < 0 ? 'text-rose-400 font-extrabold' : 'text-blue-400'">
            {{ currencyStore.formatCurrency(projectsStore.getFundSourceRemaining(item.id)) }}
          </span>
        </template>

        <!-- Cell: Utilization -->
        <template #cell-usage="{ item }">
          <UiProgressBar
            :percentage="projectsStore.getFundSourceUsagePercentage(item.id)"
            :show-label="true"
          />
        </template>
      </UiDataTable>
    </div>

    <!-- MODAL: Post Journal Entry -->
    <Modal
      :is-open="isPostJournalModalOpen"
      title="Post Journal Entry"
      @close="isPostJournalModalOpen = false"
    >
      <form @submit.prevent="handlePostJournalEntry" class="space-y-4">
        <!-- 1. Select Fund Source -->
        <div class="space-y-1.5">
          <label class="block text-xs font-medium text-[var(--text-muted)] uppercase tracking-wider">
            Fund Source <span class="text-rose-400">*</span>
          </label>
          <select
            v-model="journalForm.fund_source_id"
            required
            class="w-full rounded-lg bg-[var(--bg-surface)] border border-[var(--border-color)] text-[var(--text-main)] text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
          >
            <option value="" disabled>-- Select Fund Source --</option>
            <option
              v-for="fund in projectFundSources"
              :key="fund.id"
              :value="fund.id"
            >
              {{ fund.name }} (Available: {{ currencyStore.formatCurrency(projectsStore.getFundSourceRemaining(fund.id)) }})
            </option>
          </select>
        </div>

        <!-- 2. Ledger Account -->
        <div class="space-y-1.5">
          <label class="block text-xs font-medium text-[var(--text-muted)] uppercase tracking-wider">
            Ledger Account <span class="text-rose-400">*</span>
          </label>
          <select
            v-model="journalForm.ledger_account_id"
            required
            class="w-full rounded-lg bg-[var(--bg-surface)] border border-[var(--border-color)] text-[var(--text-main)] text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
          >
            <option value="" disabled>-- Select Ledger Account --</option>
            <option
              v-for="acc in accountingStore.ledgerAccounts.value"
              :key="acc.id"
              :value="acc.id"
            >
              [{{ acc.account_code }}] {{ acc.account_name }}
            </option>
          </select>
        </div>

        <!-- 3. Account Item Name / Line Category -->
        <div class="space-y-1.5">
          <div class="flex items-center justify-between">
            <label class="block text-xs font-medium text-[var(--text-muted)] uppercase tracking-wider">
              Account Item Name / Line Category <span class="text-rose-400">*</span>
            </label>
            <span
              v-if="journalForm.type"
              class="px-2 py-0.5 rounded text-[10px] font-bold uppercase border select-none"
              :class="journalForm.type === 'debit' ? 'bg-blue-500/10 text-blue-400 border-blue-500/20' : 'bg-amber-500/10 text-amber-400 border-amber-500/20'"
            >
              Direction: {{ journalForm.type === 'debit' ? 'Debit (Dr)' : 'Credit (Cr)' }}
            </span>
          </div>
          <select
            v-model="journalForm.category_id"
            required
            class="w-full rounded-lg bg-[var(--bg-surface)] border border-[var(--border-color)] text-[var(--text-main)] text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
          >
            <option value="" disabled>-- Select Item Category --</option>
            <option
              v-for="cat in filteredAccountItems"
              :key="cat.id"
              :value="cat.id"
            >
              {{ cat.name }} ({{ cat.code }})
            </option>
          </select>
        </div>

        <!-- 5. Amount & Date -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <UiInput
            v-model="journalForm.amount"
            type="number"
            step="0.01"
            min="0.01"
            label="Amount"
            placeholder="1500.00"
            :required="true"
          />

          <UiInput
            v-model="journalForm.date"
            type="date"
            label="Posting Date"
            :required="true"
          />
        </div>

        <!-- 6. Description / Memo -->
        <div class="space-y-1.5">
          <label class="block text-xs font-medium text-[var(--text-muted)] uppercase tracking-wider">
            Transaction Description / Memo <span class="text-rose-400">*</span>
          </label>
          <input
            v-model="journalForm.description"
            type="text"
            required
            placeholder="e.g. Invoice #PO-9021 for structural concrete pour"
            class="w-full rounded-lg bg-[var(--bg-surface)] border border-[var(--border-color)] text-[var(--text-main)] text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
          />
        </div>

        <div class="pt-4 border-t border-[var(--border-color)] flex justify-end gap-3">
          <UiButton type="button" variant="secondary" @click="isPostJournalModalOpen = false">
            Cancel
          </UiButton>
          <UiButton type="submit" variant="primary">
            Post Entry
          </UiButton>
        </div>
      </form>
    </Modal>

    <!-- MODAL: Add Fund Source -->
    <Modal
      :is-open="isAddFundModalOpen"
      title="Add Project Fund Source"
      @close="isAddFundModalOpen = false"
    >
      <form @submit.prevent="handleAddFundSource" class="space-y-4">
        <!-- VARIANT C: Two-Tab Navigation at top of modal -->
        <div v-if="currentVariant === 'C'" class="flex border-b border-[var(--border-color)] mb-4">
          <button
            type="button"
            @click="modalTab = 'existing'"
            class="flex-1 pb-2 text-xs font-semibold text-center border-b-2 transition-colors cursor-pointer"
            :class="modalTab === 'existing' ? 'border-emerald-400 text-emerald-400' : 'border-transparent text-[var(--text-muted)] hover:text-[var(--text-main)]'"
          >
            Link Existing Account
          </button>
          <button
            type="button"
            @click="modalTab = 'new'"
            class="flex-1 pb-2 text-xs font-semibold text-center border-b-2 transition-colors cursor-pointer"
            :class="modalTab === 'new' ? 'border-emerald-400 text-emerald-400' : 'border-transparent text-[var(--text-muted)] hover:text-[var(--text-main)]'"
          >
            Create & Link New Account
          </button>
        </div>

        <!-- Render selection / input based on variant and state -->
        <div>
          <!-- Variant A: Clean Dropdown Only -->
          <div v-if="currentVariant === 'A'" class="space-y-1">
            <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Fund Source Account</label>
            <select
              v-model="fundForm.name"
              class="w-full rounded-lg bg-[var(--bg-surface)] border border-[var(--border-color)] text-[var(--text-main)] text-sm px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
              required
            >
              <option value="" disabled>Select an available fund account</option>
              <option v-for="fund in accountingStore.fundAccounts.value" :key="fund.id" :value="fund.fund_name">
                {{ fund.fund_code }} - {{ fund.fund_name }} (Balance: {{ currencyStore.formatCurrency(fund.amount || 0) }})
              </option>
            </select>
          </div>

          <!-- Variant B: Dropdown + Quick Add inline -->
          <div v-else-if="currentVariant === 'B'" class="space-y-3">
            <div v-if="!isCreatingNewFund" class="space-y-1">
              <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Fund Source Account</label>
              <select
                v-model="fundForm.name"
                class="w-full rounded-lg bg-[var(--bg-surface)] border border-[var(--border-color)] text-[var(--text-main)] text-sm px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                required
              >
                <option value="" disabled>Select an available fund account</option>
                <option v-for="fund in accountingStore.fundAccounts.value" :key="fund.id" :value="fund.fund_name">
                  {{ fund.fund_code }} - {{ fund.fund_name }} (Balance: {{ currencyStore.formatCurrency(fund.amount || 0) }})
                </option>
              </select>
            </div>
            <UiInput
              v-else
              v-model="fundForm.name"
              label="New Fund Source Name"
              placeholder="e.g. Client Initial Deposit, Bank Draw #1"
              :required="true"
            />
            <div class="flex justify-end">
              <button
                type="button"
                @click="isCreatingNewFund = !isCreatingNewFund"
                class="text-[11px] font-semibold text-emerald-400 hover:text-emerald-300 transition-colors cursor-pointer"
              >
                {{ isCreatingNewFund ? '← Select existing account' : '+ Create new fund account' }}
              </button>
            </div>
          </div>

          <!-- Variant C: Two-Tab selection -->
          <div v-else-if="currentVariant === 'C'">
            <div v-if="modalTab === 'existing'" class="space-y-1">
              <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Fund Source Account</label>
              <select
                v-model="fundForm.name"
                class="w-full rounded-lg bg-[var(--bg-surface)] border border-[var(--border-color)] text-[var(--text-main)] text-sm px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                required
              >
                <option value="" disabled>Select an available fund account</option>
                <option v-for="fund in accountingStore.fundAccounts.value" :key="fund.id" :value="fund.fund_name">
                  {{ fund.fund_code }} - {{ fund.fund_name }} (Balance: {{ currencyStore.formatCurrency(fund.amount || 0) }})
                </option>
              </select>
            </div>
            <UiInput
              v-else
              v-model="fundForm.name"
              label="New Fund Source Name"
              placeholder="e.g. Client Initial Deposit, Bank Draw #1"
              :required="true"
            />
          </div>
        </div>

        <UiInput
          v-if="(currentVariant === 'B' && isCreatingNewFund) || (currentVariant === 'C' && modalTab === 'new')"
          v-model="fundForm.amount"
          type="number"
          step="0.01"
          min="0"
          prefix="$"
          label="Initial Balance ($)"
          placeholder="50000.00"
          :required="true"
        />

        <UiInput
          v-model="fundForm.date_received"
          type="date"
          label="Date Received"
          :required="true"
        />

        <div class="pt-4 border-t border-[var(--border-color)] flex justify-end gap-3">
          <UiButton type="button" variant="secondary" @click="isAddFundModalOpen = false">
            Cancel
          </UiButton>
          <UiButton type="submit" variant="primary">
            Add Fund Source
          </UiButton>
        </div>
      </form>
    </Modal>

    <!-- MODAL: Accounting Ledger Print Preview -->
    <Modal
      :is-open="isPrintBalanceSheetModalOpen"
      title="Project General Ledger Statement"
      @close="isPrintBalanceSheetModalOpen = false"
    >
      <div class="space-y-4">
        <!-- Top Action Bar in Modal -->
        <div class="flex items-center justify-between bg-slate-800/60 p-3 rounded-lg border border-[var(--border-color)] no-print">
          <div class="flex items-center gap-2">
            <UiButton type="button" variant="primary" size="sm" @click="triggerPrint">
              <template #icon-left>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
              </template>
              Print Report (Ctrl+P)
            </UiButton>
          </div>
        </div>

        <!-- Printable Document Area -->
        <div id="printable-balance-sheet" class="bg-white text-slate-900 p-8 rounded-xl shadow-lg font-sans border border-slate-200">
          <!-- Primary Header: Company Name -->
          <div class="border-b-2 border-slate-900 pb-4 text-center">
            <h1 class="text-2xl font-black uppercase tracking-tight text-slate-900">{{ companyNameHeader }}</h1>
            <!-- Secondary Line: Company Address formatted with backend model attributes -->
            <p class="text-xs text-slate-600 font-medium mt-1">{{ formattedCompanyAddress }}</p>
          </div>

          <!-- Project Metadata Grid -->
          <div class="grid grid-cols-2 gap-4 py-4 border-b border-slate-200 text-xs">
            <div>
              <p><span class="font-bold text-slate-700">Project Name:</span> <strong class="text-slate-900 font-extrabold">{{ project?.name }}</strong></p>
              <p class="mt-1"><span class="font-bold text-slate-700">Client Name:</span> {{ project?.client_name }}</p>
              <p class="mt-1"><span class="font-bold text-slate-700">Project Address:</span> {{ formatAddress(project?.address) }}</p>
            </div>
            <div class="text-right">
              <p><span class="font-bold text-slate-700">Statement Date:</span> {{ statementDate }}</p>
              <p class="mt-1"><span class="font-bold text-slate-700">Status:</span> <span class="uppercase font-bold text-emerald-700">{{ project?.status }}</span></p>
              <p class="mt-1"><span class="font-bold text-slate-700">Started:</span> {{ formatDateMMDDYY(project?.start_date) }}</p>
            </div>
          </div>

          <!-- Chronological Statement Table -->
          <div class="mt-6">
            <table class="w-full text-[11px] border-collapse">
              <thead>
                <tr class="bg-slate-100 text-slate-800 font-bold uppercase text-[10px]">
                  <th class="py-2 px-2 text-left">Date</th>
                  <th class="py-2 px-2 text-left">Item/Description</th>
                  <th class="py-2 px-2 text-left">Account</th>
                  <th class="py-2 px-2 text-left">Fund</th>
                  <th class="py-2 px-2 text-right">Debit</th>
                  <th class="py-2 px-2 text-right">Credit</th>
                  <th class="py-2 px-2 text-right">Running Balance</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-200">
                <tr v-for="item in chronologicalStatement" :key="item.id" class="hover:bg-slate-50">
                  <td class="py-2 px-2 font-mono whitespace-nowrap text-slate-600">{{ item.date }}</td>
                  <td class="py-2 px-2 font-semibold text-slate-900">
                    {{ item.category_name }}
                    <div class="text-[10px] text-slate-500 font-normal truncate max-w-xs">{{ item.description }}</div>
                  </td>
                  <td class="py-2 px-2 font-mono text-slate-600 whitespace-nowrap">{{ item.ledger_code }}</td>
                  <td class="py-2 px-2 font-mono font-bold text-emerald-700 whitespace-nowrap">{{ item.fund_code }}</td>
                  <td class="py-2 px-2 text-right font-mono font-medium text-rose-700">
                    {{ item.debit_amount > 0 ? currencyStore.formatCurrency(item.debit_amount) : '-' }}
                  </td>
                  <td class="py-2 px-2 text-right font-mono font-medium text-emerald-700">
                    {{ item.credit_amount > 0 ? currencyStore.formatCurrency(item.credit_amount) : '-' }}
                  </td>
                  <td class="py-2 px-2 text-right font-mono font-bold" :class="item.running_balance < 0 ? 'text-rose-700' : 'text-slate-900'">
                    {{ currencyStore.formatCurrency(item.running_balance) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Financial Aggregations Footer -->
          <div class="mt-6 pt-4 border-t-2 border-slate-900 grid grid-cols-3 gap-4 text-xs font-mono">
            <div class="bg-slate-50 p-3 rounded border border-slate-200">
              <span class="text-[10px] font-sans uppercase font-bold text-slate-500 block">Total Debits (Expenses)</span>
              <span class="text-sm font-bold text-rose-700">{{ currencyStore.formatCurrency(totalProjectDebits) }}</span>
            </div>
            <div class="bg-slate-50 p-3 rounded border border-slate-200">
              <span class="text-[10px] font-sans uppercase font-bold text-slate-500 block">Total Credits / Funds</span>
              <span class="text-sm font-bold text-emerald-700">{{ currencyStore.formatCurrency(totalProjectFunds + totalProjectCredits) }}</span>
            </div>
            <div class="bg-slate-900 text-white p-3 rounded border border-slate-900">
              <span class="text-[10px] font-sans uppercase font-bold text-slate-400 block">Net Balance Position</span>
              <span class="text-sm font-extrabold" :class="netLedgerBalance < 0 ? 'text-rose-400' : 'text-emerald-400'">
                {{ currencyStore.formatCurrency(netLedgerBalance) }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </Modal>

    <!-- Floating Switcher for UI Prototype Variants -->
    <div class="fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-slate-900 border border-slate-700/80 rounded-full px-4 py-2 flex items-center gap-3 shadow-2xl z-50">
      <button
        type="button"
        @click="cycleVariant(-1)"
        class="text-slate-400 hover:text-white transition-colors p-1 cursor-pointer"
        title="Previous Variant"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </button>

      <span class="text-xs font-bold text-white whitespace-nowrap min-w-[140px] text-center">
        Variant {{ currentVariant }} — {{ getVariantLabel(currentVariant) }}
      </span>

      <button
        type="button"
        @click="cycleVariant(1)"
        class="text-slate-400 hover:text-white transition-colors p-1 cursor-pointer"
        title="Next Variant"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </button>
    </div>
  </div>

  <div v-else class="glass-card rounded-xl border border-[var(--border-color)] p-12 text-center">
    <h2 class="text-base font-bold text-rose-400">Project Not Found</h2>
    <p class="text-xs text-[var(--text-muted)] mt-1">The requested construction project could not be found.</p>
    <NuxtLink to="/" class="mt-4 inline-block">
      <UiButton type="button" variant="primary" size="sm">Back to Dashboard</UiButton>
    </NuxtLink>
  </div>
</template>

<script setup lang="ts">
import type { DataTableColumn } from '~/components/ui/DataTable.vue'
import type { TabItem } from '~/components/ui/Tabs.vue'

const route = useRoute()
const authStore = useAuth()
const currencyStore = useCurrency()
const projectsStore = useProjects()
const accountingStore = useAccounting()

const projectId = computed(() => Number(route.params.id))
const project = computed(() => projectsStore.projects.value.find(p => p.id === projectId.value))

const activeTab = ref<'transactions' | 'funds'>('transactions')

const projectTabs = computed<TabItem[]>(() => [
  { value: 'transactions', label: 'Recent Journal Transactions', badge: projectTransactions.value.length },
  { value: 'funds', label: 'Allocated Fund Accounts', badge: projectFundSources.value.length },
])
const isPostJournalModalOpen = ref(false)
const isAddFundModalOpen = ref(false)
const isPrintBalanceSheetModalOpen = ref(false)
const statementDate = ref(new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }))

const router = useRouter()

const currentVariant = computed({
  get() {
    const v = route.query.variant as string
    return ['A', 'B', 'C'].includes(v) ? v : 'A'
  },
  set(val) {
    router.replace({ query: { ...route.query, variant: val } })
  }
})

const isCreatingNewFund = ref(false)
const modalTab = ref<'existing' | 'new'>('existing')

watch(currentVariant, () => {
  fundForm.name = ''
  isCreatingNewFund.value = false
  modalTab.value = 'existing'
})

watch(modalTab, () => {
  fundForm.name = ''
})

watch(isCreatingNewFund, () => {
  fundForm.name = ''
})

const getVariantLabel = (v: string) => {
  switch (v) {
    case 'A': return 'Clean Dropdown Only'
    case 'B': return 'Dropdown + Quick Add'
    case 'C': return 'Two-Tab Interface'
    default: return ''
  }
}

const cycleVariant = (dir: number) => {
  const variants = ['A', 'B', 'C']
  const idx = variants.indexOf(currentVariant.value)
  const nextIdx = (idx + dir + variants.length) % variants.length
  currentVariant.value = variants[nextIdx]
}

const handleKeyDown = (e: KeyboardEvent) => {
  const activeEl = document.activeElement
  if (activeEl && (
    activeEl.tagName === 'INPUT' ||
    activeEl.tagName === 'TEXTAREA' ||
    activeEl.getAttribute('contenteditable') !== null
  )) {
    return
  }

  if (e.key === 'ArrowLeft') {
    cycleVariant(-1)
  } else if (e.key === 'ArrowRight') {
    cycleVariant(1)
  }
}

onMounted(() => {
  window.addEventListener('keydown', handleKeyDown)
})

onBeforeUnmount(() => {
  window.removeEventListener('keydown', handleKeyDown)
})

const journalForm = reactive({
  fund_source_id: '' as number | string,
  type: 'debit' as 'debit' | 'credit',
  ledger_account_id: '' as number | string,
  category_id: '' as number | string,
  amount: '' as string | number,
  date: new Date().toISOString().split('T')[0],
  description: '',
})

const fundForm = reactive({
  name: '',
  amount: '' as string | number,
  date_received: new Date().toISOString().split('T')[0],
})

// Columns Definitions
const transactionColumns: DataTableColumn[] = [
  { key: 'date', label: 'Date', sortable: true, width: 'whitespace-nowrap' },
  { key: 'type', label: 'Type', sortable: true, width: 'w-24' },
  { key: 'category', label: 'Item Category', sortable: true, width: 'min-w-[160px]' },
  { key: 'fund', label: 'Fund Source', sortable: true, width: 'min-w-[180px]' },
  { key: 'note', label: 'Note / Description', sortable: true, width: 'min-w-[220px]' },
  { key: 'amount', label: 'Amount', sortable: true, align: 'right', width: 'min-w-[140px]' },
]

const fundSourceColumns: DataTableColumn[] = [
  { key: 'name', label: 'Fund Name', sortable: true, width: 'min-w-[200px]' },
  { key: 'date_received', label: 'Date Received', sortable: true, width: 'whitespace-nowrap' },
  { key: 'amount', label: 'Initial Allocation', sortable: true, align: 'right', width: 'min-w-[140px]' },
  { key: 'spent', label: 'Amount Spent', sortable: true, align: 'right', width: 'min-w-[140px]' },
  { key: 'remaining', label: 'Remaining', sortable: true, align: 'right', width: 'min-w-[140px]' },
  { key: 'usage', label: 'Utilization', sortable: true, width: 'min-w-[180px]' },
]

// Real-time Card Aggregations
const totalProjectFunds = computed(() => projectsStore.getProjectTotalFunds(projectId.value))
const totalProjectDebits = computed(() => projectsStore.getProjectTotalDebits(projectId.value))
const totalProjectCredits = computed(() => projectsStore.getProjectTotalCredits(projectId.value))
const netLedgerBalance = computed(() => projectsStore.getProjectNetLedgerBalance(projectId.value))
const activeFundAccountsBalance = computed(() => projectsStore.getProjectActiveFundBalance(projectId.value))
const isOverBudget = computed(() => activeFundAccountsBalance.value < 0)

const projectFundSources = computed(() => {
  return projectsStore.fundSources.value.filter(f => f.project_id === projectId.value)
})

const projectTransactions = computed(() => {
  return projectsStore.transactions.value.filter(t => t.project_id === projectId.value)
})

const activeCategories = computed(() => {
  return projectsStore.categories.value.filter(c => c.status === 'active')
})

const filteredAccountItems = computed(() => {
  if (!journalForm.ledger_account_id) {
    return activeCategories.value
  }
  const selectedLedgerId = Number(journalForm.ledger_account_id)
  const matches = activeCategories.value.filter(c => c.ledger_account_id === selectedLedgerId)
  return matches.length > 0 ? matches : activeCategories.value
})

watch(() => journalForm.ledger_account_id, (newLedgerId) => {
  if (!newLedgerId) return
  const numId = Number(newLedgerId)
  const matches = activeCategories.value.filter(c => c.ledger_account_id === numId)
  if (matches.length > 0) {
    journalForm.category_id = matches[0].id
    if (matches[0].transaction_type) {
      journalForm.type = matches[0].transaction_type
    }
  }
})

watch(() => journalForm.category_id, (newCatId) => {
  if (!newCatId) return
  const cat = activeCategories.value.find(c => c.id === Number(newCatId))
  if (cat) {
    if (cat.transaction_type) {
      journalForm.type = cat.transaction_type
    }
    if (cat.ledger_account_id && !journalForm.ledger_account_id) {
      journalForm.ledger_account_id = cat.ledger_account_id
    }
  }
})

const formatAddress = (address: any) => {
  if (!address) return 'N/A'
  if (typeof address === 'string') return address
  return `${address.street}, ${address.city} (${address.zip_code})`
}

const getFundSourceName = (fundId: number) => {
  const fund = projectsStore.fundSources.value.find(f => f.id === fundId)
  return fund ? fund.name : 'General Fund'
}

const getCategoryName = (catId: number) => {
  const cat = projectsStore.categories.value.find(c => c.id === catId)
  return cat ? cat.name : 'Line Item'
}

const getFundSourceSortValue = (item: any, key: string) => {
  switch (key) {
    case 'spent': return projectsStore.getFundSourceSpent(item.id)
    case 'remaining': return projectsStore.getFundSourceRemaining(item.id)
    case 'usage': return projectsStore.getFundSourceUsagePercentage(item.id)
    default: return undefined
  }
}

const getTransactionSortValue = (item: any, key: string) => {
  switch (key) {
    case 'category': return getCategoryName(item.category_id)
    case 'fund': return getFundSourceName(item.fund_source_id)
    default: return undefined
  }
}

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

const handleAddFundSource = async () => {
  if (!fundForm.name) return

  const isNew = currentVariant.value === 'B'
    ? isCreatingNewFund.value
    : (currentVariant.value === 'C' ? modalTab.value === 'new' : false)

  if (isNew && !fundForm.amount) return

  let finalAmount = 0
  if (isNew) {
    finalAmount = Number(fundForm.amount)
  } else {
    const existingFund = accountingStore.fundAccounts.value.find(
      f => f.fund_name === fundForm.name
    )
    finalAmount = existingFund ? Number(existingFund.amount) || 0 : 0
  }

  try {
    await projectsStore.addFundSource({
      project_id: projectId.value,
      name: fundForm.name,
      amount: finalAmount,
    })

    fundForm.name = ''
    fundForm.amount = ''
    fundForm.date_received = new Date().toISOString().split('T')[0]
    isAddFundModalOpen.value = false
  } catch (err: any) {
    alert(err?.data?.message || err?.message || 'Failed to add fund source.')
  }
}

const companyNameHeader = computed(() => {
  return authStore.currentCompany.value?.business_name || 'AccountAnt Corporate Solutions Inc.'
})

const formattedCompanyAddress = computed(() => {
  const comp = authStore.currentCompany.value
  if (!comp) return '100 Financial Towers, BGC, Taguig City, Metro Manila 1634, Philippines'
  
  const bld = comp.building_number ? `${comp.building_number} ` : ''
  const street = comp.street || '100 Financial Towers'
  const barangay = comp.barangay ? `, ${comp.barangay}` : ', BGC'
  const zip = comp.zip ? ` ${comp.zip}` : ' 1634'
  
  let cityName = 'Taguig City'
  if (comp.city_id === 1) cityName = 'Quezon City'
  else if (comp.city_id === 2) cityName = 'Taguig City (BGC)'
  else if (comp.city_id === 3) cityName = 'Pasig City'
  else if (comp.city_id === 4) cityName = 'Cebu City'
  else if (comp.city_id === 5) cityName = 'Davao City'
  
  return `${bld}${street}${barangay}, ${cityName}${zip}, Philippines`
})

const formatDateMMDDYY = (dateStr?: string) => {
  if (!dateStr) return '—'
  const dateObj = new Date(dateStr)
  if (isNaN(dateObj.getTime())) return dateStr
  const month = String(dateObj.getMonth() + 1).padStart(2, '0')
  const day = String(dateObj.getDate()).padStart(2, '0')
  const year = String(dateObj.getFullYear()).slice(-2)
  return `${month}/${day}/${year}`
}

const getFundSourceCode = (fundId: number) => {
  const fund = accountingStore.fundAccounts.value.find(f => f.id === fundId)
  if (fund && fund.fund_code) return fund.fund_code
  const projectFund = projectsStore.fundSources.value.find(f => f.id === fundId)
  if (projectFund) {
    const matchingAcctFund = accountingStore.fundAccounts.value.find(f => f.fund_name.toLowerCase() === projectFund.name.toLowerCase())
    if (matchingAcctFund) return matchingAcctFund.fund_code
    return `FND-${projectFund.id}`
  }
  return 'FND-GEN'
}

const getLedgerCodeForCategory = (catId: number) => {
  const item = accountingStore.accountItems.value.find(i => i.id === catId)
  if (item && item.ledger_account_id) {
    const acc = accountingStore.ledgerAccounts.value.find(a => a.id === item.ledger_account_id)
    if (acc) return acc.account_code
  }
  return '1000-GENERAL'
}

const chronologicalStatement = computed(() => {
  const items: Array<{
    id: string | number
    date: string
    type: 'allocation' | 'debit' | 'credit'
    category_name: string
    ledger_code: string
    fund_code: string
    description: string
    debit_amount: number
    credit_amount: number
    running_balance: number
  }> = []

  // 1. Opening Fund Allocations (Option A)
  const funds = projectFundSources.value
  for (const f of funds) {
    const rawDate = f.date_received || project.value?.start_date || new Date().toISOString().split('T')[0]
    items.push({
      id: `FUND-${f.id}`,
      date: formatDateMMDDYY(rawDate),
      type: 'allocation',
      category_name: 'Opening Capital Allocation',
      ledger_code: '1010-CASH',
      fund_code: getFundSourceCode(f.id),
      description: `Initial fund allocation for ${f.name}`,
      debit_amount: 0,
      credit_amount: Number(f.amount),
      running_balance: 0,
    })
  }

  // 2. Journal Transactions
  const txs = projectTransactions.value
  for (const t of txs) {
    const isDebit = t.type === 'debit'
    items.push({
      id: `TX-${t.id}`,
      date: formatDateMMDDYY(t.date),
      type: t.type,
      category_name: getCategoryName(t.category_id),
      ledger_code: getLedgerCodeForCategory(t.category_id),
      fund_code: getFundSourceCode(t.fund_source_id),
      description: t.note || 'Journal entry transaction',
      debit_amount: isDebit ? Number(t.amount) : 0,
      credit_amount: isDebit ? 0 : Number(t.amount),
      running_balance: 0,
    })
  }

  // Sort chronological (oldest date first)
  items.sort((a, b) => new Date(a.date).getTime() - new Date(b.date).getTime())

  // Compute running balance chronologically
  let balance = 0
  for (const item of items) {
    balance += item.credit_amount - item.debit_amount
    item.running_balance = balance
  }

  return items
})

const triggerPrint = () => {
  window.print()
}
</script>

<style scoped>
@media print {
  body * {
    visibility: hidden !important;
  }
  #printable-balance-sheet,
  #printable-balance-sheet * {
    visibility: visible !important;
  }
  #printable-balance-sheet {
    position: fixed !important;
    left: 0 !important;
    top: 0 !important;
    width: 100% !important;
    margin: 0 !important;
    padding: 24px !important;
    background: #ffffff !important;
    color: #000000 !important;
    box-shadow: none !important;
    border: none !important;
    z-index: 999999 !important;
  }
  .no-print,
  button,
  nav,
  header,
  .fixed:not(#printable-balance-sheet) {
    display: none !important;
  }
}
</style>
