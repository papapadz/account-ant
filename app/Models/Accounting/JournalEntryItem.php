<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalEntryItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ledger_account_item_id',
        'description',
        'quantity',
        'unit',
        'price',
        'subtotal',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function ledgerAccountItem()
    {
        return $this->belongsTo(LedgerAccountItem::class, 'ledger_account_item_id');
    }
}
