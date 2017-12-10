<?php
namespace App\Servers\Backend;

use App\Repositories\Backend\AdminRepository;
use App\Repositories\Frontend\CategoryRepository;
use App\Repositories\Frontend\TagRepository;

class AdminServer extends CommonServer
{

    public function __construct(
        AdminRepository $adminRepository
    ) {
        $this->adminRepository  = $adminRepository;
    }

    /**
     * 列表
     * @param  Array $input [search]
     * @return Array
     */
    public function lists($input)
    {
        $search            = isset($input['search']) ? (array) $input['search'] : [];
        $result['lists']   = $this->adminRepository->getAdminLists($search);
        $result['options'] = $this->adminRepository->getOptions();
        return responseResult(true, $result);
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
    public function update($id, $input)
    {
        $username      = isset($input['username']) ? strval($input['username']) : '';
        $email         = isset($input['email']) ? strval($input['email']) : '';
        $password      = (isset($input['password']) && !empty($input['password'])) ? Hash::make(strval($input['password'])) : '';
        $permission_id = isset($input['permission_id']) ? intval($input['permission_id']) : 0;
        $status        = isset($input['status']) ? intval($input['status']) : 0;

        if (!$username || !$email || !$permission_id) {
            return responseResult(false, [], '必填字段不得为空');
        }

        $result = $this->adminRepository->update($id, $username, $email, $password, $permission_id, $status);
        if (isset($result['flag']) && !$result['flag']) {
            return responseResult(false, [], $result['message']);
        }

        return responseResult(true, [], '更新成功');
    }

    /**
     * 删除
     * @param  Int $id
     * @return Array
     */
    public function destroy($id)
    {
        $result = $this->adminRepository->destroy($id);
        return responseResult(true, [], '删除成功');
    }
}
