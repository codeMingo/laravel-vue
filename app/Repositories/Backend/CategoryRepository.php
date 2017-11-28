<?php
namespace App\Repositories\Backend;

use App\Models\Category;

class CategoryRepository extends CommonRepository
{

    public function getCategoryLists($search)
    {
        $params = $this->parseParams('categories', $search);
        return Category::parseWheres($search)->get();
    }
}
