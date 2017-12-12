<?php
namespace App\Http\Controllers\Frontend;

use App\Servers\Frontend\ArticleServer;
use Illuminate\Http\Request;

class ArticleController extends CommonController
{

    public function __construct(ArticleServer $articleServer)
    {
        parent::__construct();
        $this->server = $articleServer;
    }

    // 文章列表
    public function lists(Request $request)
    {
        $input  = json_decode($request->input('data'), true);
        $result = $this->server->lists($input);
        return $this->responseResult($result);
    }

    // 文章详情
    public function detail($id)
    {
        $result = $this->server->detail($id);
        return $this->responseResult($result);
    }

    // 点赞 or 反对 or 收藏
    public function interactive(Request $request, $id)
    {
        $input  = $request->input('data');
        $result = $this->server->interactive($id, $input);
        return $this->responseResult($result);
    }

    // 评论 or 回复
    public function comment(Request $request, $id)
    {
        $input  = $request->input('data');
        $result = $this->server->comment($id, $input);
        return $this->responseResult($result);
    }
}
