<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Backend\LoginRepository as AdminLoginRepository;
use App\Repositories\Frontend\LoginRepository as UserLoginRepository;

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


    public $adminLoginRepository;
    public $userLoginRepository;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(AdminLoginRepository $adminLoginRepository, UserLoginRepository $userLoginRepository)
    {
        $this->adminLoginRepository = $adminLoginRepository;
        $this->userLoginRepository = $userLoginRepository;
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
        $result = $this->adminLoginRepository->login($input);
        return response()->json($result);
    }

    // 后台注销
    public function adminLogout()
    {
        $result = $this->adminLoginRepository->logout();
        return response()->json($result);
    }

    // 后台重置密码
    public function adminReset(Request $request)
    {
        $input  = $request->input('data');
        $result = $this->adminLoginRepository->reset($input);
        return response()->json($result);
    }

    // 后台获取初始用户数据
    public function adminLoginStatus(Request $request)
    {
        $result = $this->adminLoginRepository->loginStatus();
        return response()->json($result);
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
        $result = $this->userLoginRepository->login($input);
        return response()->json($result);
    }

    // 前台注销
    public function userLogout()
    {
        $result = $this->userLoginRepository->logout();
        return response()->json($result);
    }

    // 前台重置密码
    public function userReset(Request $request)
    {
        $input  = $request->input('data');
        $result = $this->userLoginRepository->reset($input);
        return response()->json($result);
    }

    // 前台获取初始用户数据
    public function loginStatus(Request $request)
    {
        $result = $this->userLoginRepository->loginStatus();
        return response()->json($result);
    }
}
