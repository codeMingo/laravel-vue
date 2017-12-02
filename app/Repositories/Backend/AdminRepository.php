<?php
namespace App\Repositories\Backend;

use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminRepository extends CommonRepository
{

    /**
     * 管理员列表
     * @param  Array $input [search]
     * @return Array
     */
    public function lists($input)
    {
        $search            = isset($input['search']) ? (array) $input['search'] : [];
        $result['lists']   = $this->getAdminLists($search);
        $result['options'] = $this->getOptions();
        return $this->responseResult(true, $result);
    }

    /**
     * 新增
     * @param  Array $input [username, email, password, permission_id, status]
     * @return Array
     */
    public function store($input)
    {
        $username      = isset($input['username']) ? strval($input['username']) : '';
        $email         = isset($input['email']) ? strval($input['email']) : '';
        $password      = isset($input['password']) ? Hash::make(strval($input['password'])) : '';
        $permission_id = isset($input['permission_id']) ? intval($input['permission_id']) : 0;
        $status        = isset($input['status']) ? intval($input['status']) : 0;

        if (!$username || !$email || !$password || !$permission_id) {
            return $this->responseResult(false, [], '必填字段不得为空');
        }
        $unique_list = Admin::where('username', $username)->whereOr('email', $email)->first();
        if (!empty($unique_list)) {
            return $this->responseResult(false, [], $unique_list->username == $username ? '用户名被注册' : '邮箱被注册');
        }
        $result = Admin::create([
            'username'      => $username,
            'email'         => $email,
            'password'      => $password,
            'permission_id' => $permission_id,
            'status'        => $status,
        ]);

        Parent::saveOperateRecord([
            'action' => 'Admin/store',
            'params' => [
                'input' => $input,
            ],
            'text'   => '新增管理员成功',
        ]);
        return $this->responseResult(!!$result, !$result ? [] : $result, !$result ? '新增失败，未知错误' : '新增成功');
    }

    /**
     * 详情
     * @param  Int $id
     * @return Array
     */
    public function show($id)
    {
        $result['list'] = Admin::where('id', $id)->with('adminPermission')->with('adminLoginReocrd')->with('adminOperateRecord')->first();
        return $this->responseResult(!!$result, !$result ? [] : $result, !$result ? '不存在这条数据' : '获取成功');
    }

    /**
     * 编辑
     * @param  Array $input [username, email, password, permission_id, status]
     * @param  Int $id
     * @return Array
     */
    public function update($input, $id)
    {
        $username      = isset($input['username']) ? strval($input['username']) : '';
        $email         = isset($input['email']) ? strval($input['email']) : '';
        $password      = (isset($input['password']) && !empty($input['password'])) ? Hash::make(strval($input['password'])) : '';
        $permission_id = isset($input['permission_id']) ? intval($input['permission_id']) : 0;
        $status        = isset($input['status']) ? intval($input['status']) : 0;

        if (!$username || !$email || !$permission_id) {
            return $this->responseResult(false, [], '必填字段不得为空');
        }

        $admin_list = Admin::where('id', $id)->first();
        if (empty($admin_list)) {
            return $this->responseResult(false, [], '管理员不存在');
        }
        $unique_list = Admin::where('username', $username)->whereOr('email', $email)->where('id', '!=', $id)->first();
        if (!empty($unique_list)) {
            return $this->responseResult(false, [], $unique_list->username == $username ? '用户名被注册' : '邮箱被注册');
        }
        $updateData = [
            'username'      => $username,
            'email'         => $email,
            'permission_id' => $permission_id,
            'status'        => $status,
        ];
        if ($password) {
            $updateData['password'] = $password;
        };
        $result = Admin::where('id', $id)->update($updateData);

        // 记录操作日志
        Parent::saveOperateRecord([
            'action' => 'Admin/update',
            'params' => [
                'input' => $input,
            ],
            'text'   => '更新管理员成功',
        ]);

        return $this->responseResult(!!$result, !$result ? [] : $result, !$result ? '更新失败，未知错误' : '更新成功');
    }

    /**
     * 删除
     * @param  Int $id
     * @return Array
     */
    public function destroy($id)
    {
        $result = Admin::where('id', $id)->delete();
        if (!$result) {
            return $this->responseResult(false, [], '该管理员不存在或已被删除');
        }

        // 记录操作日志
        Parent::saveOperateRecord([
            'action' => 'Admin/destroy',
            'params' => [
                'admin_id' => $id,
            ],
            'text'   => '删除管理员成功',
        ]);
        return $this->responseResult(true, [], '删除成功');
    }

    /**
     * 列表
     * @param  Array $search [permission_id, status, username]
     * @return Object              结果集
     */
    public function getAdminLists($search)
    {
        $where_params = $this->parseParams('admins', $search);
        return Admin::parseWheres($where_params)->paginate();
    }

    public function getOptions()
    {
        $result['permission'] = DB::table('admin_permissions')->where('status', 1)->get();
        $result['status']     = [['value' => 0, 'text' => '冻结'], ['value' => 1, 'text' => '正常']];
        return $result;
    }
}
