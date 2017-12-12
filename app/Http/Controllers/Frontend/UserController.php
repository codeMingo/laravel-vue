<?php
namespace App\Http\Controllers\Frontend;

use App\Servers\Frontend\UserServer;
use Illuminate\Http\Request;

class UserController extends CommonController
{

    public function __construct(UserServer $userServer)
    {
        parent::__construct();
        $this->server = $userServer;
    }

    // 用户信息
    public function show()
    {
        $result = $this->server->show();
        return $this->responseResult($result);
    }

    // 当前用户
    public function currentUser()
    {
        $result = $this->server->currentUser();
        return $this->responseResult($result);
    }

    // 更改用户资料
    public function update(Request $request)
    {
        $input  = $request->input('data');
        $result = $this->server->update($input);
        return $this->responseResult($result);
    }

    // 收藏列表
    public function collectLists(Request $request)
    {
        $input  = json_decode($request->input('data'), true);
        $result = $this->server->collectLists($input);
        return $this->responseResult($result);
    }
}
