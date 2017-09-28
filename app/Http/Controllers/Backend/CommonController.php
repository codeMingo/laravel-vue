<?php

namespace App\Http\Controllers\Backend;

use App\Repositories\Backend\CommonRepository;
use Illuminate\Http\Request;

class CommonController extends BaseController
{

    // 上传图片
    public function uploadImage(Request $request)
    {
        $input  = $request->file('file');
        $result = CommonRepository::getInstance()->uploadImage($input);
        return response()->json($result);
    }
}
