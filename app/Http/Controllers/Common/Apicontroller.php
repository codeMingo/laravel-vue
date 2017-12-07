<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Repositories\Common\ApiRepository;
use Illuminate\Http\Request;

class Apicontroller extends Controller
{

    public $repository;

    public function __construct(ApiRepository $apiRepository)
    {
        $this->repository = $apiRepository;
    }

    // 获取七牛上传token
    public function uploadToken(Request $request)
    {
        $result = $this->repository->createToken();
        return response()->json($result);
    }

    // 刷新缓存
    public function refreshCache()
    {
        $result = $this->repository->refreshCache();
        return response()->json($result);
    }
}
