<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Accounting\LedgerAccountItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LedgerAccountItemController extends Controller
{
    public function index(Request $request)
    {
        $query = LedgerAccountItem::with(['ledgerAccount', 'fundAccount', 'accountItem', 'project', 'user', 'items']);

        if ($request->has('project_id')) {
            $query->where('project_id', $request->query('project_id'));
        }

        $entries = $query->orderBy('created_at', 'desc')->get();

        return response()->json($entries);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ledger_account_id' => 'required|integer',
            'fund_account_id' => 'nullable|integer',
            'project_id' => 'nullable|exists:projects,id',
            'account_item_id' => 'required|integer',
            'amount' => 'required|numeric|min:0.01',
            'transaction_type' => 'required|in:debit,credit',
            'description' => 'nullable|string|max:255',
            'items' => 'nullable|array',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'required|string|max:50',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.subtotal' => 'nullable|numeric|min:0',
        ]);

        // Validation Rule: If project_id is provided, fund_account_id must be allocated to the project
        if (!empty($validated['project_id']) && !empty($validated['fund_account_id'])) {
            $isAllocated = \App\Models\Accounting\ProjectFund::where('project_id', $validated['project_id'])
                ->where('fund_account_id', $validated['fund_account_id'])
                ->exists();

            if (!$isAllocated) {
                return response()->json([
                    'message' => 'The selected Fund Source is not allocated to this Project.',
                    'errors' => ['fund_account_id' => ['Fund Source must be allocated to the Project before posting journal entries.']]
                ], 422);
            }
        }

        $entry = DB::transaction(function () use ($validated, $request) {
            $newEntry = LedgerAccountItem::create([
                'ledger_account_id' => $validated['ledger_account_id'],
                'fund_account_id' => $validated['fund_account_id'] ?? null,
                'project_id' => $validated['project_id'] ?? null,
                'account_item_id' => $validated['account_item_id'],
                'amount' => $validated['amount'],
                'transaction_type' => $validated['transaction_type'],
                'description' => $validated['description'] ?? null,
                'user_id' => $request->user() ? $request->user()->id : 1,
            ]);

            if (!empty($validated['items'])) {
                foreach ($validated['items'] as $itemData) {
                    $qty = (float) $itemData['quantity'];
                    $prc = (float) $itemData['price'];
                    $subtotal = round($qty * $prc, 2);

                    $newEntry->items()->create([
                        'description' => $itemData['description'],
                        'quantity' => $qty,
                        'unit' => $itemData['unit'],
                        'price' => $prc,
                        'subtotal' => $subtotal,
                    ]);
                }
            }

            return $newEntry;
        });

        return response()->json([
            'message' => 'Journal transaction posted successfully',
            'data' => $entry->load(['ledgerAccount', 'fundAccount', 'accountItem', 'project', 'items']),
        ], 201);
    }

    public function summary()
    {
        $debits = (float) LedgerAccountItem::where('transaction_type', 'debit')->sum('amount');
        $credits = (float) LedgerAccountItem::where('transaction_type', 'credit')->sum('amount');

        return response()->json([
            'total_debits' => $debits,
            'total_credits' => $credits,
            'net_balance' => $debits - $credits,
        ]);
    }
}
