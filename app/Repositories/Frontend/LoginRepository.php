<?php
namespace App\Repositories\Frontend;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginRepository extends BaseRepository
{

    /**
     * 登录
     * @param  Array $data    登录信息
     * @param  Request $request
     * @return Array
     */
    public function login($input, $request)
    {
        if (strpos($input['account'], '@')) {
            //邮箱登录
            $flag = Auth::guard('web')->attempt(['email' => $input['account'], 'password' => $input['password'], 'active' => 1, 'status' => 1], $input['remember']);
        } else {
            $flag = Auth::guard('web')->attempt(['username' => $input['account'], 'password' => $input['password']]);
        }
        if (!$flag) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '用户名或密码错误',
            ];
        }
        $user         = Auth::guard('web')->user();
        file_put_contents('e:/user.txt', json_encode($user));
        $updateResult = User::where('id', $user['id'])->update([
            'last_login_time' => date('Y-m-d H:i:s', time()),
            'last_login_ip'   => $request->getClientIp(),
        ]);
        $resultData['data'] = [
            'username' => $user['username'],
            'email'    => $user['email'],
            'face'    => $user['face'],
        ];
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => $resultData,
            'message' => '登录成功',
        ];
    }

    public function reset($input)
    {

    }

    /**
     * 获取用户初始登录状态
     * @return Array
     */
    public function loginStatus()
    {
        $resultData = [];
        if (Auth::guard('web')->check()) {
            $userList = Auth::guard('web')->user();
            $userData = [
                'username' => $userList->username,
                'email' => $userList->email,
                'face' => $userList->face
            ];
            $resultData['data'] = $userData;
        }
        return [
            'status' => Parent::SUCCESS_STATUS,
            'data' => $resultData,
            'message' => '获取成功',
        ];
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
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'message' => '退出成功',
        ];
    }
}
