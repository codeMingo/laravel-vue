<?php
namespace App\Repositories\Backend;

use App\Models\AdminPermission;

class AdminPermissionRepository extends CommonRepository
{

    public function __construct(AdminPermission $adminPermission)
    {
        parent::__construct($adminPermission);
    }

    /**
     * 获取权限节点数量
     * @param  Int $id 权限id
     * @return Array
     */
    public function getPermissionNodeCount($id)
    {
        $list = $this->getAdminPermissionList($id);
        if (empty($list)) {
            return responseResult(false, [], '获取失败，不存在这个权限');
        }
        $result['count'] = !empty($list['permission_includes']) ? count(implode(',', $list['permission_includes'])) : 0;
        return responseResult(true, $result);
    }

    /**
     * 获取一条权限节点
     * @param  Int $id 权限id
     * @return Object
     */
    public function getAdminPermissionList($id)
    {
        return $this->model->where('id', $id)->where('status', 1)->first();
    }

}
