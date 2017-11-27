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
    public function lists(Request $request)
    {
        $input  = json_decode($request->input('data'), true);
        $result = CategoryRepository::getInstance()->lists($input);
        return response()->json($result);
    }
}
