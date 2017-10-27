<?php

namespace App\Models;

class ArticleInteract extends Base
{

    protected $fillable = [
        'article_id', 'user_id', 'like', 'hate', 'collect',
    ];

    // 关联用户表
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
