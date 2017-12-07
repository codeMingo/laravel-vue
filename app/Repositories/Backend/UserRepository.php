<?php
namespace App\Repositories\Backend;

use App\Models\User;

use App\Repositories\Common\DictRepository;

class UserRepository extends CommonRepository
{

    public $dictRepository;

    public function __construct(DictRepository $dictRepository)
    {
        parent::__construct();
        $this->dictRepository = $dictRepository;
    }

    /**
     * 用户列表
     * @param  Array $input [search]
     * @return Array
     */
    public function lists($input)
    {
        $search            = isset($input['search']) ? (array) $input['search'] : [];
        $result['lists']   = $this->getUserLists($search);
        $result['options'] = $this->getOptions();

        return responseResult(true, $result);
    }

    /**
     * 新增
     * @param  Array $input [username, email, password, status, active]
     * @return Array
     */
    public function store($input)
    {
        $username = isset($input['username']) ? strval($input['username']) : '';
        $email    = isset($input['email']) ? strval($input['email']) : '';
        $face     = isset($input['face']) ? strval($input['face']) : '';
        $password = isset($input['password']) ? Hash::make(strval($input['password'])) : '';
        $status   = isset($input['status']) ? intval($input['status']) : 0;
        $active   = isset($input['active']) ? intval($input['active']) : 0;

        if (!$username || !$email || !$password) {
            return responseResult(false, [], '新增失败，必填信息不得为空');
        }

        $unique_list = User::where('username', $username)->whereOr('email', $email)->first();
        if (!empty($unique_list)) {
            $error_text = $unique_list->username == $username ? '新增失败，用户名已被新增' : '新增失败，邮箱已被新增';
            return responseResult(false, [], $error_text);
        }

        $result = User::create([
            'username' => $username,
            'email'    => $email,
            'face'     => $face,
            'password' => $password,
            'status'   => $status,
            'active'   => $active,
        ]);

        Parent::saveOperationRecord([
            'action' => 'User/store',
            'params' => [
                'input' => $input,
            ],
            'text'   => '新增用户成功',
        ]);

        return responseResult(true, $result, '新增成功');
    }

    /**
     * 更新用户
     * @param  Array $input [username, email, password, permission_id, status]
     * @param  Int $user_id
     * @return Array
     */
    public function update($input, $id)
    {
        $list = $this->getUserList($id);
        if (empty($list)) {
            return responseResult(false, [], '更新失败，不存在此用户');
        }

        $username = isset($input['username']) ? strval($input['username']) : '';
        $email    = isset($input['email']) ? strval($input['email']) : '';
        $face     = isset($input['face']) ? strval($input['face']) : '';
        $password = isset($input['password']) ? Hash::make(strval($input['password'])) : '';
        $status   = isset($input['status']) ? intval($input['status']) : 0;
        $active   = isset($input['active']) ? intval($input['active']) : 0;

        if (!$username || !$email) {
            return responseResult(false, [], '更新失败，必填信息不得为空');
        }

        $unique_list = User::where('username', $username)->whereOr('email', $email)->where('id', '!=', $id)->first();
        if (!empty($unique_list)) {
            $error_text = $unique_list->username == $username ? '更新失败，用户名已经存在' : '更新失败，邮箱已经存在';
            return responseResult(false, [], $error_text);
        }

        $data = [
            'username' => $username,
            'email'    => $email,
            'face'     => $face,
            'status'   => $status,
            'active'   => $active,
        ];
        if ($password) {
            $data['password'] = $password;
        }
        User::where('id', $id)->save($data);

        Parent::saveOperationRecord([
            'action' => 'User/store',
            'params' => [
                'input' => $input,
            ],
            'text'   => '更新用户成功',
        ]);

        return responseResult(true, [], '更新成功');
    }

    /**
     * 删除
     * @param  Int $id
     * @return Array
     */
    public function destroy($id)
    {
        $result = User::where('id', $id)->delete();
        // 记录操作日志
        Parent::saveOperateRecord([
            'action' => 'User/destroy',
            'params' => [
                'admin_id' => $id,
            ],
            'text'   => '删除用户成功',
        ]);
        return responseResult(true, $result, '删除成功');
    }

    /**
     * 获取一条用户数据
     * @param  Int $id 用户id
     * @return Object
     */
    public function getUserList($id)
    {
        return User::where('id', $id)->first();
    }

    /**
     * 列表
     * @param  Array $search [permission_id, status, username]
     * @return Object              结果集
     */
    public function getUserLists($search)
    {
        $where_params = $this->parseParams('users', $search);

        return User::parseWheres($where_params)->paginate();
    }

    /**
     * 获取options
     * @return Array
     */
    public function getOptions()
    {
        $result['gender'] = $this->dictRepository->getListsByCode('gender');
        $result['status'] = [['value' => 0, 'text' => '冻结'], ['value' => 1, 'text' => '正常']];
        $result['active'] = [['value' => 0, 'text' => '未激活'], ['value' => 1, 'text' => '已激活']];

        return $result;
    }
}
