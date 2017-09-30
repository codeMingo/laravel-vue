<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminOperateRecord extends Model
{
    protected $fillable = [
        'admin_id', 'action', 'params', 'text', 'ip_address', 'status',
    ];
}
