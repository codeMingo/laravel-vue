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

    // 关联所有的互动
    public function interactives()
    {
        return $this->hasMany('App\Models\ArticleInteractive');
    }



    /**
     * 文章列表
     * @param  Array $searchForm [catetory_id, title]
     * @return Object
     */
    public static function lists($searchForm)
    {
        $whereParams = [];
        if (isset($searchForm['category_id']) && !empty($searchForm['category_id'])) {
            $whereParams['category_id'] = $searchForm['category_id'];
        }
        $query = Article::where($whereParams);
        if (isset($searchForm['title']) && $searchForm['title'] !== '') {
            $query->where('title', 'like', '%' . $searchForm['title'] . '%');
        }
        return $query->paginate(config('blog.pageSize'));
    }
}
