<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArticleComment extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'parent_id', 'article_id', 'user_id', 'content', 'is_audit', 'status',
    ];

    // 关联用户表
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
