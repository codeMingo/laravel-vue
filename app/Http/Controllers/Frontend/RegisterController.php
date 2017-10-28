<?php

namespace App\Http\Controllers\Frontend;

use App\Repositories\Frontend\RegisterRepository;
use Illuminate\Http\Request;

class RegisterController extends BaseController
{

    // 创建用户
    public function createUser(Request $request)
    {
        $input  = $request->input('data');
        $result = RegisterRepository::getInstance()->createUser($input);
        return response()->json($result);
    }

    // 激活用户
    public function activeUser(Request $request)
    {
        $input  = $request->all();
        $result = RegisterRepository::getInstance()->activeUser($input);
        return response()->json($result);
    }
}
