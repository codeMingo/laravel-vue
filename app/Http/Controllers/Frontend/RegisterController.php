<?php
namespace App\Http\Controllers\Frontend;

use App\Servers\Frontend\RegisterServer;
use Illuminate\Http\Request;

class RegisterController extends BaseController
{

    public $server;

    public function __construct(RegisterServer $registerServer)
    {
        parent::__construct();
        $this->server = $registerServer;
    }

    // 创建用户
    public function register(Request $request)
    {
        $input  = $request->input('data');
        $result = $this->server->register($input);
        return response()->json($result);
    }

    // 激活用户
    public function active(Request $request)
    {
        $input = $request->all();
        $result =  $this->server->active($input);
        return view('frontend.active', [
            'status' => $result['status'],
            'message' => $result['message'],
        ]);
    }

    // 发送激活邮件
    public function sendActiveEmail(Request $request)
    {
        $input  = $request->input('data');
        $result = $this->server->sendActiveEmail($input);
        return response()->json($result);
    }
}
