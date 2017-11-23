<?php
namespace App\Repositories\Frontend;

use App\Models\Category;

class CategoryRepository extends BaseRepository
{
    /**
     * 列表
     * @param  Array $input [search]
     * @return Array
     */
    public function lists($input)
    {
        $result['lists'] = $this->getCategoryLists($input['search']);
        return $this->responseResult(true, $result);
    }

    /**
     * 列表
     * @param  Array $search [type]
     * @return Object
     */
    public function getCategoryLists($search)
    {
        $type = isset($search['type']) ? intval($search['type']) : 0;
        if (!$type) {
            return [];
        }
        $category_type = DictRepository::getInstance()->getValueByCodeAndTextEn('category', $type);
        return Category::where('category_type', $category_type)->where('status', 1)->get();
    }
}
