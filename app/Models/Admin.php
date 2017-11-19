<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\Authenticatable as ContractsAuthenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Authenticatable as AuthAuthenticatable;

class Admin extends Base implements ContractsAuthenticatable
{
    use Notifiable;
    use SoftDeletes;
    use AuthAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password', 'permission_id', 'status',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    // 关联菜单表
    public function adminPermission()
    {
        return $this->hasOne('App\Models\AdminPermission', 'id', 'admin_id');
    }

    // 关联登录日志表
    public function adminLoginRecord()
    {
        return $this->hasMany('App\Models\AdminLoginRecord', 'id', 'admin_id');
    }

    // 关联操作日志表
    public function adminOperateRecord()
    {
        return $this->hasMany('App\Models\AdminOperateRecord', 'id', 'admin_id');
    }
}
