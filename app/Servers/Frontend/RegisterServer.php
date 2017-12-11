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
        $this->userRepository = $userRepository;
    }

    // 创建用户
    public function register(array $input)
    {
        $username = isset($input['username']) ? strval($input['username']) : '';
        $email    = isset($input['email']) ? strval($input['email']) : '';
        $face     = isset($input['face']) ? strval($input['face']) : '';
        $password = isset($input['password']) ? Hash::make(strval($input['password'])) : '';

        if (!$username || !$email || !$password) {
            return returnError('注册失败，必填信息不得为空');
        }

        // 用户名和邮箱重复判断
        $list = $this->userRepository->getListByWhere(['username' => $username, 'email' => ['or', $email]]);
        if (!empty($list)) {
            return returnError($list->username == $username ? '用户名已存在' : '邮箱已存在');
        }

        $result = $this->registerRepository->register($username, $email, $face, $password);
        return returnSuccess('注册成功，请在24小时内激活账号', $result);
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
            return returnError('激活失败，邮件已经失效');
        }

        // 判断是否存在这个用户
        $list = $this->userRepository->getListByWhere(['id' => $user_id]);
        if (!empty($list)) {
            return returnError('激活失败，不存在此用户');
        }

        $result = $this->registerRepository->active($user_id);
        if (!$result) {
            return returnError('激活失败，账户已经激活');
        }
        return returnSuccess('激活成功，恭喜您，账户激活成功');
    }
}
