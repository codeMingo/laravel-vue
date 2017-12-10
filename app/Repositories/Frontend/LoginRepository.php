<?php
namespace App\Repositories\Frontend;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginRepository extends CommonRepository
{

    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    /**
     * 登录
     * @param  String $account 登录帐号
     * @param  String $password 密码
     * @param  Boolean $remember 是否记住密码
     * @return Array
     */
    public function login($account, $password, $remember)
    {
        if (strpos($account, '@')) {
            //邮箱登录
            $flag = Auth::guard('web')->attempt(['email' => $account, 'password' => $password, 'active' => 1, 'status' => 1], $remember);
        } else {
            $flag = Auth::guard('web')->attempt(['username' => $account, 'password' => $password]);
        }
        if (!$flag) {
            return ['flag' => false, 'message' => '登录失败，用户名或密码错误'];
        }
        $user = Auth::guard('web')->user();
        $this->model->where('id', $user['id'])->update([
            'last_login_time' => date('Y-m-d H:i:s', time()),
            'last_login_ip'   => getClientIp(),
        ]);
        $result = [
            'username' => $user['username'],
            'email'    => $user['email'],
            'face'     => $user['face'],
        ];
        return $result;
    }

    public function reset($input)
    {

    }

    /**
     * 退出
     * @return Array
     */
    public function logout()
    {
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        }
        return true;
    }
}
