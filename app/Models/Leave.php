<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Leave extends Base
{
    use SoftDeletes;

    protected $fillable = [
        'parent_id', 'user_id', 'content', 'is_audit', 'ip_address', 'status',
    ];

    // 关联用户表
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
