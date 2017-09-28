<?php
namespace App\Repositories\Backend;

use App\Models\Admin;
use App\Models\AdminPermission;
use Illuminate\Support\Facades\Hash;

class AdminRepository extends BaseRepository
{

    /**
     * 管理员列表
     * @param  Array $input [searchForm]
     * @return Array
     */
    public function lists($input)
    {
        $resultData['lists']             = Admin::lists($input['searchForm']);
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
    public function create($input)
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
        if (!$insertResult) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '未知错误，请联系管理员',
            ];
        }
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => [],
            'message' => '新增成功',
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
        if (!$updateResult) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '未知错误，请联系管理员',
            ];
        }
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => [],
            'message' => '更新成功',
        ];
    }

    /**
     * 删除
     * @param  Int $admin_id
     * @return Array
     */
    public function delete($admin_id)
    {
        $deleted = Admin::where('id', $admin_id)->delete();
        return [
            'status'  => $deleted ? Parent::SUCCESS_STATUS : Parent::ERROR_STATUS,
            'data'    => [],
            'message' => $deleted ? '管理员删除成功' : '管理员删除失败',
        ];
    }

    /**
     * 改变某一个字段的值
     * @param  Int $id
     * @param  Array $data [field, value]
     * @return Array
     */
    public function changeFieldValue($id, $input)
    {
        $field = isset($input['field']) ? strval($input['field']) : '';
        $value = isset($input['value']) ? strval($input['value']) : '';

        if (!$field) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '未知错误，请联系管理员',
            ];
        }

        $updateResult = Admin::where('id', $id)->update([$field => $value]);
        return [
            'status'  => $updateResult ? Parent::SUCCESS_STATUS : Parent::ERROR_STATUS,
            'data'    => [],
            'message' => $updateResult ? '操作成功' : '操作失败',
        ];
    }
}
