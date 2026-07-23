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
                'user_id' => $user->id,
                'ledger_account_id' => 10,
            ]
        );

        $fund2 = FundAccount::firstOrCreate(
            ['fund_code' => 'FND-202'],
            [
                'company_id' => $company ? $company->id : 1,
                'fund_name' => 'Capital Expenditure & R&D Fund',
                'description' => 'Reserved capital for software automation & infrastructure expansion',
                'user_id' => $user->id,
                'ledger_account_id' => 20,
            ]
        );

        $fund3 = FundAccount::firstOrCreate(
            ['fund_code' => 'FND-303'],
            [
                'company_id' => $company ? $company->id : 1,
                'fund_name' => 'Payroll Reserve Fund',
                'description' => 'Dedicated fund for bi-weekly employee compensation & tax withholding',
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
                'description' => 'Operating bank deposits and liquid treasury holdings',
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
                'account_code' => '1500-EQUIP',
                'account_name' => 'Server & IT Infrastructure Assets',
                'description' => 'Hardware, high-performance compute nodes & software licenses',
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
            [
                'id' => 40,
                'account_code' => '4010-REV',
                'account_name' => 'SaaS Subscription Revenue',
                'description' => 'Recurring platform API usage revenue',
                'fund_account_id' => $fund1->id,
                'user_id' => $user->id,
                'ledger_account_id' => 1,
            ],
        ];

        foreach ($ledgerAccounts as $accData) {
            LedgerAccount::firstOrCreate(['account_code' => $accData['account_code']], $accData);
        }

        // 3. Seed Account Items
        $items = [
            [
                'id' => 1,
                'item_code' => 'ITEM-ACC-01',
                'item_name' => 'Client Subscription Payment',
                'description' => 'Enterprise tier automated ledger subscription',
            ],
            [
                'id' => 2,
                'item_code' => 'ITEM-EXP-02',
                'item_name' => 'Cloud Hosting Infrastructure',
                'description' => 'AWS/GCP GPU cluster monthly compute fee',
            ],
            [
                'id' => 3,
                'item_code' => 'ITEM-PAY-03',
                'item_name' => 'Engineering Staff Payroll',
                'description' => 'Monthly engineering team salary disbursement',
            ],
            [
                'id' => 4,
                'item_code' => 'ITEM-TAX-04',
                'item_name' => 'Corporate Tax Withholding',
                'description' => 'Quarterly state and federal tax remittance',
            ],
            [
                'id' => 5,
                'item_code' => 'ITEM-LIC-05',
                'item_name' => 'Database Security License',
                'description' => 'Annual database encryption key management service',
            ],
        ];

        foreach ($items as $itemData) {
            AccountItem::firstOrCreate(['item_code' => $itemData['item_code']], $itemData);
        }
    }
}
