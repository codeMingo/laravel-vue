<?php
namespace App\Repositories\Frontend;

use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategoryRepository extends BaseRepository
{

    /**
     * 根据字典的菜单类型获取某一类菜单列表
     * @param  String $text_en 字典表的text_en
     * @return Object
     */
    public function getListsByDictText($text_en)
    {
        $category_type = DB::table('dicts')->where('text_en', $text_en)->where('status', 1)->value('value');
        return Category::where('category_type', $category_type)->where('status', 1)->get();
    }
}
