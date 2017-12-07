<?php
namespace App\Http\Controllers\Frontend;

use App\Repositories\Frontend\RegisterRepository;
use Illuminate\Http\Request;

class RegisterController extends BaseController
{

    public $repository;

    public function __construct(RegisterRepository $registerRepository)
    {
        parent::__construct();
        $this->repository = $registerRepository;
    }

    // 创建用户
    public function register(Request $request)
    {
        $input  = $request->input('data');
        $result = $this->repository->register($input);
        return response()->json($result);
    }

    // 激活用户
    public function active(Request $request)
    {
        $input = $request->all();
        $result =  $this->repository->active($input);
        return view('frontend.active', [
            'status' => $result['status'],
            'message' => $result['message'],
        ]);
    }

    // 发送激活邮件
    public function sendActiveEmail(Request $request)
    {
        $input  = $request->input('data');
        $result = $this->repository->sendActiveEmail($input);
        return response()->json($result);
    }
}
