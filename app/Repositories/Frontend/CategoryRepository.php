<?php
namespace App\Repositories\Frontend;

use App\Models\Category;

class CategoryRepository extends CommonRepository
{

    public function __construct(Category $category)
    {
        parent::__construct($category);
    }

    /**
     * 文章菜单列表
     * @return Array
     */
    public function articleCategoryLists()
    {
        $dicts  = $this->getRedisDictLists(['category' => ['article']]);
        $search = [
            'filter' => ['id', 'title'],
            'search' => [
                'status'        => 1,
                'category_type' => $dicts['category']['article'],
            ],
        ];
        $result = $this->getAllLists($search);
        return $result;
    }

    /**
     * 视频菜单列表
     * @return Array
     */
    public function videoCategoryLists()
    {
        $dicts  = $this->getRedisDictLists(['category' => ['video']]);
        $search = [
            'filter' => ['id', 'title'],
            'search' => [
                'status'        => 1,
                'category_type' => $dicts['category']['video'],
            ],
        ];
        $result = $this->getCategoryLists($search);
        return $result;
    }

    /**
     * 列表
     * @param  Array $search [type]
     * @return Object
     */
    public function getCategoryLists($search)
    {
        $default_search = [
            'search' => [
                'status' => 1,
            ],
        ];
        $search = array_merge($default_search, $search);
        return $this->getLists($search);
    }
}
