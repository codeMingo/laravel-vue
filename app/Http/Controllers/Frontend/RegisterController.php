<?php
namespace App\Http\Controllers\Frontend;

use App\Repositories\Frontend\RegisterRepository;
use Illuminate\Http\Request;

class RegisterController extends BaseController
{

    // 创建用户
    public function register(Request $request)
    {
        $input  = $request->input('data');
        $result = RegisterRepository::getInstance()->register($input);
        return response()->json($result);
    }

    // 激活用户
    public function active(Request $request)
    {
        $input = $request->all();
        $result =  RegisterRepository::getInstance()->active($input);
        return view('frontend.active', [
            'status' => $result['status'],
            'message' => $result['message'],
        ]);
    }

    // 发送激活邮件
    public function sendActiveEmail(Request $request)
    {
        $input  = $request->input('data');
        $result = RegisterRepository::getInstance()->sendActiveEmail($input);
        return response()->json($result);
    }
}
