<?php
namespace App\Servers\Backend;

use App\Repositories\Backend\AdminPermissionRepository;
use App\Repositories\Backend\AdminRepository;
use App\Repositories\Backend\LoginRepository;

class LoginServer extends CommonServer
{

    public function __construct(
        LoginRepository $loginRepository,
        AdminRepository $adminRepository,
        AdminPermissionRepository $adminPermissionRepository
    ) {
        $this->loginRepository           = $loginRepository;
        $this->adminRepository           = $adminRepository;
        $this->adminPermissionRepository = $adminPermissionRepository;
    }

    /**
     * 登录
     * @param  Array $input [account, password, remember]
     * @return Array
     */
    public function login($input)
    {
        $account  = isset($input['account']) ? strval($input['account']) : '';
        $password = isset($input['password']) ? strval($input['password']) : '';

        if (!$account || !$password) {
            return returnError('登录失败，必填字段不得为空');
        }

        $list = $this->loginRepository->login($account, $password);
        if (!$list) {
            return returnError('登录失败，账号或密码错误');
        }
        $result['list'] = [
            'username'        => $list['username'],
            'email'           => $list['email'],
            'permission_text' => $this->adminPermissionRepository->getTextById($list['permission_id']),
        ];
        return returnSuccess('登录成功', $result);
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
        $list   = $this->adminRepository->currentLogin();
        if (empty($list)) {
            return returnError('未登录');
        }
        $result['list'] = [
            'username'        => $list->username,
            'email'           => $list->email,
            'permission_text' => $this->adminPermissionRepository->getTextById($list->permission_id),
        ];
        return returnSuccess('已登录', $result);
    }

    /**
     * 退出
     * @return Array
     */
    public function logout()
    {
        $this->loginRepository->logout();
        return returnSuccess('退出成功');
    }
}
