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

        if ($items->isEmpty()) {
            AccountItem::create(['item_code' => 'ITEM-ACC-01', 'item_name' => 'Client Subscription Payment', 'description' => 'Enterprise tier automated ledger subscription', 'ledger_account_id' => 40, 'transaction_type' => 'credit']);
            AccountItem::create(['item_code' => 'ITEM-EXP-02', 'item_name' => 'Cloud Hosting Infrastructure', 'description' => 'AWS/GCP GPU cluster monthly compute fee', 'ledger_account_id' => 20, 'transaction_type' => 'debit']);
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
            'transaction_type' => 'nullable|in:debit,credit',
        ]);

        $item = AccountItem::create($validated);

        return response()->json([
            'message' => 'Account item created successfully',
            'data' => $item->load('ledgerAccount'),
        ], 201);
    }
}
