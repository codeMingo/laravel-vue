<?php
namespace App\Http\Controllers\Frontend;

use App\Repositories\Frontend\CategoryRepository;
use Illuminate\Http\Request;

class CategoryController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    // 列表
    public function articleCategoryLists(Request $request)
    {
        $result = CategoryRepository::getInstance()->getArticleCategoryLists();
        return response()->json($result);
    }
}
