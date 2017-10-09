<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('guest', ['except' => 'logout']);
        // $this->middleware('auth', ['only' => ['adminLogout']]);
        // $this->beforeFilter('csrf', array('on' => 'post'));
    }

    // 后台登录界面
    public function index()
    {
        return view('backend.index');
    }

    // 后台登录
    public function adminLogin(Request $request)
    {
        $input  = $request->input('data');
        $result = \App\Repositories\Backend\LoginRepository::getInstance()->login($input);
        return response()->json($result);
    }

    // 后台注销
    public function adminLogout()
    {
        $result = \App\Repositories\Backend\LoginRepository::getInstance()->logout();
        return response()->json($result);
    }

    // 后台重置密码
    public function adminReset(Request $request)
    {
        $input  = $request->input('data');
        $result = \App\Repositories\Backend\LoginRepository::getInstance()->reset($input);
        return response()->json($result);
    }

    //前台获取初始用户数据
    public function loginStatus(Request $request)
    {
        $result = \App\Repositories\Frontend\LoginRepository::getInstance()->loginStatus();
        return response()->json($result);
    }

    // 前台登录
    public function userLogin(Request $request)
    {
        $input  = $request->input('data');
        $result = \App\Repositories\Frontend\LoginRepository::getInstance()->login($input);
        return response()->json($result);
    }

    // 前台注销
    public function userLogout()
    {
        $result = \App\Repositories\Frontend\LoginRepository::getInstance()->logout();
        return response()->json($result);
    }

    // 前台重置密码
    public function userReset(Request $request)
    {
        $input  = $request->input('data');
        $result = App\Repositories\Frontend\LoginRepository::getInstance()->reset($input);
        return response()->json($result);
    }
}
