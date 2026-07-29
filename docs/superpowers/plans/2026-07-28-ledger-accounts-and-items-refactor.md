# Ledger Accounts & Items Refactor Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Refactor Ledger Accounts page layout to card-style grid with live search (matching `funds.vue`), remove parent fund account selector when creating ledger accounts, add ledger account selector when creating account items, auto-populate ledger accounts upon selecting account items in journal entries, and update backend models, controllers, seeders, and database schema.

**Architecture:** Update Laravel backend Eloquent models (`LedgerAccount`, `AccountItem`), controllers (`LedgerAccountController`, `AccountItemController`), migrations (`2026_07_28_000001_add_ledger_account_id_to_account_items_and_make_fund_account_id_nullable.php`), and Nuxt 3 frontend composables/pages (`useAccounting.ts`, `accounts.vue`, `items.vue`, `journal.vue`).

**Tech Stack:** Laravel 11 (PHP 8.2), Nuxt 3 (Vue 3, Tailwind CSS), Ionic Vue primitives.

## Global Constraints

- Preserve existing dark mode styling tokens (`glass-card`, `border-[var(--border-color)]`, `text-[var(--text-main)]`, `text-[var(--text-muted)]`).
- Follow card layout patterns established in `src/frontend/app/pages/management/funds.vue`.
- Make foreign key relationships nullable and safe for deletion (`nullOnDelete`).
- Reference ADR 0006 (`docs/ADR/0006-decoupling-ledger-accounts-and-linking-account-items.md`) for architectural decisions.

---

### Task 1: Database Migration & Eloquent Model Updates

**Files:**
- Create: `src/backend/database/migrations/2026_07_28_000001_add_ledger_account_id_to_account_items_and_make_fund_account_id_nullable.php`
- Modify: `src/backend/app/Models/Accounting/AccountItem.php`
- Modify: `src/backend/app/Models/Accounting/LedgerAccount.php`

**Interfaces:**
- Consumes: Database schema for `ledger_accounts` and `account_items`
- Produces: `ledger_account_id` foreign key on `account_items`, nullable `fund_account_id` on `ledger_accounts`, Eloquent relationships `ledgerAccount()` and `accountItems()`.

- [ ] **Step 1: Create migration file**

Create migration `src/backend/database/migrations/2026_07_28_000001_add_ledger_account_id_to_account_items_and_make_fund_account_id_nullable.php`:
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ledger_accounts', function (Blueprint $table) {
            $table->unsignedBigInteger('fund_account_id')->nullable()->change();
        });

        Schema::table('account_items', function (Blueprint $table) {
            $table->unsignedBigInteger('ledger_account_id')->nullable()->after('description');
            $table->foreign('ledger_account_id')->references('id')->on('ledger_accounts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('account_items', function (Blueprint $table) {
            $table->dropForeign(['ledger_account_id']);
            $table->dropColumn('ledger_account_id');
        });

        Schema::table('ledger_accounts', function (Blueprint $table) {
            $table->unsignedBigInteger('fund_account_id')->nullable(false)->change();
        });
    }
};
```

- [ ] **Step 2: Update `AccountItem` model**

In `src/backend/app/Models/Accounting/AccountItem.php`:
```php
<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_code',
        'item_name',
        'description',
        'ledger_account_id',
    ];

    public function ledgerAccount()
    {
        return $this->belongsTo(LedgerAccount::class, 'ledger_account_id');
    }
}
```

- [ ] **Step 3: Update `LedgerAccount` model**

In `src/backend/app/Models/Accounting/LedgerAccount.php`:
```php
<?php

namespace App\Models\Accounting;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LedgerAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'account_code',
        'account_name',
        'description',
        'fund_account_id',
        'user_id',
        'ledger_account_id',
    ];

    public function fundAccount()
    {
        return $this->belongsTo(FundAccount::class, 'fund_account_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function journalEntries()
    {
        return $this->hasMany(LedgerAccountItem::class, 'ledger_account_id');
    }

    public function accountItems()
    {
        return $this->hasMany(AccountItem::class, 'ledger_account_id');
    }
}
```

---

### Task 2: Backend API Controllers & Seeders

**Files:**
- Modify: `src/backend/app/Http/Controllers/Api/LedgerAccountController.php`
- Modify: `src/backend/app/Http/Controllers/Api/AccountItemController.php`
- Modify: `src/backend/database/seeders/AccountingSeeder.php`

- [ ] **Step 1: Update `LedgerAccountController`**

In `src/backend/app/Http/Controllers/Api/LedgerAccountController.php`:
Change validation for `fund_account_id` to optional (`nullable|integer`) in `store()`:
```php
        $validated = $request->validate([
            'account_code' => 'required|string|max:20',
            'account_name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'fund_account_id' => 'nullable|integer',
        ]);

        $account = LedgerAccount::create([
            'account_code' => $validated['account_code'],
            'account_name' => $validated['account_name'],
            'description' => $validated['description'] ?? null,
            'fund_account_id' => $validated['fund_account_id'] ?? null,
            'user_id' => 1,
            'ledger_account_id' => 1,
        ]);
```

- [ ] **Step 2: Update `AccountItemController`**

In `src/backend/app/Http/Controllers/Api/AccountItemController.php`:
```php
    public function index()
    {
        $items = AccountItem::with('ledgerAccount')->get();

        if ($items->isEmpty()) {
            AccountItem::create(['item_code' => 'ITEM-ACC-01', 'item_name' => 'Client Subscription Payment', 'description' => 'Enterprise tier automated ledger subscription', 'ledger_account_id' => 40]);
            AccountItem::create(['item_code' => 'ITEM-EXP-02', 'item_name' => 'Cloud Hosting Infrastructure', 'description' => 'AWS/GCP GPU cluster monthly compute fee', 'ledger_account_id' => 20]);
            $items = AccountItem::with('ledgerAccount')->get();
        }

        return response()->json($items);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_code' => 'required|string|max:20',
            'item_name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'ledger_account_id' => 'nullable|integer',
        ]);

        $item = AccountItem::create($validated);

        return response()->json([
            'message' => 'Account item created successfully',
            'data' => $item->load('ledgerAccount'),
        ], 201);
    }
