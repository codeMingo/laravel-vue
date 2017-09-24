<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
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
     *
     */
    public static function lists($searchForm)
    {
        $whereParams = [];
        if (issetAndNotEmpty($searchForm['permission_id'])) {
            $whereParams['permission_id'] = $searchForm['permission_id'];
        }
        $query = Admin::where($whereParams);
        if (issetAndNotEmpty($searchForm['username'])) {
            $query->where('username', 'like', '%' . $searchForm['username'] . '%');
        }
        return $query->paginate(config('blog.pageSize'));
    }

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
