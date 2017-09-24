<?php
namespace App\Repositories\Backend;

use App\Models\Admin;
use Illuminate\Support\Facades\Auth;

class LoginRepository extends BaseRepository
{

    /**
     * 登录
     * @param  Array $data    登录信息
     * @param  Request $request
     * @return Array
     */
    public function login($data, $request)
    {
        $loginData = [
            'username' => $data['username'],
            'password' => $data['password'],
        ];
        if (!Auth::guard('admin')->attempt($loginData)) {
            return [
                'status'  => Parent::ERROR_STATUS,
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
        $updateData = [
            'last_login_ip'   => $request->getClientIp(),
            'last_login_time' => date('Y-m-d H:i:s', time()),
        ];
        $updateResult = Admin::where('id', $adminList->id)->update($updateData);
        if (!$updateResult) {
            return [
                'status' => Parent::ERROR_STATUS,
                'data' => [],
                'message' => '登录失败，发生未知错误'
            ];
        }
        $returnData['data'] = [
            'username' => $adminList->username,
            'email'    => $adminList->email,
        ];
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => $returnData,
            'message' => '登录成功'
        ];
    }

    public function reset($input)
    {

    }

    /**
     * 退出
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
