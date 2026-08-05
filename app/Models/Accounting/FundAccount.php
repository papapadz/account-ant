<?php

namespace App\Models\Accounting;

use App\Models\HR\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FundAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'fund_code',
        'fund_name',
        'description',
        'amount',
        'user_id',
        'ledger_account_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ledgerAccounts()
    {
        return $this->hasMany(LedgerAccount::class, 'fund_account_id');
    }
}
