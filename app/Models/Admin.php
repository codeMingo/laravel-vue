<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Base implements Authenticatable
{
    use Notifiable;
    use SoftDeletes;

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

    /**
     * 获取权限对应的人数
     */
    public static function getNumLists($permissionIds)
    {
        $lists  = Admin::whereIn('id', $permissionIds)->get();
        $result = [];
        foreach ($lists as $key => $value) {
            if (isset($result[$value['permission_id']])) {
                $result[$value['permission_id']] += 1;
            } else {
                $result[$value['permission_id']] = 0;
            }
        }
        return $result;
    }
}
