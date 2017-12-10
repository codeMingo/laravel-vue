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
        $username = isset($input['username']) ? strval($input['username']) : '';
        $password = isset($input['password']) ? strval($input['password']) : '';

        if (!$username || !$password) {
            return responseResult(false, [], '登录失败，必填字段不得为空');
        }

        $result['list'] = $this->loginRepository->login($username, $password);
        if (isset($result['list']['flag']) && !$result['list']['flag']) {
            return responseResult(false, [], $result['list']['message']);
        }
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
        $list   = $this->adminRepository->currentLoginAdmin();
        if (empty($list)) {
            return responseResult(true, $result);
        }
        $result['list'] = [
            'username'        => $list->username,
            'email'           => $list->email,
            'permission_text' => $this->adminPermissionRepository->getTextById($list->permission_id),
        ];
        return responseResult(true, $result);
    }

    /**
     * 退出
     * @return Array
     */
    public function logout()
    {
        $this->loginRepository->logout();
        return responseResult(true, [], '退出成功');
    }
}
