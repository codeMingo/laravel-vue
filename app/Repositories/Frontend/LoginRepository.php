<?php
namespace App\Repositories\Frontend;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginRepository extends CommonRepository
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 登录
     * @param  Array $data [account, password, remember]
     * @return Array
     */
    public function login($input)
    {
        $account  = isset($input['account']) ? strval($input['account']) : '';
        $password = isset($input['password']) ? strval($input['password']) : '';
        $remember = isset($input['remember']) ? (bool) $input['remember'] : false;

        if (!$account || !$password) {
            return responseResult(false, [], '登录失败，必填字段不得为空');
        }
        if (strpos($account, '@')) {
            //邮箱登录
            $flag = Auth::guard('web')->attempt(['email' => $account, 'password' => $password, 'active' => 1, 'status' => 1], $remember);
        } else {
            $flag = Auth::guard('web')->attempt(['username' => $account, 'password' => $password]);
        }
        if (!$flag) {
            return responseResult(false, [], '登录失败，用户名或密码错误');
        }
        $user = Auth::guard('web')->user();
        User::where('id', $user['id'])->update([
            'last_login_time' => date('Y-m-d H:i:s', time()),
            'last_login_ip'   => getClientIp(),
        ]);
        $result['list'] = [
            'username' => $user['username'],
            'email'    => $user['email'],
            'face'     => $user['face'],
        ];
        return responseResult(true, $result, '登录成功');
    }

    public function reset($input)
    {

    }

    /**
     * 获取登录信息
     * @return Array
     */
    public function loginStatus()
    {
        $result = [];
        if (Auth::guard('web')->check()) {
            $userList = Auth::guard('web')->user();
            $userData = [
                'username' => $userList->username,
                'email'    => $userList->email,
                'face'     => $userList->face,
            ];
            $result['list'] = $userData;
        }
        return responseResult(true, $result);
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
        return responseResult(true, [], '退出成功');
    }
}
