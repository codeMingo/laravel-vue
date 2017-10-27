<?php

namespace App\Models;

class AdminLoginRecord extends Base
{
    protected $fillable = [
        'admin_id', 'params', 'text', 'ip_address', 'status',
    ];
}
