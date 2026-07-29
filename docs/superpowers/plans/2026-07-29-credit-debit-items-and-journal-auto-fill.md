# Account Item Credit/Debit Selection & Journal Auto-Fill Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add a credit/debit (`transaction_type`) selection when creating catalog items in `items.vue` (persisting to DB), remove manual transaction type selection when posting journal entries in `project/[id].vue`, and auto-select line items and transaction type based on chosen ledger account.

**Architecture:** Extend `account_items` schema with a `transaction_type` enum (`debit` | `credit`). Pass this attribute through Laravel model/controller and Nuxt `useAccounting` composables. In `project/[id].vue`, update the Post Journal Entry modal to filter available line items when a ledger account is selected, auto-selecting the matching line item and setting the transaction type automatically.

**Tech Stack:** Vue 3 / Nuxt 3, TypeScript, Tailwind CSS, Laravel 11 PHP backend, Eloquent ORM.

## Global Constraints

- Preserve clean UI styling and Glassmorphism design tokens consistent with Nuxt + Tailwind system.
- Maintain existing API response formats for `/account-items` and `/journal-entries`.
- All backend modifications must be accompanied by proper database migrations.

---

### Task 1: Backend Database Migration, Model & Controller Updates

**Files:**
- Create: `src/backend/database/migrations/2026_07_29_000001_add_transaction_type_to_account_items_table.php`
- Modify: `src/backend/app/Models/Accounting/AccountItem.php`
- Modify: `src/backend/app/Http/Controllers/Api/AccountItemController.php`
- Modify: `src/backend/database/seeders/AccountingSeeder.php`

**Interfaces:**
- Consumes: Database schema `account_items`
- Produces: `AccountItem` model with `transaction_type` ('debit' | 'credit')

- [ ] **Step 1: Create migration for `transaction_type` column**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('account_items', function (Blueprint $table) {
            $table->enum('transaction_type', ['debit', 'credit'])->default('debit')->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('account_items', function (Blueprint $table) {
            $table->dropColumn('transaction_type');
        });
    }
};
```

- [ ] **Step 2: Update `AccountItem.php` model fillable array**

```php
protected $fillable = [
    'item_code',
    'item_name',
    'description',
    'ledger_account_id',
    'transaction_type',
];
```

- [ ] **Step 3: Update `AccountItemController.php` validation & default fallback items**

```php
$validated = $request->validate([
    'item_code' => 'required|string|max:20',
    'item_name' => 'required|string|max:100',
    'description' => 'nullable|string|max:255',
    'ledger_account_id' => 'nullable|integer',
    'transaction_type' => 'nullable|in:debit,credit',
]);
```

- [ ] **Step 4: Update `AccountingSeeder.php` to include `transaction_type` in seed items**

Add `'transaction_type' => 'credit'` for revenue items and `'debit'` for expense items.

- [ ] **Step 5: Run migration**

Run: `php artisan migrate` in `src/backend`

---

### Task 2: Frontend Types & `useAccounting` Composable Updates

**Files:**
- Modify: `src/frontend/app/composables/useAccounting.ts`
- Modify: `src/frontend/app/composables/useProjects.ts`

**Interfaces:**
- Consumes: API endpoints `/account-items`
- Produces: `AccountItem` TypeScript interface with `transaction_type?: 'debit' | 'credit'`

- [ ] **Step 1: Update `AccountItem` interface in `useAccounting.ts`**

```typescript
export interface AccountItem {
  id: number
  item_code: string
  item_name: string
  description?: string
  ledger_account_id?: number
  transaction_type?: 'debit' | 'credit'
}
```

- [ ] **Step 2: Update `ExpenseCategory` in `useProjects.ts` to include `transaction_type` and `ledger_account_id`**

```typescript
export interface ExpenseCategory {
  id: number
  name: string
  code?: string
  status: 'active' | 'archived'
  ledger_account_id?: number
  transaction_type?: 'debit' | 'credit'
  created_at: string
}
```

- [ ] **Step 3: Update `useProjects.ts` mapping when syncing `categories` from `accountItems`**

```typescript
categories.value = accountingStore.accountItems.value.map(item => ({
  id: item.id,
  name: item.item_name,
  code: item.item_code,
  status: 'active',
  ledger_account_id: item.ledger_account_id,
  transaction_type: item.transaction_type || 'debit',
  created_at: new Date().toISOString(),
}))
```

---

### Task 3: Management Account Items Catalog Page Update (`items.vue`)

**Files:**
- Modify: `src/frontend/app/pages/management/items.vue`

**Interfaces:**
- Consumes: `useAccounting()`
- Produces: Form input and visual indicators for Credit/Debit in `items.vue`

- [ ] **Step 1: Update `newItem` state in `items.vue`**

```typescript
const newItem = reactive({
  item_code: '',
  item_name: '',
  description: '',
  ledger_account_id: undefined as number | undefined,
  transaction_type: 'debit' as 'debit' | 'credit',
})
```

- [ ] **Step 2: Add Credit / Debit radio or button selection in create item modal**

```html
<div>
  <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Transaction Type *</label>
  <div class="grid grid-cols-2 gap-3">
    <UiButton
      type="button"
      :variant="newItem.transaction_type === 'debit' ? 'primary' : 'secondary'"
      block
      size="sm"
      @click="newItem.transaction_type = 'debit'"
    >
      Debit (Expense)
    </UiButton>
    <UiButton
      type="button"
      :variant="newItem.transaction_type === 'credit' ? 'primary' : 'secondary'"
      block
      size="sm"
      @click="newItem.transaction_type = 'credit'"
    >
      Credit (Income / Refund)
    </UiButton>
  </div>
