<?php

namespace App\Http\Controllers\Common;

use App\Repositories\Common\ApiRepository;
use Illuminate\Http\Request;

class Apicontroller extends BaseController
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
        return $this->responseResult($result);
    }

    // 刷新缓存
    public function refreshCache()
    {
        $result = $this->repository->refreshCache();
        return $this->responseResult($result);
    }
}
