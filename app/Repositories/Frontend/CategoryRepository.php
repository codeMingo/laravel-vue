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
    public function index($input)
    {
        $search          = isset($input['search']) ? (array) $input['search'] : [];
        $result['lists'] = $this->getCategoryLists($search);
        return $this->responseResult(true, $result);
    }

    /**
     * 列表
     * @param  Array $search [type]
     * @return Object
     */
    public function getCategoryLists($search)
    {
        $type = isset($search['type']) ? strval($search['type']) : 0;
        if (!$type) {
            return [];
        }
        $dicts                   = $this->getRedisDictLists(['category' => [$type]]);
        $search['category_type'] = $dicts['category']['article'];
        $search['status']        = 1;
        return Category::parseWhere($search)->get();
    }
}
