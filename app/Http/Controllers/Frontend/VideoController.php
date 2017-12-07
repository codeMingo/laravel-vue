<?php
namespace App\Http\Controllers\Frontend;

use App\Repositories\Frontend\VideoRepository;
use Illuminate\Http\Request;

class VideoController extends BaseController
{

    public $repository;

    public function __construct(VideoRepository $videoRepository)
    {
        parent::__construct();
        $this->repository = $videoRepository;
    }

    public function __construct()
    {
        parent::__construct();
    }

    // 视频列表
    public function lists()
    {
        $input  = json_decode($request->input('data'), true);
        $result = $this->repository->lists($input);
        return response()->json($result);
    }

    // 视频详情
    public function detail($video_id)
    {
        $result = $this->repository->getVideoDetail($video_id);
        return response()->json($result);
    }

    // 获取视频评论列表
    public function commentLists($video_id)
    {
        $result = $this->repository->commentLists($video_id);
        return response()->json($result);
    }

    // 点赞 or 反对 or 收藏 视频
    public function interactive(Request $request, $video_id)
    {
        $input  = $request->input('data');
        $result = $this->repository->interactive($video_id, $input);
        return response()->json($result);
    }

    // 评论 or 回复 视频
    public function comment(Request $request, $video_id)
    {
        $input  = $request->input('data');
        $result = $this->repository->comment($video_id, $input);
        return response()->json($result);
    }
}
