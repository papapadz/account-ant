<?php

namespace App\Models\Accounting;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectFund extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'fund_account_id',
        'initial_amount',
        'date_received',
        'user_id',
    ];

    protected $casts = [
        'initial_amount' => 'decimal:2',
        'date_received' => 'date:Y-m-d',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function fundAccount()
    {
        return $this->belongsTo(FundAccount::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
