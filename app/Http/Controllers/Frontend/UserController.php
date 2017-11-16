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

    public function mainShow()
    {
        $result = UserRepository::getInstance()->mainShow();
        return response()->json($result);
    }

    // 个人中心
    public function index()
    {
        $result = UserRepository::getInstance()->index();
        return response()->json($result);
    }

    // 更改用户资料
    public function updateUser(Request $request, $user_id)
    {
        $input  = $request->input('data');
        $result = UserRepository::getInstance()->updateUser($input, $user_id);
        return response()->json($result);
    }

    // 收藏列表
    public function collectLists(Request $request)
    {
        $input = json_decode($request->input('data'), true);
        $result = UserRepository::getInstance()->collectLists($input);
        return response()->json($result);
    }
}
