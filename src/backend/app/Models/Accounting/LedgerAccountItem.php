<?php

namespace App\Models\Accounting;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LedgerAccountItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ledger_account_id',
        'fund_account_id',
        'project_id',
        'account_item_id',
        'amount',
        'transaction_type',
        'description',
        'user_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function ledgerAccount()
    {
        return $this->belongsTo(LedgerAccount::class, 'ledger_account_id');
    }

    public function fundAccount()
    {
        return $this->belongsTo(FundAccount::class, 'fund_account_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function accountItem()
    {
        return $this->belongsTo(AccountItem::class, 'account_item_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
