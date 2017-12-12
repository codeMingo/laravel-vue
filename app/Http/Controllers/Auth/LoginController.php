<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Servers\Backend\LoginServer as AdminLoginServer;
use App\Servers\Frontend\LoginServer as UserLoginServer;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
     */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    public $adminLoginServer;
    public $userServer;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(AdminLoginServer $adminLoginServer, UserLoginServer $userLoginServer)
    {
        $this->adminLoginServer = $adminLoginServer;
        $this->userServer           = $userLoginServer;
    }

    // 后台登录界面
    public function adminIndex()
    {
        return view('backend.index');
    }

    // 后台登录
    public function adminLogin(Request $request)
    {
        $input  = $request->input('data');
        $result = $this->adminLoginServer->login($input);
        return $this->responseResult($result);
    }

    // 后台注销
    public function adminLogout()
    {
        $result = $this->adminLoginServer->logout();
        return $this->responseResult($result);
    }

    // 后台重置密码
    public function adminReset(Request $request)
    {
        $input  = $request->input('data');
        $result = $this->adminLoginServer->reset($input);
        return $this->responseResult($result);
    }

    // 后台获取初始用户数据
    public function adminLoginStatus(Request $request)
    {
        $result = $this->adminLoginServer->loginStatus();
        return $this->responseResult($result);
    }

    // 前台登录界面
    public function index()
    {
        return view('frontend.index');
    }

    // 前台登录
    public function userLogin(Request $request)
    {
        $input  = $request->input('data');
        $result = $this->userServer->login($input);
        return $this->responseResult($result);
    }

    // 前台注销
    public function userLogout()
    {
        $result = $this->userServer->logout();
        return $this->responseResult($result);
    }

    // 前台重置密码
    public function userReset(Request $request)
    {
        $input  = $request->input('data');
        $result = $this->userServer->reset($input);
        return $this->responseResult($result);
    }

    // 前台获取初始用户数据
    public function loginStatus(Request $request)
    {
        $result = $this->userServer->loginStatus();
        return $this->responseResult($result);
    }
}