</div>
```

- [ ] **Step 3: Add visual indicator badge (Debit / Credit) to Catalog cards**

Render badge for `item.transaction_type` on each account item card.

---

### Task 4: Project Page Journal Entry Modal Update (`project/[id].vue`)

**Files:**
- Modify: `src/frontend/app/pages/project/[id].vue`

**Interfaces:**
- Consumes: `useProjects()`, `useAccounting()`
- Produces: Auto-filling line items and transaction types in Post Journal Entry modal

- [ ] **Step 1: Remove manual Transaction Type selection UI from Post Journal Entry modal**

Remove Step 2 UI block (`Transaction Type: Credit or Debit`).

- [ ] **Step 2: Add watcher / handler on `journalForm.ledger_account_id` selection**

When a ledger account is selected:
1. Filter line items (`activeCategories` or `accountItems`) that match the chosen `ledger_account_id`.
2. Auto-select the first matching line item as `journalForm.category_id`.
3. Auto-set `journalForm.type` to the `transaction_type` of the selected line item/account item.

```typescript
watch(() => journalForm.ledger_account_id, (newLedgerId) => {
  if (!newLedgerId) return
  const numId = Number(newLedgerId)
  const matchingItem = accountingStore.accountItems.value.find(i => i.ledger_account_id === numId)
  if (matchingItem) {
    journalForm.category_id = matchingItem.id
    journalForm.type = matchingItem.transaction_type || 'debit'
  } else {
    // Fallback if no explicit item linked to this ledger account
    const matchingCat = projectsStore.categories.value.find(c => c.ledger_account_id === numId)
    if (matchingCat) {
      journalForm.category_id = matchingCat.id
      journalForm.type = matchingCat.transaction_type || 'debit'
    }
  }
})
```

- [ ] **Step 3: Filter line item options in the modal to show matching items for the selected ledger account**

If a ledger account is selected, restrict or highlight items matching that ledger account.

---

## Verification Plan

### Automated Verification
- Run database migrations: `php artisan migrate` in `src/backend`
- Run seeder if needed: `php artisan db:seed --class=AccountingSeeder`
- Build frontend to check for TypeScript / Vue compilation errors: `npm run build` inside `src/frontend`

### Manual Verification
1. Navigate to `/management/items`
2. Create a new account item, selecting "Credit (Income / Refund)".
3. Verify that the saved item appears with a Credit badge in the catalog grid and persists across reloads.
4. Navigate to a project page e.g. `/project/1`.
5. Open "Post Journal Entry" modal.
6. Verify that the manual Credit/Debit selection control is removed.
7. Select a Ledger Account from the dropdown (e.g. `[4010-REV] SaaS Subscription Revenue`).
8. Verify that the Line Category / Item Name automatically selects `Client Subscription Payment` and sets transaction type to `credit`.
