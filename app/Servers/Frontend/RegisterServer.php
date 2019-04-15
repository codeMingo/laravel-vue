<?php
namespace App\Servers\Frontend;

use App\Repositories\Frontend\RegisterRepository;
use App\Repositories\Frontend\UserRepository;
use Illuminate\Support\Facades\Hash;

class RegisterServer extends CommonServer
{

    public function __construct(
        RegisterRepository $registerRepository,
        UserRepository $userRepository
    ) {
        $this->registerRepository = $registerRepository;
        $this->userRepository     = $userRepository;
    }

    // 创建用户
    public function register(array $input)
    {
        $username = isset($input['username']) ? strval($input['username']) : '';
        $email    = isset($input['email']) ? strval($input['email']) : '';
        $face     = isset($input['face']) ? strval($input['face']) : '';
        $password = isset($input['password']) ? Hash::make(strval($input['password'])) : '';

        if (!$username || !$email || !$password) {
            return ['code' => ['x00001', 'register']];
        }

        // 用户名和邮箱重复判断
        $search_where = [
            'filter' => ['username', 'email'],
            'search' => [
                'username' => $username,
                'email'    => ['or', $email],
            ],
        ];
        $list = $this->userRepository->getList($search_where);
        if (!empty($list)) {
            $code = $list->username == $username ? 'x00002' : 'x00003';
            return ['code' => [$code, 'register']];
        }

        $result = $this->registerRepository->register($username, $email, $face, $password);

        return ['注册成功，请在24小时内激活账号', $result];
    }

    /**
     * 激活用户
     * @param  Array $input [user_id]
     * @return Array
     */
    public function active($input)
    {
        // url + 号会被自动转化成空格
        $user_id = isset($input['user_id']) ? authcode(str_replace(' ', '+', $input['user_id']), 'decrypt') : '';
        if (!$user_id) {
            return ['code' => ['x00004', 'register']];
        }

        // 判断是否存在这个用户
        if (!$is_exist = $this->userRepository->existList([
            'id' => $user_id,
        ])) {
            return ['code' => ['x00004', 'register']];
        }

        $this->registerRepository->active($user_id);
        return ['激活成功'];
    }
}
