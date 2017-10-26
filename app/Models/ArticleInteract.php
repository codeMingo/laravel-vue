<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleInteract extends Model
{

    protected $fillable = [
        'article_id', 'user_id', 'like', 'hate', 'collect'
    ];

    // 关联用户表
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
