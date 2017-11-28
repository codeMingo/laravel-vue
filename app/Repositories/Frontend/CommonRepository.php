<?php
namespace App\Repositories\Frontend;

use App\Repositories\Common\BaseRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommonRepository extends BaseRepository
{
    /**
     * 获取当前用户id
     * @return Int
     */
    public function getCurrentId()
    {
        if (Auth::guard('web')->check()) {
            return Auth::guard('web')->id();
        } else {
            return 0;
        }
    }

    /**
     * 记录操作日志
     * @param  Array  $input [action, params, text, status]
     * @return Array
     */
    public function saveOperateRecord($input)
    {
        DB::table('user_operate_records')->insert([
            'user_id'    => $this->getCurrentId(),
            'action'     => isset($input['action']) ? strval($input['action']) : '',
            'params'     => isset($input['params']) ? json_encode($input['params']) : '',
            'text'       => isset($input['text']) ? strval($input['text']) : '操作成功',
            'ip_address' => getClientIp(),
            'status'     => isset($input['status']) ? intval($input['status']) : 1,
        ]);
    }
}
