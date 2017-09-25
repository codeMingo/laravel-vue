<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{

    // 关联所有的评论
    public function comments()
    {
        return $this->hasMany('App\Models\ArticleComment');
    }

    // 获取文章列表
    public static function lists($searchForm)
    {
        $whereParams = [];
        if (issetAndNotEmpty($searchForm['category_id'])) {
            $whereParams['category_id'] = $searchForm['category_id'];
        }
        $query = Article::where($whereParams);
        if (issetAndNotEmpty($searchForm['title'])) {
            $query->where('title', 'like', '%' . $searchForm['title'] . '%');
        }
        return $query->paginate(config('blog.pageSize'));
    }
}
