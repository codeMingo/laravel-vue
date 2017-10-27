<?php

namespace App\Models;

class ArticleRead extends Base
{

    protected $fillable = [
        'user_id', 'article_id', 'ip_address',
    ];

    // 关联用户表
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
