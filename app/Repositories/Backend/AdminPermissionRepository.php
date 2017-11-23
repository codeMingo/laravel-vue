<?php
namespace App\Repositories\Backend;

use App\Models\AdminPermission;

class AdminPermissionRepository extends BaseRepository
{

    public $table_name   = 'admin_permissions';
    public $params_rules = [
        'id'                 => '=',
        'text'               => 'like',
        'permission_include' => 'in',
        'status'             => '=',
    ];

    public function getPermissionNodeCount($id)
    {
        $admin_permission_list = AdminPermission::find($id);
        $count                 = 0;
        if (!empty($admin_permission_list) && $admin_permission_list['permission_includes']) {
            $count = count(implode(',', $admin_permission_list['permission_includes']));
        }

        return [
            'status'  => !empty($admin_permission_list) ? Parent::ERROR_STATUS : Parent::SUCCESS_STATUS,
            'data'    => $count,
            'message' => !empty($admin_permission_list) ? '不存在这个权限' : '获取成功',
        ];
    }

}
