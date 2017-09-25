<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Repositories\Frontend\ArticleRepository;
class ArticleController extends BaseController
{

    // 文章列表
    public function lists(Request $request)
    {
        $input = $request->input('data');
        $result = ArticleRepository::getInstance()->lists($input);
        return response()->json($result);
    }

    // 文章详情
    public function detail($id)
    {
        $result = ArticleRepository::getInstance()->detail($id);
        return response()->json($result);
    }

    // 点赞 or 反对 or 收藏
    public function interactive(Request $request, $id)
    {
        $input = $request->input('data');
        $result = ArticleRepository::getInstance()->interactive($input, $id);
        return response()->json($result);
    }

    // 评论 or 回复
    public function comment(Request $request)
    {
        $input = $request->input('data');
        $result = ArticleRepository::getInstance()->comment($input);
        return response()->json($result);
    }
}
