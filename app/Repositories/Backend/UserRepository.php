<?php
namespace App\Repositories\Backend;

use App\Models\User;
use App\Repositories\Backend\DictRepository;

class UserRepository extends BaseRepository
{

    /**
     * 用户列表
     * @param  Array $input [search]
     * @return Array
     */
    public function lists($input)
    {
        $result['lists']   = $this->getUserLists($input['search']);
        $result['options'] = $this->getOptions();
        return $this->responseResult(true, $result);
    }

    /**
     * 新增
     * @param  Array $input [username, email, password, status, active]
     * @return Array
     */
    public function create($input)
    {
        $username = isset($input['username']) ? strval($input['username']) : '';
        $email    = isset($input['email']) ? strval($input['email']) : '';
        $face     = isset($input['face']) ? strval($input['face']) : '';
        $password = isset($input['password']) ? Hash::make(strval($input['password'])) : '';
        $status   = isset($input['status']) ? intval($input['status']) : 0;
        $active   = isset($input['active']) ? intval($input['active']) : 0;

        if (!$username || !$email || !$password) {
            return $this->responseResult(false, [], '新增失败，必填信息不得为空');
        }

        $unique_list = User::where('username', $username)->whereOr('email', $email)->first();
        if (!empty($unique_list)) {
            $error_text = $unique_list->username == $username ? '新增失败，用户名已被新增' : '新增失败，邮箱已被新增';
            return $this->responseResult(false, [], $error_text);
        }

        $result = User::create([
            'username' => $username,
            'email'    => $email,
            'face'     => $face,
            'password' => $password,
            'status'   => $status,
            'active'   => $active,
        ]);

        return $this->responseResult(true, $result, '新增成功');
    }

    /**
     * 编辑用户
     * @param  Array $input [username, email, password, permission_id, status]
     * @param  Int $user_id
     * @return Array
     */
    public function update($input, $id)
    {
        $list = $this->getUserList($id);
        if (empty($list)) {
            return $this->responseResult(false, [], '编辑失败，不存在此用户');
        }

        $username = isset($input['username']) ? strval($input['username']) : '';
        $email    = isset($input['email']) ? strval($input['email']) : '';
        $face     = isset($input['face']) ? strval($input['face']) : '';
        $password = isset($input['password']) ? Hash::make(strval($input['password'])) : '';
        $status   = isset($input['status']) ? intval($input['status']) : 0;
        $active   = isset($input['active']) ? intval($input['active']) : 0;

        if (!$username || !$email) {
            return $this->responseResult(false, [], '编辑失败，必填信息不得为空');
        }

        $unique_list = User::where('username', $username)->whereOr('email', $email)->where('id', '!=', $id)->first();
        if (!empty($unique_list)) {
            $error_text = $unique_list->username == $username ? '编辑失败，用户名已经存在' : '编辑失败，邮箱已经存在';
            return $this->responseResult(false, [], $error_text);
        }

        $data = [
            'username' => $username,
            'email'    => $email,
            'face'     => $face,
            'status'   => $status,
            'active'   => $active,
        ];
        if ($password) {
            $data['password'] = $password
        }

        $result = User::where('id', $id)->save($data);

        return $this->responseResult(true, $result, '编辑成功');
    }

    /**
     * 删除
     * @param  Int $id
     * @return Array
     */
    public function delete($id)
    {
        $result = User::where('id', $id)->delete();
        return $this->responseResult(true, $result, '删除成功');
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
     * 获取options
     * @return Array
     */
    public function getOptions()
    {
        $result['gender'] = DictRepository::getInstance()->getDictListsByCode(['gender'])['gender'];
        $result['status'] = [['value' => 0, 'text' => '冻结'], ['value' => 1, 'text' => '正常']];
        $result['active'] = [['value' => 0, 'text' => '未激活'], ['value' => 1, 'text' => '已激活']];
        return $result;
    }
}
