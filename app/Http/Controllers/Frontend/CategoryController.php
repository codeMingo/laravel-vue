<?php
namespace App\Http\Controllers\Frontend;

use App\Servers\Frontend\CategoryServer;
use Illuminate\Http\Request;

class CategoryController extends CommonController
{

    public function __construct(CategoryServer $categoryServer)
    {
        parent::__construct();
        $this->server = $categoryServer;
    }

    // 文章菜单列表
    public function articleCategoryLists(Request $request)
    {
        $result = $this->server->getArticleCategoryLists();
        return $this->responseResult($result);
    }
}
