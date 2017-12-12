<?php
namespace App\Servers\Frontend;

use App\Repositories\Frontend\LoginRepository;
use App\Repositories\Frontend\UserRepository;

class LoginServer extends CommonServer
{

    public function __construct(
        LoginRepository $loginRepository,
        UserRepository $userRepository
    ) {
        $this->loginRepository = $loginRepository;
        $this->userRepository = $userRepository;
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
        $remember = isset($input['remember']) ? (bool) $input['remember'] : false;

        if (!$account || !$password) {
            return ['code' => ['x00001', 'login']];
        }

        $list = $this->loginRepository->login($account, $password, $remember);
        if (!$list) {
            return ['code' => ['x00002', 'login']];
        }

        $result['list'] = [
            'username' => $list['username'],
            'email'    => $list['email'],
            'face'     => $list['face'],
        ];
        return ['登录成功', $result];
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
        $list   = $this->userRepository->currentLogin();
        if (empty($list)) {
            return ['未登录'];
        }
        $result['list'] = [
            'username' => $list->username,
            'email'    => $list->email,
            'face'     => $list->face,
            'sign'     => $list->sign,
        ];
        return ['已登录', $result];
    }

    /**
     * 退出
     * @return Array
     */
    public function logout()
    {
        $this->loginRepository->logout();
        return ['退出成功', $result];
    }
}
