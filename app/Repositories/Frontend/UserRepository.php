<?php
namespace App\Repositories\Frontend;

use App\Models\Interact;
use App\Models\User;

class UserRepository extends BaseRepository
{

    /**
     * 用户信息
     * @return Array
     */
    public function show()
    {
        $result['list'] = $this->getUserList($this->getUserId());
        return $this->responseResult(true, $result);
    }

    /**
     * 个人中心页面
     * @return Array
     */
    public function index()
    {
        $result['list'] = $this->getUserList($this->getUserId());
        return $this->responseResult(true, $result);
    }

    /**
     * 根据user_id获取用户
     * @param  Int $user_id 用户id
     * @return Object
     */
    public function getUserList($user_id)
    {
        return User::where('id', $user_id)->where('status', 1)->where('active', 1)->first();
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
            return $this->responseResult(false, [], '更新失败，必填信息不得为空');
        }
        $user_id = $this->getUserId();

        $unique_list = User::where('username', $username)->where('id', '!=', $user_id)->first();
        if (!empty($unique_list)) {
            return $this->responseResult(false, [], '更新失败，用户名已经存在');
        }

        User::where('id', $user_id)->update([
            'username' => $username,
            'sign'     => $sign,
            'web_url'  => $web_url,
        ]);

        // 记录操作日志
        Parent::saveOperateRecord([
            'action' => 'User/update',
            'params' => $input,
            'text'   => '更新成功',
        ]);

        return $this->responseResult(true, [], '更新成功');
    }

    /**
     * 收藏列表页面
     * @param  Array $input []
     * @return Array
     */
    public function collect($input)
    {
        $search          = isset($input['search']) ? $input['search'] : [];
        $result['lists'] = $this->getCollectLists($this->getUserId(), $search);
        return $this->responseResult(true, $result);
    }

    /**
     * 获取收藏列表
     * @param  Int $user_id 用户id
     * @return Object
     */
    public function getCollectLists($user_id, $search)
    {
        $search['user_id'] = $user_id;
        $search['collect'] = 1;
        $params            = $this->parseParams('interactes', $search);
        return             = Interact::parseWheres($params)->with('article')->with('videoList')->paginate();
    }
}
