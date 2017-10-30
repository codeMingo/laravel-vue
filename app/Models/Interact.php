<?php

namespace App\Models;

class Interact extends Base
{

    protected $fillable = [
        'article_id', 'video_list_id', 'user_id', 'like', 'hate', 'collect',
    ];

    // 关联用户表
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function article()
    {
        return $this->belongsTo('App\Models\Article');
    }

    public function videoList()
    {
        return $this->belongsTo('App\Models\VideoList');
    }
}
