<?php

namespace Database\Seeders;

use App\Models\Accounting\AccountItem;
use App\Models\Accounting\FundAccount;
use App\Models\Accounting\LedgerAccount;
use App\Models\HR\Company;
use App\Models\User;
use Illuminate\Database\Seeder;

class AccountingSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create([
            'name' => 'System Admin',
            'email' => 'admin@accountant.io',
        ]);
        $company = Company::first();

        // 1. Seed Fund Accounts
        $fund1 = FundAccount::firstOrCreate(
            ['fund_code' => 'FND-101'],
            [
                'company_id' => $company ? $company->id : 1,
                'fund_name' => 'General Operating Fund',
                'description' => 'Primary corporate liquidity and daily operational expenses',
                'amount' => 500000.00,
                'user_id' => $user->id,
                'ledger_account_id' => 10,
            ]
        );

        $fund2 = FundAccount::firstOrCreate(
            ['fund_code' => 'FND-202'],
            [
                'company_id' => $company ? $company->id : 1,
                'fund_name' => 'Capital Expenditure & R&D Fund',
                'description' => 'Reserved capital',
                'amount' => 750000.00,
                'user_id' => $user->id,
                'ledger_account_id' => 20,
            ]
        );

        $fund3 = FundAccount::firstOrCreate(
            ['fund_code' => 'FND-303'],
            [
                'company_id' => $company ? $company->id : 1,
                'fund_name' => 'Payroll Reserve Fund',
                'description' => 'Dedicated fund for bi-weekly employee compensation',
                'amount' => 300000.00,
                'user_id' => $user->id,
                'ledger_account_id' => 30,
            ]
        );

        // 2. Seed Ledger Accounts
        $ledgerAccounts = [
            [
                'id' => 10,
                'account_code' => '1010-CASH',
                'account_name' => 'Cash & Cash Equivalents',
                'description' => 'Cash, payments and received funds',
                'fund_account_id' => $fund1->id,
                'user_id' => $user->id,
                'ledger_account_id' => 1,
            ],
            [
                'id' => 11,
                'account_code' => '1020-AR',
                'account_name' => 'Accounts Receivable',
                'description' => 'Invoiced customer subscriptions & enterprise contracts',
                'fund_account_id' => $fund1->id,
                'user_id' => $user->id,
                'ledger_account_id' => 1,
            ],
            [
                'id' => 20,
                'account_code' => '1500-CONS-SUPP',
                'account_name' => 'Construction Supplies and Equipment',
                'description' => 'Construction supplies, items and equipment needed for projects',
                'fund_account_id' => $fund2->id,
                'user_id' => $user->id,
                'ledger_account_id' => 2,
            ],
            [
                'id' => 30,
                'account_code' => '5010-SALARY',
                'account_name' => 'Salaries & Staff Expenses',
                'description' => 'Direct compensation, payroll tax & health benefits',
                'fund_account_id' => $fund3->id,
                'user_id' => $user->id,
                'ledger_account_id' => 3,
            ],
        ];

        foreach ($ledgerAccounts as $accData) {
            LedgerAccount::firstOrCreate(['account_code' => $accData['account_code']], $accData);
        }

        // 3. Seed Account Items
        $equipAcc = LedgerAccount::where('account_code', '1500-CONS-SUPP')->first();
        $salaryAcc = LedgerAccount::where('account_code', '5010-SALARY')->first();
        $cashAcc = LedgerAccount::where('account_code', '1010-CASH')->first();

        $items = [
            [
                'item_code' => 'ITEM-ACC-01',
                'item_name' => 'Client Payment',
                'description' => 'Payment for work and services provided',
                'ledger_account_id' => $cashAcc ? $cashAcc->id : LedgerAccount::first()->id,
                'transaction_type' => 'credit',
            ],
            [
                'item_code' => 'ITEM-PAY-03',
                'item_name' => 'Engineering Staff Payroll',
                'description' => 'Monthly engineering team salaries',
                'ledger_account_id' => $salaryAcc ? $salaryAcc->id : LedgerAccount::first()->id,
                'transaction_type' => 'debit',
            ],
            [
                'item_code' => 'ITEM-EQUIP-01',
                'item_name' => 'Construction Supplies and Equipment',
                'description' => 'Construction supplies, items and equipment needed for projects',
                'ledger_account_id' => $equipAcc ? $equipAcc->id : LedgerAccount::first()->id,
                'transaction_type' => 'debit',
            ],
        ];

        foreach ($items as $itemData) {
            AccountItem::firstOrCreate(['item_code' => $itemData['item_code']], $itemData);
        }
    }
}
