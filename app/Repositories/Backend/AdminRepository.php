<?php
namespace App\Repositories\Backend;

use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminRepository extends CommonRepository
{

    public function __construct(Admin $admin)
    {
        parent::__construct($admin);
    }

    /**
     * 获取当前登录的用户
     * @return Array
     */
    public function currentLogin()
    {
        $result = [];
        if (Auth::guard('admin')->check()) {
            $result = Auth::guard('admin')->user();
        }
        return $result;
    }

    /**
     * 新增
     * @param  Array $input [username, email, password, permission_id, status]
     * @return Array
     */
    public function store($username, $email, $password, $permission_id, $status)
    {
        $result = $this->model->create([
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
        return $result;
    }

    /**
     * 编辑
     * @param  Array $input [username, email, password, permission_id, status]
     * @param  Int $id
     * @return Array
     */
    public function update($id, $username, $email, $password, $permission_id, $status)
    {
        $data = [
            'username'      => $username,
            'email'         => $email,
            'permission_id' => $permission_id,
            'status'        => $status,
            'username'      => $username,
        ];
        if ($password) {
            $data['password'] = $password;
        };
        $result = (bool) $this->model->where('id', $id)->update($data);

        // 记录操作日志
        Parent::saveOperateRecord([
            'action' => 'Admin/update',
            'params' => [
                'id'            => $id,
                'username'      => $username,
                'email'         => $email,
                'password'      => $password,
                'permission_id' => $permission_id,
                'status'        => $status,
            ],
            'text'   => $result ? '更新管理员资料成功' : '更新管理员资料失败',
            'status' => $result,
        ]);
        return $result;
    }

    /**
     * 删除
     * @param  Int $id
     * @return Array
     */
    public function destroy($id)
    {
        $result = (bool) $this->deleteById($id);

        // 记录操作日志
        Parent::saveOperateRecord([
            'action' => 'Admin/destroy',
            'params' => [
                'admin_id' => $id,
            ],
            'text'   => $result ? '删除管理员成功' : '删除管理员失败',
            'status' => $result,
        ]);
        return $result;
    }

    // 获取option
    public function getOptions()
    {
        $result['permission'] = DB::table('admin_permissions')->where('status', 1)->get();
        $result['status']     = [['value' => 0, 'text' => '冻结'], ['value' => 1, 'text' => '正常']];
        return $result;
    }

    // 判断管理员是否存在
}
