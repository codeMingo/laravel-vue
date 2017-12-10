<?php
namespace App\Servers\Frontend;

use App\Repositories\Frontend\UserRepository;
use App\Repositories\Frontend\InteractRepository;

class UserServer extends CommonServer
{

    public function __construct(
        UserRepository $userRepository,
        InteractRepository $interactRepository
    ) {
        $this->userRepository = $userRepository;
        $this->interactRepository = $interactRepository;
    }

    /**
     * 获取当前登录用户
     * @param  Int $user_id 用户id
     * @return Object
     */
    public function currentUser()
    {
        $result['list'] = $this->userRepository->currentLoginUser();
        return responseResult(true, $result);
    }

    /**
     * 更新资料
     * @param  Array $input   用户资料
     * @return Array
     */
    public function update($input)
    {
        $username = isset($input['username']) ? strval($input['username']) : '';
        $sign     = isset($input['sign']) ? strval($input['sign']) : '';
        $web_url  = isset($input['web_url']) ? strval($input['web_url']) : '';

        if (!$username) {
            return responseResult(false, [], '更新失败，必填信息不得为空');
        }
        $result = $this->userRepository->update($username, $sign, $web_url);
        if (isset($result['flag']) && !$result['flag']) {
            return responseResult(false, [], $result['message']);
        }

        return responseResult(true, [], '更新成功');
    }

    /**
     * 收藏列表
     * @param  Array $input []
     * @return Array
     */
    public function collectLists($input)
    {
        $search           = isset($input['search']) ? $input['search'] : [];
        $result['lists']  = $this->interactRepository->getCollectLists($search);
        return responseResult(true, $result);
    }
}
