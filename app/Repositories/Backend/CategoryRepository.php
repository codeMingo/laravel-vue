<?php
namespace App\Repositories\Backend;

use App\Models\Category;

class CategoryRepository extends CommonRepository
{

    public function __construct(Category $category)
    {
        parent::__construct($category);
    }

    public function getCategoryLists($search)
    {
        $params = $this->parseParams($search);

        return $this->model->parseWheres($search)->get();
    }
}
