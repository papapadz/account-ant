<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_code',
        'item_name',
        'description',
    ];
}
