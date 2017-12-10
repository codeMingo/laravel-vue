<?php
namespace App\Servers\Frontend;

use App\Repositories\Frontend\RegisterRepository;
use Illuminate\Support\Facades\Hash;

class RegisterServer extends CommonServer
{

    public function __construct(
        RegisterRepository $registerRepository
    ) {
        $this->registerRepository = $registerRepository;
    }

    // 创建用户
    public function register(array $input)
    {
        $username = isset($input['username']) ? strval($input['username']) : '';
        $email    = isset($input['email']) ? strval($input['email']) : '';
        $face     = isset($input['face']) ? strval($input['face']) : '';
        $password = isset($input['password']) ? Hash::make(strval($input['password'])) : '';

        if (!$username || !$email || !$password) {
            return responseResult(false, [], '注册失败，必填信息不得为空');
        }

        $result = $this->registerRepository->register($username, $email, $face, $password);
        if (isset($result['flag']) && !$result['flag']) {
            return responseResult(false, [], $result['message']);
        }
        return responseResult(true, $result, '注册成功，请在24小时内激活账号');
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
            return responseResult(false, [], '激活失败，邮件已经失效');
        }
        $result = $this->registerRepository->active($user_id);
        if (isset($result['flag']) && !$result['flag']) {
            return responseResult(false, [], $result['message']);
        }
        return responseResult(true, [], '激活成功，恭喜您，账户激活成功');
    }
}
