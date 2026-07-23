<?php

namespace Database\Seeders;

use App\Models\Accounting\AccountItem;
use App\Models\Accounting\FundAccount;
use App\Models\Accounting\LedgerAccount;
use App\Models\Accounting\LedgerAccountItem;
use App\Models\Accounting\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class LedgerItemSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $userId = $user ? $user->id : 1;

        $cashAcc = LedgerAccount::where('account_code', '1010-CASH')->first();
        $equipAcc = LedgerAccount::where('account_code', '1500-EQUIP')->first();
        $salaryAcc = LedgerAccount::where('account_code', '5010-SALARY')->first();
        $revAcc = LedgerAccount::where('account_code', '4010-REV')->first();

        $fund1 = FundAccount::where('fund_code', 'FND-101')->first();
        $fund2 = FundAccount::where('fund_code', 'FND-202')->first();

        $itemSub = AccountItem::where('item_code', 'ITEM-ACC-01')->first();
        $itemHost = AccountItem::where('item_code', 'ITEM-EXP-02')->first();
        $itemPay = AccountItem::where('item_code', 'ITEM-PAY-03')->first();

        $project1 = Project::where('name', 'LIKE', '%Smart Transit%')->first();
        $project2 = Project::where('name', 'LIKE', '%FinTech%')->first();

        $entries = [
            [
                'ledger_account_id' => $cashAcc ? $cashAcc->id : 10,
                'fund_account_id' => $fund1 ? $fund1->id : 1,
                'project_id' => null,
                'account_item_id' => $itemSub ? $itemSub->id : 1,
                'amount' => 125000.00,
                'transaction_type' => 'debit',
                'description' => 'Enterprise license payment received from FinCorp Ltd',
                'user_id' => $userId,
                'created_at' => '2026-07-20 09:30:00',
            ],
            [
                'ledger_account_id' => $revAcc ? $revAcc->id : 40,
                'fund_account_id' => $fund1 ? $fund1->id : 1,
                'project_id' => null,
                'account_item_id' => $itemSub ? $itemSub->id : 1,
                'amount' => 125000.00,
                'transaction_type' => 'credit',
                'description' => 'Recognized SaaS revenue from FinCorp contract',
                'user_id' => $userId,
                'created_at' => '2026-07-20 09:30:00',
            ],
            [
                'ledger_account_id' => $equipAcc ? $equipAcc->id : 20,
                'fund_account_id' => $fund2 ? $fund2->id : 2,
                'project_id' => $project1 ? $project1->id : 1,
                'account_item_id' => $itemHost ? $itemHost->id : 2,
                'amount' => 85000.00,
                'transaction_type' => 'debit',
                'description' => 'Transit fare processing server rack deployment for QC Central station',
                'user_id' => $userId,
                'created_at' => '2026-07-15 10:00:00',
            ],
            [
                'ledger_account_id' => $cashAcc ? $cashAcc->id : 10,
                'fund_account_id' => $fund1 ? $fund1->id : 1,
                'project_id' => $project1 ? $project1->id : 1,
                'account_item_id' => $itemSub ? $itemSub->id : 1,
                'amount' => 150000.00,
                'transaction_type' => 'debit',
                'description' => 'Initial government milestone grant payment received',
                'user_id' => $userId,
                'created_at' => '2026-07-05 14:30:00',
            ],
            [
                'ledger_account_id' => $salaryAcc ? $salaryAcc->id : 30,
                'fund_account_id' => $fund1 ? $fund1->id : 1,
                'project_id' => $project2 ? $project2->id : 2,
                'account_item_id' => $itemPay ? $itemPay->id : 3,
                'amount' => 40000.00,
                'transaction_type' => 'debit',
                'description' => 'AI model fine-tuning team engineering sprint compensation',
                'user_id' => $userId,
                'created_at' => '2026-07-18 16:45:00',
            ],
        ];

        foreach ($entries as $entryData) {
            LedgerAccountItem::create($entryData);
        }
    }
}
