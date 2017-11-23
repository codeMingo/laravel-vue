<?php

namespace App\Http\Controllers\Frontend;

use App\Repositories\Frontend\ArticleRepository;
use Illuminate\Http\Request;

class ArticleController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    // 文章列表
    public function lists(Request $request)
    {
        $input  = json_decode($request->input('data'), true);
        $result = ArticleRepository::getInstance()->lists($input);
        return response()->json($result);
    }

    // 文章详情
    public function detail($article_id)
    {
        $result = ArticleRepository::getInstance()->detail($article_id);
        return response()->json($result);
    }

    // 获取评论列表
    public function commentLists($article_id)
    {
        $result = ArticleRepository::getInstance()->commentLists($article_id);
        return response()->json($result);
    }

    // 点赞 or 反对 or 收藏
    public function interactive(Request $request, $article_id)
    {
        $input  = $request->input('data');
        $result = ArticleRepository::getInstance()->interactive($input, $article_id);
        return response()->json($result);
    }

    // 评论 or 回复
    public function comment(Request $request, $article_id)
    {
        $input  = $request->input('data');
        $result = ArticleRepository::getInstance()->comment($input, $article_id);
        return response()->json($result);
    }

    // 点赞 or 反对 or 收藏 详情
    public function interactiveDetail(Request $request, $id)
    {
        $input  = $request->input('data');
        $result = ArticleRepository::getInstance()->interactiveDetail($input, $id);
        return response()->json($result);
    }

    // 推荐文章
    public function recommendLists(Request $request)
    {
        $input  = $request->input('data');
        $result = ArticleRepository::getInstance()->recommendList($input);
        return response()->json($result);
    }

    // 获取我点赞 or 反对 or 收藏的文章
    public function interativeLists(Request $request)
    {
        $input  = $request->input('data');
        $result = ArticleRepository::getInstance()->interativeLists($input);
        return response()->json($result);
    }
}
