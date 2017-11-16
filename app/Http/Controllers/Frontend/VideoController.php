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
    public function index()
    {
        $result = VideoRepository::getInstance()->index();
        return response()->json($result);
    }
}
