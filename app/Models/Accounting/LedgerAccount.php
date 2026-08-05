<?php

namespace App\Models\Accounting;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LedgerAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'account_code',
        'account_name',
        'description',
        'status',
        'fund_account_id',
        'user_id',
        'ledger_account_id',
    ];

    public function fundAccount()
    {
        return $this->belongsTo(FundAccount::class, 'fund_account_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function journalEntries()
    {
        return $this->hasMany(LedgerAccountItem::class, 'ledger_account_id');
    }

    public function accountItems()
    {
        return $this->hasMany(AccountItem::class, 'ledger_account_id');
    }
}
