<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Base
{

    use SoftDeletes;

    protected $fillable = [
        'category_id', 'title', 'simple_instrution', 'auther', 'tag_include', 'source', 'is_audit', 'status',
    ];

    // 关联所有的评论
    public function videoList()
    {
        return $this->hasMany('App\Models\VideoList', 'video_id', 'id');
    }
}