```

- [ ] **Step 3: Update `AccountingSeeder.php`**

In `src/backend/database/seeders/AccountingSeeder.php`, attach `ledger_account_id` to items:
```php
        $items = [
            [
                'id' => 1,
                'item_code' => 'ITEM-ACC-01',
                'item_name' => 'Client Subscription Payment',
                'description' => 'Enterprise tier automated ledger subscription',
                'ledger_account_id' => 40,
            ],
            [
                'id' => 2,
                'item_code' => 'ITEM-EXP-02',
                'item_name' => 'Cloud Hosting Infrastructure',
                'description' => 'AWS/GCP GPU cluster monthly compute fee',
                'ledger_account_id' => 20,
            ],
            [
                'id' => 3,
                'item_code' => 'ITEM-PAY-03',
                'item_name' => 'Engineering Staff Payroll',
                'description' => 'Monthly engineering team salary disbursement',
                'ledger_account_id' => 30,
            ],
            [
                'id' => 4,
                'item_code' => 'ITEM-TAX-04',
                'item_name' => 'Corporate Tax Withholding',
                'description' => 'Quarterly state and federal tax remittance',
                'ledger_account_id' => 10,
            ],
            [
                'id' => 5,
                'item_code' => 'ITEM-LIC-05',
                'item_name' => 'Database Security License',
                'description' => 'Annual database encryption key management service',
                'ledger_account_id' => 20,
            ],
        ];
```

---

### Task 3: Frontend Composable (`useAccounting.ts`) Updates

**Files:**
- Modify: `src/frontend/app/composables/useAccounting.ts`

- [ ] **Step 1: Update `LedgerAccount` and `AccountItem` interfaces**

In `src/frontend/app/composables/useAccounting.ts`:
```typescript
export interface LedgerAccount {
  id: number
  account_code: string
  account_name: string
  description?: string
  fund_account_id?: number
  user_id: number
  ledger_account_id?: number
  created_at?: string
}

export interface AccountItem {
  id: number
  item_code: string
  item_name: string
  description?: string
  ledger_account_id?: number
}
```

- [ ] **Step 2: Update seed state and `addAccountItem` function**

In `useAccounting.ts`:
Assign `ledger_account_id` to seed items:
```typescript
  const accountItems = useState<AccountItem[]>('accounting_account_items', () => [
    { id: 1, item_code: 'ITEM-ACC-01', item_name: 'Client Subscription Payment', description: 'Enterprise tier automated ledger subscription', ledger_account_id: 40 },
    { id: 2, item_code: 'ITEM-EXP-02', item_name: 'Cloud Hosting Infrastructure', description: 'AWS/GCP GPU cluster monthly compute fee', ledger_account_id: 20 },
    { id: 3, item_code: 'ITEM-PAY-03', item_name: 'Engineering Staff Payroll', description: 'Monthly engineering team salary disbursement', ledger_account_id: 30 },
    { id: 4, item_code: 'ITEM-TAX-04', item_name: 'Corporate Tax Withholding', description: 'Quarterly state and federal tax remittance', ledger_account_id: 10 },
    { id: 5, item_code: 'ITEM-LIC-05', item_name: 'Database Security License', description: 'Annual database encryption key management service', ledger_account_id: 20 },
  ])
