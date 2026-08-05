<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeviceInfo extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'serial_number',
        'device_name',
        'device_location',
        'company_id'
    ];
}
