<?php
namespace App\Http\Controllers\Frontend;

use App\Repositories\Frontend\VideoRepository;
use Illuminate\Http\Request;

class VideoController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    // 视频列表
    public function lists()
    {
        $input  = json_decode($request->input('data'), true);
        $result = VideoRepository::getInstance()->getVideoLists($input);
        return response()->json($result);
    }

    // 视频详情
    public function detail($video_id)
    {
        $result = VideoRepository::getInstance()->getVideoDetail($video_id);
        return response()->json($result);
    }

    // 获取视频评论列表
    public function commentLists($video_id)
    {
        $result = VideoRepository::getInstance()->commentLists($video_id);
        return response()->json($result);
    }

    // 点赞 or 反对 or 收藏 视频
    public function interactive(Request $request, $video_id)
    {
        $input  = $request->input('data');
        $result = VideoRepository::getInstance()->interactive($video_id, $input);
        return response()->json($result);
    }

    // 评论 or 回复 视频
    public function comment(Request $request, $video_id)
    {
        $input  = $request->input('data');
        $result = VideoRepository::getInstance()->comment($video_id, $input);
        return response()->json($result);
    }
}