```

---

### Task 4: Refactor Ledger Accounts Page (`accounts.vue`)

**Files:**
- Modify: `src/frontend/app/pages/management/accounts.vue`

- [ ] **Step 1: Replace table layout with Card Grid layout and Search Bar**

In `src/frontend/app/pages/management/accounts.vue`:
Add a search input bar and filter state `searchQuery`:
```html
    <!-- Search Bar -->
    <div class="glass-card p-4 rounded-xl border border-[var(--border-color)]">
      <div class="relative">
        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-[var(--text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search ledger accounts by code, name, or description..."
          class="input-field pl-9 text-xs"
        />
      </div>
    </div>

    <!-- Ledger Accounts Grid -->
    <ClientOnly>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div
          v-for="account in filteredAccounts"
          :key="account.id"
          class="glass-card p-5 rounded-xl border border-[var(--border-color)] relative overflow-hidden flex flex-col justify-between group"
        >
          <div>
            <div class="flex items-center justify-between mb-3">
              <span class="px-2.5 py-1 rounded bg-blue-500/10 text-blue-400 border border-blue-500/20 font-mono text-xs font-bold">
                {{ account.account_code }}
              </span>
              <span class="text-[11px] text-[var(--text-muted)] font-mono">ID: #{{ account.id }}</span>
            </div>

            <h3 class="text-base font-bold text-[var(--text-main)] group-hover:text-blue-500 transition-colors">
              {{ account.account_name }}
            </h3>
            <p class="text-xs text-[var(--text-muted)] mt-1 line-clamp-2">
              {{ account.description || 'No description provided.' }}
            </p>
          </div>

          <div class="pt-4 mt-4 border-t border-[var(--border-color)] flex items-center justify-between text-xs">
            <div class="text-[var(--text-muted)]">
              Created: <span class="font-mono text-[var(--text-main)]">{{ account.created_at || 'N/A' }}</span>
            </div>
            <span class="text-blue-500 font-semibold">Active Account</span>
          </div>
        </div>
      </div>
    </ClientOnly>
```

- [ ] **Step 2: Remove Parent Fund Account dropdown from Modal**

Remove the `<select>` form group for Parent Fund Account in the Create Modal.
Update `newAcc` reactive object to omit `fund_account_id` requirement:
```typescript
const newAcc = reactive({
  account_code: '',
  account_name: '',
  description: '',
  user_id: 1,
  ledger_account_id: 1,
})
```

---

### Task 5: Add Ledger Account Dropdown to Account Items Page (`items.vue`) with Search

**Files:**
- Modify: `src/frontend/app/pages/management/items.vue`

- [ ] **Step 1: Add Search bar and Ledger Account select field to Create Modal**

In `src/frontend/app/pages/management/items.vue`:
Add search bar filter above grid:
```html
    <!-- Search Bar -->
    <div class="glass-card p-4 rounded-xl border border-[var(--border-color)]">
      <div class="relative">
        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-[var(--text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search items by item code or name..."
          class="input-field pl-9 text-xs"
        />
      </div>
    </div>
```

Modal field:
```html
        <div>
          <label class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1">Ledger Account *</label>
          <select v-model="newItem.ledger_account_id" required class="input-field">
            <option value="" disabled>Select linked ledger account...</option>
            <option v-for="acc in accounting.ledgerAccounts.value" :key="acc.id" :value="acc.id">
              {{ acc.account_code }} - {{ acc.account_name }}
            </option>
          </select>
        </div>
```

- [ ] **Step 2: Display linked Ledger Account badge on catalog cards**

In `items.vue`:
```html
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

            <div v-if="item.ledger_account_id" class="mt-2">
              <span class="inline-flex items-center gap-1 text-[11px] font-mono bg-blue-500/10 text-blue-400 border border-blue-500/20 px-2 py-0.5 rounded">
                Linked Account: {{ getLedgerAccountCode(item.ledger_account_id) }}
              </span>
            </div>
          </div>
```

Add helper `getLedgerAccountCode`:
```typescript
const getLedgerAccountCode = (ledgerAccountId?: number) => {
  if (!ledgerAccountId) return 'N/A'
  const acc = accounting.ledgerAccounts.value.find(a => a.id === ledgerAccountId)
  return acc ? `${acc.account_code} (${acc.account_name})` : `#${ledgerAccountId}`
}
```

Update `newItem` reactive object:
```typescript
const newItem = reactive({
  item_code: '',
  item_name: '',
  description: '',
  ledger_account_id: undefined as number | undefined,
})
```

---

### Task 6: Auto-populate Ledger Account when selecting Account Item in Journal Entry Modal (`journal.vue`)

**Files:**
- Modify: `src/frontend/app/pages/management/journal.vue`

- [ ] **Step 1: Watch `newEntry.account_item_id` to auto-fill `newEntry.ledger_account_id`**

In `src/frontend/app/pages/management/journal.vue`:
```typescript
watch(() => newEntry.account_item_id, (newItemId) => {
  if (!newItemId) return
  const item = accounting.accountItems.value.find(i => i.id === newItemId)
  if (item && item.ledger_account_id) {
    newEntry.ledger_account_id = item.ledger_account_id
  }
})
```

---

### Task 7: Verification & Testing

- [ ] **Step 1: Test creating a ledger account without parent fund dropdown**
- [ ] **Step 2: Test creating an account item with ledger account dropdown selected**
- [ ] **Step 3: Test posting a journal entry and verifying auto-population of ledger account**
- [ ] **Step 4: Verify search filtering and card grid layout in accounts.vue and items.vue**
