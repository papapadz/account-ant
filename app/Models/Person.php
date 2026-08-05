<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Person extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'people';

    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'name_extension',
        'birth_date',
        'birth_place',
        'civil_status',
        'gender',
        'citizenship_id',
    ];

    public function affiliations()
    {
        return $this->hasMany(\App\Models\HR\PersonAffiliation::class, 'person_id');
    }
}
