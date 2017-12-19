<?php
namespace App\Servers\Frontend;

use App\Repositories\Frontend\InteractRepository;
use App\Repositories\Frontend\UserRepository;

class UserServer extends CommonServer
{

    public function __construct(
        UserRepository $userRepository,
        InteractRepository $interactRepository
    ) {
        $this->userRepository     = $userRepository;
        $this->interactRepository = $interactRepository;
    }

    /**
     * 获取当前登录用户
     * @param  Int $user_id 用户id
     * @return Object
     */
    public function currentUser()
    {
        $result['list'] = $this->userRepository->currentUser();

        return ['获取成功', $result];
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
            return ['code' => ['x00004', 'system']];
        }

        // 判断用户名是否重复
        if (!$is_exist = $this->adminRepository->existList([
            'username' => $username,
            'email'    => ['or', $email],
            'id'       => ['!=', getCurrentUserId()],
        ])) {
            return ['code' => ['x00001', 'user']];
        }
        $result = $this->userRepository->update($username, $sign, $web_url);

        return ['更新成功', $result];
    }

    /**
     * 收藏列表
     * @param  Array $input []
     * @return Array
     */
    public function collectLists($input)
    {
        $search          = isset($input['search']) ? $input['search'] : [];
        $result['lists'] = $this->interactRepository->getCollectLists($search);

        return ['获取成功', $result];
    }
}
