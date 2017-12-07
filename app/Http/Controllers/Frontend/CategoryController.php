<?php
namespace App\Http\Controllers\Frontend;

use App\Repositories\Frontend\CategoryRepository;
use Illuminate\Http\Request;

class CategoryController extends BaseController
{

    public $repository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        parent::__construct();
        $this->repository = $categoryRepository;
    }

    // 文章菜单列表
    public function articleCategoryLists(Request $request)
    {
        $result = $this->repository->getArticleCategoryLists();
        return response()->json($result);
    }
}
