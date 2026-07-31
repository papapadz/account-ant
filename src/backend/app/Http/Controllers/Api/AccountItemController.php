<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Accounting\AccountItem;
use Illuminate\Http\Request;

class AccountItemController extends Controller
{
    public function index()
    {
        $items = AccountItem::with('ledgerAccount')->get();

        // if ($items->isEmpty()) {
        //     $revAcc = \App\Models\Accounting\LedgerAccount::where('account_code', '4010-REV')->first()
        //         ?? \App\Models\Accounting\LedgerAccount::first();
        //     $equipAcc = \App\Models\Accounting\LedgerAccount::where('account_code', '1500-EQUIP')->first()
        //         ?? \App\Models\Accounting\LedgerAccount::first();

        //     if ($revAcc) {
        //         AccountItem::firstOrCreate(
        //             ['item_code' => 'ITEM-ACC-01'],
        //             [
        //                 'item_name' => 'Client Subscription Payment',
        //                 'description' => 'Enterprise tier automated ledger subscription',
        //                 'ledger_account_id' => $revAcc->id,
        //                 'transaction_type' => 'credit'
        //             ]
        //         );
        //     }

        //     if ($equipAcc) {
        //         AccountItem::firstOrCreate(
        //             ['item_code' => 'ITEM-EXP-02'],
        //             [
        //                 'item_name' => 'Cloud Hosting Infrastructure',
        //                 'description' => 'AWS/GCP GPU cluster monthly compute fee',
        //                 'ledger_account_id' => $equipAcc->id,
        //                 'transaction_type' => 'debit'
        //             ]
        //         );
        //     }

        //     $items = AccountItem::with('ledgerAccount')->get();
        // }

        return response()->json($items);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_code' => 'required|string|max:20',
            'item_name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'ledger_account_id' => 'nullable|integer',
            'transaction_type' => 'nullable|in:debit,credit',
        ]);

        $item = AccountItem::create($validated);

        return response()->json([
            'message' => 'Account item created successfully',
            'data' => $item->load('ledgerAccount'),
        ], 201);
    }
    public function updateStatus(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:active,inactive,archived',
        ]);

        $item = \App\Models\Accounting\AccountItem::findOrFail($id);
        $item->update(['status' => $validated['status']]);

        return response()->json([
            'status' => 'success',
            'message' => 'Account item status updated successfully',
            'data' => $item->load('ledgerAccount'),
        ]);
    }
}
