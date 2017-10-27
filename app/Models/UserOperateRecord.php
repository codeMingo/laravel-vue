<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserOperateRecord extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'action', 'params', 'text', 'ip_address', 'status'
    ];
}
