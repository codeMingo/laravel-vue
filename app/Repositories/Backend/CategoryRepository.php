<?php
namespace App\Repositories\Backend;

use App\Models\Category;

class CategoryRepository extends CommonRepository
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getCategoryLists($search)
    {
        $params = $this->parseParams('categories', $search);

        return Category::parseWheres($search)->get();
    }
}
