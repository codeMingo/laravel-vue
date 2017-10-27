<?php

namespace App\Models;

class AdminOperateRecord extends Base
{
    protected $fillable = [
        'admin_id', 'action', 'params', 'text', 'ip_address', 'status',
    ];
}
