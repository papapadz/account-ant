# Fund Account Amount & Search Filtering Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add an `amount` (Initial Fund Amount) input field when creating a new fund account, display fund amounts on fund cards, add live search filtering to `funds.vue`, and update backend database schema, Eloquent models, controllers, seeders, and composable state.

**Architecture:** Create database migration `2026_07_28_000002_add_amount_to_fund_accounts_table.php`, update Laravel model `FundAccount.php`, controller `FundAccountController.php`, seeder `AccountingSeeder.php`, composable `useAccounting.ts`, and Nuxt page `funds.vue`. Reference ADR 0008 (`docs/ADR/0008-fund-account-amount-and-search-filtering.md`).

**Tech Stack:** Laravel 11 (PHP 8.2), Nuxt 3 (Vue 3, Tailwind CSS).

## Global Constraints

- Retain existing design system color tokens (`emerald-500`, `glass-card`, `border-[var(--border-color)]`).
- Format all currency figures cleanly with `$XX,XXX.XX` and JetBrains Mono fonts (`font-mono`).
- Provide instant client-side live search filtering across fund code, name, and description.

---

### Task 1: Database Migration & Eloquent Model Updates

**Files:**
- Create: `src/backend/database/migrations/2026_07_28_000002_add_amount_to_fund_accounts_table.php`
- Modify: `src/backend/app/Models/Accounting/FundAccount.php`

- [ ] **Step 1: Create database migration**

Create migration `src/backend/database/migrations/2026_07_28_000002_add_amount_to_fund_accounts_table.php`:
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fund_accounts', function (Blueprint $table) {
            $table->decimal('amount', 15, 2)->default(0.00)->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('fund_accounts', function (Blueprint $table) {
            $table->dropColumn('amount');
        });
    }
};
```

- [ ] **Step 2: Update `FundAccount` Eloquent model**

In `src/backend/app/Models/Accounting/FundAccount.php`:
```php
    protected $fillable = [
        'company_id',
        'fund_code',
        'fund_name',
        'description',
        'amount',
        'user_id',
        'ledger_account_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];
```

---

### Task 2: Backend Controller & Seeder Updates

**Files:**
- Modify: `src/backend/app/Http/Controllers/Api/FundAccountController.php`
- Modify: `src/backend/database/seeders/AccountingSeeder.php`

- [ ] **Step 1: Update `FundAccountController`**

In `src/backend/app/Http/Controllers/Api/FundAccountController.php`:
Update `index()` fallback seed objects and `store()` validation to validate `'amount' => 'required|numeric|min:0'`.

- [ ] **Step 2: Update `AccountingSeeder.php`**

In `src/backend/database/seeders/AccountingSeeder.php`:
Attach `amount` values to seed funds (FND-101: 500,000.00, FND-202: 750,000.00, FND-303: 300,000.00).

---

### Task 3: Frontend Composable (`useAccounting.ts`) Updates

**Files:**
- Modify: `src/frontend/app/composables/useAccounting.ts`

- [ ] **Step 1: Update `FundAccount` interface and seed state**

In `src/frontend/app/composables/useAccounting.ts`:
Add `amount?: number` to `FundAccount` interface and update seed fund objects.

---

### Task 4: Frontend Page Updates (`funds.vue`)

**Files:**
- Modify: `src/frontend/app/pages/management/funds.vue`

- [ ] **Step 1: Add Live Search Input Bar**

Add `<input v-model="searchQuery" type="text" placeholder="Search fund by code, name, or description..." class="input-field pl-9 text-xs" />`.

- [ ] **Step 2: Add Amount Input to Create Modal**

Add `<UiInput v-model.number="newFund.amount" type="number" step="1000" label="Initial Fund Amount ($ USD) *" placeholder="500000" :required="true" />`.

- [ ] **Step 3: Render Prominent Amount Badge on Fund Cards**

Display initial fund balance badge: `<span class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-mono font-bold text-xs">${{ formatCurrency(fund.amount || 0) }}</span>`.

---

### Task 5: Verification & Testing

- [ ] **Step 1: Run `php artisan migrate` and `php artisan db:seed --class=AccountingSeeder`**
- [ ] **Step 2: Run `npm run build` in `src/frontend`**
- [ ] **Step 3: Test creating a new fund account with amount input**
- [ ] **Step 4: Test live search filtering in `funds.vue`**
