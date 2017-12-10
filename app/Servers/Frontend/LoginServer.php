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
            return responseResult(false, [], '登录失败，必填字段不得为空');
        }

        $list = $this->loginRepository->login($account, $password, $remember);
        if (isset($list['flag']) && !$list['flag']) {
            return responseResult(false, [], $list['message']);
        }

        $result['list'] = [
            'username' => $list['username'],
            'email'    => $list['email'],
            'face'     => $list['face'],
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
        $list   = $this->userRepository->currentLoginUser();
        if (empty($list)) {
            return responseResult(true, $result);
        }
        $result['list'] = [
            'username' => $list->username,
            'email'    => $list->email,
            'face'     => $list->face,
            'sign'     => $list->sign,
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
