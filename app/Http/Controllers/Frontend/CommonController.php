<?php

namespace App\Http\Controllers\Frontend;

use App\Repositories\Frontend\CommonRepository;
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

    // 发送邮件
    public function sendEmail(Request $request)
    {
        $input  = $request->file('data');
        $result = CommonRepository::getInstance()->sendEmail($input);
        return response()->json($result);

    }
}
