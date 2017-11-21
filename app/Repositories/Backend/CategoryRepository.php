<?php
namespace App\Repositories\Backend;

use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategoryRepository extends BaseRepository
{

    /**
     * 获取文章菜单
     * @return Object
     */
    public function getArticleCategories()
    {
        $article_category_value = DB::table('dicts')->where('code', 'category')->where('text_en', 'article')->where('status', 1)->value('value');
        return Category::where('category_type', $article_category_value)->where('status', 1)->get();
    }
}
