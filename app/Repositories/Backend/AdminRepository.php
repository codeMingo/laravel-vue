<?php
namespace App\Repositories\Backend;

use App\Models\Admin;
use App\Models\AdminPermission;
use Illuminate\Support\Facades\Hash;

class AdminRepository extends BaseRepository
{

    public $table_name = 'admins';
    public $params_rules = [
        'id' => '=',
        'username' => 'like',
        'email' => 'like',
        'permission_id' => '=',
        'status' => '='
    ];

    /**
     * 管理员列表
     * @param  Array $input [search_form]
     * @return Array
     */
    public function index($input)
    {
        $resultData['lists']             = $this->getAdminLists($input['search_form']);
        $resultData['permissionOptions'] = DB::table('admin_permissions')->where('status', 1)->get();
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
        $username      = validateValue($input['username']);
        $email         = validateValue($input['email']);
        $password      = isset($input['password']) ? Hash::make(strval($input['password'])) : '';
        $permission_id = validateValue($input['permission_id'], 'int');
        $status        = validateValue($input['status'], 'int');

        if (!$username || !$email || $password || !$permission_id) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '必填字段不得为空',
            ];
        }
        $unique_list = Admin::where('username', $username)->whereOr('email', $email)->first();
        if (!empty($unique_list)) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => $unique_list->username == $username ? '用户名被注册' : '邮箱被注册',
            ];
        }
        $result = Admin::create([
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
            'text'   => !$result ? '新增管理员失败，未知错误' : '新增管理员成功',
            'status' => !!$result,
        ]);
        return [
            'status'  => !$result ? Parent::ERROR_STATUS : Parent::SUCCESS_STATUS,
            'data'    => $result,
            'message' => !$result ? '新增管理员失败，未知错误' : '新增管理员成功',
        ];
    }

    /**
     * 编辑
     * @param  Array $input [username, email, password, permission_id, status]
     * @param  Int $admin_id
     * @return Array
     */
    public function update($input, $admin_id)
    {
        $username      = validateValue($input['username']);
        $email         = validateValue($input['email']);
        $password      = isset($input['password']) && !empty($input['password']) ? Hash::make(strval($input['password'])) : '';
        $permission_id = validateValue($input['permission_id'], 'int');
        $status        = validateValue($input['status'], 'int');

        if (!$username || !$email || !$permission_id) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '必填字段不得为空',
            ];
        }

        $admin_list = Admin::where('id', $admin_id)->first();
        if (empty($admin_list)) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '管理员不存在',
            ];
        }
        $unique_list = Admin::where('username', $username)->whereOr('email', $email)->where('id', '!=', $admin_id)->first();
        if (!empty($unique_list)) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => $unique_list->username == $username ? '用户名被注册' : '邮箱被注册',
            ];
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
        $result = Admin::where('id', $admin_id)->update($updateData);

        // 记录操作日志
        Parent::saveOperateRecord([
            'action' => 'Admin/update',
            'params' => [
                'input' => $input,
            ],
            'text'   => !$result ? '更新管理员失败，未知错误' : '更新管理员成功',
            'status' => !!$result,
        ]);

        return [
            'status'  => !$result ? Parent::ERROR_STATUS : Parent::SUCCESS_STATUS,
            'data'    => $result,
            'message' => !$result ? '更新管理员失败，未知错误' : '更新管理员成功',
        ];
    }

    /**
     * 删除
     * @param  Int $admin_id
     * @return Array
     */
    public function destroy($admin_id)
    {
        $result = Admin::where('id', $admin_id)->delete();

        // 记录操作日志
        Parent::saveOperateRecord([
            'action' => 'Admin/destroy',
            'params' => [
                'admin_id' => $admin_id,
            ],
            'text'   => !$result ? '删除管理员失败，未知错误' : '删除管理员成功',
            'status' => !!$result,
        ]);

        return [
            'status'  => !$result ? Parent::ERROR_STATUS : Parent::SUCCESS_STATUS,
            'data'    => $result,
            'message' => !$result ? '删除管理员失败，未知错误' : '删除管理员成功',
        ];
    }

    /**
     * 列表
     * @param  Array $search_form [permission_id, status, username]
     * @return Object              结果集
     */
    public function getAdminLists($search_form)
    {
        $where_params = $this->parseParams($search_form);
        return Admin::parseWheres($where_params)->paginate();
    }
}
