<?php
namespace App\Repositories\Backend;

use App\Models\Admin;
use App\Models\AdminPermission;
use Illuminate\Support\Facades\Hash;

class AdminRepository extends BaseRepository
{

    public $table_name = 'admins';

    /**
     * 管理员列表
     * @param  Array $input [search_form]
     * @return Array
     */
    public function index($input)
    {
        $resultData['lists']             = $this->getAdminLists($input['search_form']);
        $resultData['permissionOptions'] = AdminPermission::where('status', 1)->get();
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => $resultData,
            'message' => '数据获取成功',
        ];
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
        $permission_id = isset($input['permission_id']) ? intval($input['permission_id']) : '';
        $status        = isset($input['status']) ? intval($input['status']) : '';

        if (!$username || !$email || $password || !$permission_id) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '必填字段不得为空',
            ];
        }
        $usernameList = Admin::where('username', $username)->first();
        if (!empty($usernameList)) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '用户名已经存在',
            ];
        }
        $emailList = Admin::where('email', $email)->first();
        if (!empty($emailList)) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '邮箱已经存在',
            ];
        }
        $insertResult = Admin::create([
            'username'      => $username,
            'email'         => $email,
            'password'      => $password,
            'permission_id' => $permission_id,
            'status'        => $status,
        ]);

        // 记录操作日志
        Parent::saveOperateRecord([
            'action' => 'Admin/store',
            'params' => [
                'input' => $input,
            ],
            'text'   => !$insertResult ? '新增管理员失败，未知错误' : '新增管理员成功',
            'status' => !!$insertResult,
        ]);
        return [
            'status'  => !$insertResult ? Parent::ERROR_STATUS : Parent::SUCCESS_STATUS,
            'data'    => [],
            'message' => !$insertResult ? '新增管理员失败，未知错误' : '新增管理员成功',
        ];
    }

    /**
     * 编辑管理员
     * @param  Array $input [username, email, password, permission_id, status]
     * @param  Int $admin_id
     * @return Array
     */
    public function update($input, $admin_id)
    {
        $username      = isset($input['username']) ? strval($input['username']) : '';
        $email         = isset($input['email']) ? strval($input['email']) : '';
        $password      = isset($input['password']) && !empty($input['password']) ? Hash::make(strval($input['password'])) : '';
        $permission_id = isset($input['permission_id']) ? intval($input['permission_id']) : '';
        $status        = isset($input['status']) ? intval($input['status']) : '';

        if (!$username || !$email || !$permission_id) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '必填字段不得为空',
            ];
        }

        $adminData = Admin::where('id', $admin_id)->first();
        if (empty($adminData)) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '管理员不存在',
            ];
        }
        $usernameList = Admin::where('id', '!=', $admin_id)->where('username', $username)->first();
        if (!empty($usernameList)) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '用户名已经存在',
            ];
        }
        $emailList = Admin::where('id', '!=', $admin_id)->where('email', $email)->first();
        if (!empty($emailList)) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '邮箱已经存在',
            ];
        };
        $updateData = [
            'username'      => $input['username'],
            'email'         => $input['email'],
            'permission_id' => $input['permission_id'],
            'status'        => $input['status'],
        ];
        if ($password) {
            $updateData['password'] = $password;
        };
        $updateResult = Admin::where('id', $admin_id)->update($updateData);

        // 记录操作日志
        Parent::saveOperateRecord([
            'action' => 'Admin/update',
            'params' => [
                'input' => $input,
            ],
            'text'   => !$updateResult ? '更新管理员失败，未知错误' : '更新管理员成功',
            'status' => !!$updateResult,
        ]);

        return [
            'status'  => !$updateResult ? Parent::ERROR_STATUS : Parent::SUCCESS_STATUS,
            'data'    => [],
            'message' => !$updateResult ? '更新管理员失败，未知错误' : '更新管理员成功',
        ];
    }

    /**
     * 删除
     * @param  Int $admin_id
     * @return Array
     */
    public function destroy($admin_id)
    {
        $deleteResult = Admin::where('id', $admin_id)->delete();

        // 记录操作日志
        Parent::saveOperateRecord([
            'action' => 'Admin/destroy',
            'params' => [
                'admin_id' => $admin_id,
            ],
            'text'   => !$deleteResult ? '删除管理员失败，未知错误' : '删除管理员成功',
            'status' => !!$deleteResult,
        ]);

        return [
            'status'  => !$deleteResult ? Parent::ERROR_STATUS : Parent::SUCCESS_STATUS,
            'data'    => [],
            'message' => !$deleteResult ? '删除管理员失败，未知错误' : '删除管理员成功',
        ];
    }

    /**
     * 改变某一个字段的值
     * @param  Int $admin_id
     * @param  Array $data [field, value]
     * @return Array
     */
    public function changeFieldValue($admin_id, $input)
    {
        $field = isset($input['field']) ? strval($input['field']) : '';
        $value = isset($input['value']) ? strval($input['value']) : '';

        if (!$field) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '参数错误，请刷新页面重试',
            ];
        }

        $updateResult = Admin::where('id', $admin_id)->update([$field => $value]);

        // 记录操作日志
        Parent::saveOperateRecord([
            'action' => 'Admin/changeFieldValue',
            'params' => [
                'admin_id' => $admin_id,
                'input'    => $input,
            ],
            'text'   => !$updateResult ? '操作失败，未知错误' : '操作成功',
            'status' => !!$updateResult,
        ]);

        return [
            'status'  => !$updateResult ? Parent::ERROR_STATUS : Parent::SUCCESS_STATUS,
            'data'    => [],
            'message' => !$updateResult ? '操作失败，未知错误' : '操作成功',
        ];
    }

    public function getAdminLists($search_form)
    {
        $where_params = [];
        if (isset($search_form['permission_id']) && !empty($search_form['permission_id'])) {
            $where_params['permission_id'] = $search_form['permission_id'];
        }
        $query = Admin::where($where_params);
        if (isset($search_form['username']) && $search_form['username'] !== '') {
            $query->where('username', 'like', '%' . $search_form['username'] . '%');
        }
        return $query->paginate();
    }
}
