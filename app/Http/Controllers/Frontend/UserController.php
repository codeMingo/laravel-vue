<?php
namespace App\Http\Controllers\Frontend;

use App\Repositories\Frontend\UserRepository;
use Illuminate\Http\Request;

class UserController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    // 用户信息
    public function show()
    {
        $result = UserRepository::getInstance()->show();
        return response()->json($result);
    }

    // 个人中心
    public function index()
    {
        $result = UserRepository::getInstance()->index();
        return response()->json($result);
    }

    // 更改用户资料
    public function update(Request $request)
    {
        $input  = $request->input('data');
        $result = UserRepository::getInstance()->update($input);
        return response()->json($result);
    }

    // 收藏列表
    public function collect(Request $request)
    {
        $input  = json_decode($request->input('data'), true);
        $result = UserRepository::getInstance()->collect($input);
        return response()->json($result);
    }
}
