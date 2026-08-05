<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_name',
        'business_description',
        'city_id',
        'business_classification',
        'business_scope',
        'street',
        'building_number',
        'barangay',
        'zip',
        'date_started',
        'date_ended',
        'is_government',
        'created_by',
    ];

    protected $casts = [
        'is_government' => 'boolean',
        'date_started' => 'date',
        'date_ended' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function fundAccounts()
    {
        return $this->hasMany(\App\Models\Accounting\FundAccount::class, 'company_id');
    }
}
