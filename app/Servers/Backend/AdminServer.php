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
    public function index($input)
    {
        $result['lists']   = $this->adminRepository->getLists($input);
        $result['options'] = $this->adminRepository->getOptions();

        return ['获取成功', $result];
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
            return ['code' => ['x00004', 'system']];
        }

        // 用户名和邮箱重复判断
        $search_where = [
            'filter' => ['username', 'email']
            'search' => [
                'username' => $username,
                'email'    => ['or', $email],
            ],
        ];
        $list = $this->adminRepository->getList($search_where);
        if (!empty($list)) {
            $code = $list->username == $username ? 'x00001' : 'x00002';
            return ['code' => [$code, 'system']];
        }

        $result['list'] = $this->adminRepository->store($username, $email, $password, $permission_id, $status);

        if (!$result['list']) {
            return ['code' => ['x00001', 'system']];
        }
        return ['新增成功', $result];
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
            return ['code' => ['x00004', 'system']];
        }

        // 用户名和邮箱重复判断
        $search_where = [
            'filter' => ['username', 'email']
            'search' => [
                'username' => $username,
                'email'    => ['or', $email],
                'id'       => ['!=', $id],
            ],
        ];
        $list = $this->adminRepository->getList($search_where);
        if (!empty($list)) {
            $code = $list->username == $username ? 'x00001' : 'x00002';
            return ['code' => [$code, 'system']];
        }

        $result = $this->adminRepository->update($id, $username, $email, $password, $permission_id, $status);

        if (!$result) {
            return ['code' => ['x00001', 'system']];
        }
        return ['更新成功', $result];
    }

    /**
     * 删除
     * @param  Int $id
     * @return Array
     */
    public function destroy($id)
    {
        $result = $this->adminRepository->destroy($id);

        if (!$result) {
            return ['code' => ['x00002', 'system']];
        }

        return ['删除成功', $result];
    }
}
