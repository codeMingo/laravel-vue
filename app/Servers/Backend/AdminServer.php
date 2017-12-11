<?php
namespace App\Servers\Backend;

use App\Repositories\Backend\AdminRepository;

class AdminServer extends CommonServer
{

    public function __construct(
        AdminRepository $adminRepository
    ) {
        $this->adminRepository = $adminRepository;
    }

    /**
     * 列表
     * @param  Array $input [search]
     * @return Array
     */
    public function lists($input)
    {
        $search            = isset($input['search']) ? (array) $input['search'] : [];
        $result['lists']   = $this->adminRepository->getLists($search);
        $result['options'] = $this->adminRepository->getOptions();
        return returnSuccess('请求成功', $result);
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
            return returnError('必填字段不得为空');
        }

        // 用户名和邮箱重复判断
        $list = $this->adminRepository->getListByWhere(['username' => $username, 'email' => ['or', $email]]);
        if (!empty($list)) {
            return returnError($list->username == $username ? '用户名已存在' : '邮箱已存在');
        }

        $result['list'] = $this->adminRepository->store($username, $email, $password, $permission_id, $status);
        return returnSuccess('新增成功', $result);
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
            return returnError('必填字段不得为空');
        }

        // 用户名和邮箱重复判断
        $list = $this->adminRepository->getListByWhere(['username' => $username, 'email' => ['or', $email], 'id' => ['!=', $id]]);
        if (!empty($list)) {
            return returnError($list->username == $username ? '用户名已存在' : '邮箱已存在');
        }

        $result = $this->adminRepository->update($id, $username, $email, $password, $permission_id, $status);
        return returnSuccess('更新成功');
    }

    /**
     * 删除
     * @param  Int $id
     * @return Array
     */
    public function destroy($id)
    {
        $result = $this->adminRepository->destroy($id);
        return returnSuccess('删除成功');
    }
}
