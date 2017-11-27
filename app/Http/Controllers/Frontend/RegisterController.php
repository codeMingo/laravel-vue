<?php
namespace App\Http\Controllers\Frontend;

use App\Repositories\Frontend\RegisterRepository;
use Illuminate\Http\Request;

class RegisterController extends BaseController
{

    // 创建用户
    public function register(Request $request)
    {
        $input  = json_decode($request->input('data'), true);
        $result = RegisterRepository::getInstance()->register($input);
        return response()->json($result);
    }

    // 激活用户
    public function active(Request $request)
    {
        $input  = json_decode($request->input('data'), true);
        $result = RegisterRepository::getInstance()->active($input);
        return response()->json($result);
    }
}
