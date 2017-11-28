<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Repositories\Common\ApiRepository;
use Illuminate\Http\Request;

class Apicontroller extends Controller
{

    // 获取七牛上传token
    public function uploadToken(Request $request)
    {
        $result = ApiRepository::getInstance()->createToken();
        return response()->json($result);
    }

    // 刷新缓存
    public function refreshCache()
    {
        $result = ApiRepository::getInstance()->refreshCache();
        return response()->json($result);
    }
}
