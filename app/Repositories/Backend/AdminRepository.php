<?php
namespace App\Repositories\Backend;

use App\Models\Admin;
use App\Models\AdminPermission;
use Illuminate\Support\Facades\Auth;

class AdminRepository extends CommonRepository
{

    public function __construct(Admin $admin, AdminPermission $adminPermission)
    {
        parent::__construct($admin);
        $this->adminPermission = $adminPermission;
    }

    /**
     * 列表
     * @param  array $input 查询条件
     * @return object
     */
    public function getLists($input)
    {
        $default_search = [
            'filter' => ['id', 'username', 'email', 'permission_id', 'last_login_ip', 'last_login_time'],
            'sort'   => [
                'created_at' => 'asc',
            ],
        ];
        $search = $this->parseParams($default_search, $input);
        return $this->model->parseWheres($search)->paginate();
    }

    /**
     * 新增
     * @param  string $username      用户名
     * @param  string $email         邮箱
     * @param  string $password      加密后的密码
     * @param  int $permission_id 权限id
     * @param  int $status        状态
     * @return object
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
                'username'      => $username,
                'email'         => $email,
                'password'      => $password,
                'permission_id' => $permission_id,
                'status'        => $status,
            ],
            'text'   => !!$result ? '新增管理员成功' : '新增管理员失败',
            'status' => !!$result,
        ]);
        return $result;
    }

    /**
     * 新增
     * @param  int $id      主键
     * @param  string $username      用户名
     * @param  string $email         邮箱
     * @param  string $password      加密后的密码
     * @param  int $permission_id 权限id
     * @param  int $status        状态
     * @return object
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
        $result = $this->model->updateByWhere(['id' => $id], $data);

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
     * @param  int|array $id
     * @return boolean
     */
    public function destroy($id)
    {
        $result = $this->deleteById($id);

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
        $result['permission'] = $this->adminPermission->where('status', 1)->get();
        $result['status'] = [['value' => 0, 'text' => '冻结'], ['value' => 1, 'text' => '正常']];
        return $result;
    }

    /**
     * 获取当前登录的用户
     * @return array
     */
    public function currentLogin()
    {
        $result = [];
        if (Auth::guard('admin')->check()) {
            $result = Auth::guard('admin')->user();
        }
        return $result;
    }
}
