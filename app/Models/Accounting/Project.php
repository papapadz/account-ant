<?php

namespace App\Models\Accounting;

use App\Models\Address\City;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'user_id',
        'name',
        'description',
        'budget',
        'start_date',
        'end_date',
        'status',
        'client_name',
        'is_government',
        'city_id',
        'house_number',
        'street',
        'village',
        'barangay',
        'zip',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_government' => 'boolean',
    ];

    protected $appends = [
        'total_allocated_funds',
        'total_debits',
        'total_credits',
        'total_expenses',
        'running_balance',
        'budget_utilized_percentage',
        'is_over_budget',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function projectFunds()
    {
        return $this->hasMany(ProjectFund::class);
    }

    public function fundAccounts()
    {
        return $this->belongsToMany(FundAccount::class, 'project_funds')
            ->withPivot('initial_amount', 'user_id')
            ->withTimestamps();
    }

    public function journalEntries()
    {
        return $this->hasMany(LedgerAccountItem::class);
    }

    public function getTotalAllocatedFundsAttribute(): float
    {
        return (float) $this->projectFunds()->sum('initial_amount');
    }

    public function getTotalDebitsAttribute(): float
    {
        return (float) $this->journalEntries()->where('transaction_type', 'debit')->sum('amount');
    }

    public function getTotalCreditsAttribute(): float
    {
        return (float) $this->journalEntries()->where('transaction_type', 'credit')->sum('amount');
    }

    public function getTotalExpensesAttribute(): float
    {
        return $this->getTotalDebitsAttribute() - $this->getTotalCreditsAttribute();
    }

    public function getRunningBalanceAttribute(): float
    {
        return (float) $this->budget - $this->getTotalExpensesAttribute();
    }

    public function getBudgetUtilizedPercentageAttribute(): float
    {
        if ($this->budget <= 0) {
            return 0.0;
        }
        return round(($this->getTotalExpensesAttribute() / $this->budget) * 100, 2);
    }

    public function getIsOverBudgetAttribute(): bool
    {
        return $this->getTotalExpensesAttribute() > $this->budget;
    }
}
