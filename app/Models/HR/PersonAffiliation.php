<?php

namespace App\Models\HR;

use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PersonAffiliation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'person_id',
        'company_id',
        'affiliation_level',
        'employment_status',
        'employee_id',
        'position_id',
        'is_head',
    ];

    protected $casts = [
        'is_head' => 'boolean',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }
}
