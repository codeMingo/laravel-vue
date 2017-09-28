<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleComment extends Model
{

    // 关联用户表
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
