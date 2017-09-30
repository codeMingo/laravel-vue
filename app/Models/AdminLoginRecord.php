<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminLoginRecord extends Model
{
    protected $fillable = [
        'admin_id', 'params', 'text', 'ip_address', 'status',
    ];
}
