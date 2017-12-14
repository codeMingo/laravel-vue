<?php
namespace App\Repositories\Frontend;

use App\Models\User;
use App\Repositories\Frontend\InteractRepository;
use Illuminate\Support\Facades\Auth;

class UserRepository extends CommonRepository
{

    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    /**
     * 获取当前登录的用户
     * @return Array
     */
    public function currentUser()
    {
        $result = [];
        if (Auth::guard('web')->check()) {
            $result = Auth::guard('web')->user();
        }
        return $result;
    }

    /**
     * 用户信息
     * @return Array
     */
    public function show()
    {
        $result['list'] = $this->getUserList(getCurrentUserId());
        return responseResult(true, $result);
    }

    /**
     * 个人中心
     * @return Array
     */
    public function index()
    {
        $result['list'] = $this->getUserList(getCurrentUserId());
        return responseResult(true, $result);
    }

    /**
     * 根据user_id获取用户
     * @param  Int $user_id 用户id
     * @return Object
     */
    public function getUserList($user_id)
    {
        return $this->model->where('id', $user_id)->where('status', 1)->where('active', 1)->first();
    }

    /**
     * 更新资料
     * @param  Array $input   用户资料
     * @return Array
     */
    public function update($username, $sign, $web_url)
    {
        $this->model->where('id', $user_id)->update([
            'username' => $username,
            'sign'     => $sign,
            'web_url'  => $web_url,
        ]);

        // 记录操作日志
        Parent::saveOperateRecord([
            'action' => 'User/update',
            'params' => [
                'username' => $username,
                'sign'     => $sign,
                'web_url'  => $web_url,
            ],
            'text'   => '更新成功',
        ]);

        return true;
    }

    /**
     * 收藏列表
     * @param  Array $input []
     * @return Array
     */
    public function collect($input)
    {
        $search           = isset($input['search']) ? $input['search'] : [];
        $input['user_id'] = getCurrentUserId();
        $result           = $this->interactRepository->getInteractLists($search);
        return $result;
    }

}
