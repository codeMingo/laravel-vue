<?php
namespace App\Http\Controllers\Frontend;

use App\Repositories\Frontend\UserRepository;
use Illuminate\Http\Request;

class UserController extends BaseController
{

    public $repository;

    public function __construct(UserRepository $userRepository)
    {
        parent::__construct();
        $this->repository = $userRepository;
    }
    
    // 用户信息
    public function show()
    {
        $result = $this->repository->show();
        return response()->json($result);
    }

    // 个人中心
    public function index()
    {
        $result = $this->repository->index();
        return response()->json($result);
    }

    // 更改用户资料
    public function update(Request $request)
    {
        $input  = $request->input('data');
        $result = $this->repository->update($input);
        return response()->json($result);
    }

    // 收藏列表
    public function collect(Request $request)
    {
        $input  = json_decode($request->input('data'), true);
        $result = $this->repository->collect($input);
        return response()->json($result);
    }
}
