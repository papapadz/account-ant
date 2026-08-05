<?php

namespace App\Models\Accounting;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountsPayable extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'accounts_payables';

    protected $fillable = [
        'ledger_account_item_id',
        'name',
        'amount',
        'status',
        'due_date',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date:Y-m-d',
    ];

    public function ledgerAccountItem()
    {
        return $this->belongsTo(LedgerAccountItem::class, 'ledger_account_item_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
