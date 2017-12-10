<?php
namespace App\Repositories\Backend;

use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
    public function currentLoginAdmin()
    {
        $result = [];
        if (Auth::guard('admin')->check()) {
            $result = Auth::guard('admin')->user();
        }
        return $result;
    }

    /**
     * 详情
     * @param  int $id 文章id
     * @return Array
     */
    public function getAdminDetail($id)
    {

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
            return responseResult(false, [], '必填字段不得为空');
        }
        $unique_list = $this->model->where('username', $username)->whereOr('email', $email)->first();
        if (!empty($unique_list)) {
            return responseResult(false, [], $unique_list->username == $username ? '用户名被注册' : '邮箱被注册');
        }
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
        return responseResult(true, $result, '新增成功');
    }

    /**
     * 编辑
     * @param  Array $input [username, email, password, permission_id, status]
     * @param  Int $id
     * @return Array
     */
    public function update($id, $username, $email, $password, $permission_id, $status)
    {
        $list = $this->model->find($id);
        if (empty($list)) {
            return ['flag' => false, 'message' => '管理员不存在'];
        }
        $unique_list = $this->model->where('username', $username)->whereOr('email', $email)->where('id', '!=', $id)->first();
        if (!empty($unique_list)) {
            return ['flag' => false, 'message' => $unique_list->username == $username ? '用户名被注册' : '邮箱被注册'];
        }
        $list->username      = $username;
        $list->email         = $email;
        $list->permission_id = $permission_id;
        $list->status        = $status;
        if ($password) {
            $list->password = $password;
        };
        $result = $list->save();

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
            'text'   => '更新管理员资料成功',
        ]);
        return true;
    }

    /**
     * 删除
     * @param  Int $id
     * @return Array
     */
    public function destroy($id)
    {
        $result = $this->deleteById($id);
        if (!$result) {
            return ['flag' => false, 'message' => '该管理员不存在或已被删除'];
        }

        // 记录操作日志
        Parent::saveOperateRecord([
            'action' => 'Admin/destroy',
            'params' => [
                'admin_id' => $id,
            ],
            'text'   => '删除管理员成功',
        ]);
        return true;
    }

    /**
     * 列表
     * @param  Array $search [permission_id, status, username]
     * @return Object              结果集
     */
    public function getAdminLists($search)
    {
        $where_params = $this->parseParams($search);
        return $this->model->parseWheres($where_params)->paginate();
    }

    public function getOptions()
    {
        $result['permission'] = DB::table('admin_permissions')->where('status', 1)->get();
        $result['status']     = [['value' => 0, 'text' => '冻结'], ['value' => 1, 'text' => '正常']];
        return $result;
    }
}
