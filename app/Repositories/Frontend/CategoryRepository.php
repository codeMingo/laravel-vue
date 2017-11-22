<?php
namespace App\Repositories\Frontend;

use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategoryRepository extends BaseRepository
{
    /**
     * 列表
     * @param  Array $input [search_form]
     * @return Array
     */
    public function lists($input)
    {
        $result['lists'] = $this->getCategoryLists($input['search_from']);
        return $this->responseResult(true, $result);
    }

    /**
     * 列表
     * @param  Array $search_from [type]
     * @return Object
     */
    public function getCategoryLists($search_from)
    {
        $type = validateValue($search_from['type']);
        if (!$type) {
            return [];
        }
        $category_type = DictRepository::getInstance()->getValueByCodeAndTextEn('category', $type);
        return Category::where('category_type', $category_type)->where('status', 1)->get();
    }
}
