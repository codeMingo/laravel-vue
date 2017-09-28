<?php
namespace App\Repositories\Backend;

use App\Models\Admin;
use Illuminate\Support\Facades\Auth;

class LoginRepository extends BaseRepository
{

    /**
     * 登录
     * @param  Array $data [username, password]
     * @param  Request $request
     * @return Array
     */
    public function login($input, $request)
    {
        $username = isset($input['username']) ? strval($input['username']) : '';
        $password = isset($input['password']) ? strval($input['password']) : '';

        if (!$username || !$password) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '必填字段不得为空',
            ];
        }

        if (!Auth::guard('admin')->attempt(['username' => $username, 'password' => $password])) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '用户名或密码错误',
            ];
        }
        $adminList = Auth::guard('admin')->user();
        if (!$adminList->status) {
            Auth::guard('admin')->logout();
            return [
                'status'  => Parent::ERROR_STATUS,
                'message' => '帐号被禁用',
            ];
        };

        $updateResult = Admin::where('id', $adminList->id)->update([
            'last_login_ip'   => $request->getClientIp(),
            'last_login_time' => date('Y-m-d H:i:s', time()),
        ]);
        if (!$updateResult) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '登录失败，发生未知错误',
            ];
        }
        $returnData['data'] = [
            'username' => $adminList->username,
            'email'    => $adminList->email,
        ];
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => $returnData,
            'message' => '登录成功',
        ];
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
        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        }
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'message' => '退出成功',
        ];
    }
}
