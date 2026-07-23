<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Accounting\LedgerAccount;
use Illuminate\Http\Request;

class LedgerAccountController extends Controller
{
    public function index()
    {
        $accounts = LedgerAccount::with('fundAccount')->get();

        if ($accounts->isEmpty()) {
            LedgerAccount::create([
                'account_code' => '1010-CASH',
                'account_name' => 'Cash & Cash Equivalents',
                'description' => 'Operating bank deposits and liquid treasury holdings',
                'fund_account_id' => 1,
                'user_id' => 1,
                'ledger_account_id' => 1,
            ]);
            LedgerAccount::create([
                'account_code' => '4010-REV',
                'account_name' => 'SaaS Subscription Revenue',
                'description' => 'Recurring platform API usage revenue',
                'fund_account_id' => 1,
                'user_id' => 1,
                'ledger_account_id' => 1,
            ]);
            $accounts = LedgerAccount::with('fundAccount')->get();
        }

        return response()->json($accounts);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_code' => 'required|string|max:20',
            'account_name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'fund_account_id' => 'required|integer',
        ]);

        $account = LedgerAccount::create([
            'account_code' => $validated['account_code'],
            'account_name' => $validated['account_name'],
            'description' => $validated['description'] ?? null,
            'fund_account_id' => $validated['fund_account_id'],
            'user_id' => 1,
            'ledger_account_id' => 1,
        ]);

        return response()->json([
            'message' => 'Ledger account created successfully',
            'data' => $account,
        ], 201);
    }

    public function destroy($id)
    {
        $account = LedgerAccount::findOrFail($id);
        $account->delete();
        return response()->json(['message' => 'Ledger account deleted successfully']);
    }
}
