<?php
namespace App\Repositories\Frontend;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserRepository extends BaseRepository
{

    public $table_name = 'users';

    public function userData()
    {
        $user_id        = Auth::guard('web')->id();
        $result['list'] = $this->getUserList($user_id);
        return [
            'status'  => Parent::ERROR_STATUS,
            'data'    => $result,
            'message' => '数据获取成功',
        ];
    }

    public function getUserList($user_id)
    {
        return User::select(['id', 'username', 'email', 'sign', 'web_url'])->where('id', $user_id)->where('status', 1)->where('active', 1)->first();
    }

    public function updateUser($input, $user_id)
    {
        $username = isset($input['username']) ? strval($input['username']) : '';
        $sign     = isset($input['sign']) ? strval($input['sign']) : '';
        $web_url  = isset($input['web_url']) ? strval($input['web_url']) : '';

        if (!$username) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '必填信息不得为空',
            ];
        }
        $user_repeat = User::where('username', $username)->where('id', '!=', $user_id)->first();
        if (!empty($user_repeat)) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '用户名已经存在',
            ];
        }
        $update_result = User::where('id', $user_id)->update([
            'username' => $username,
            'sign'     => $sign,
            'web_url'  => $web_url,
        ]);

        // 记录操作日志
        Parent::saveUserOperateRecord([
            'action' => 'User/update-user',
            'params' => $input,
            'text'   => $update_result ? '修改成功' : '修改失败',
            'status' => !!$update_result,
        ]);

        $result['list'] = [];
        if ($update_result) {
            $result['list'] = $this->getUserList($user_id);
        }

        return [
            'status'  => $update_result ? Parent::SUCCESS_STATUS : Parent::ERROR_STATUS,
            'data'    => $update_result ? $result : [],
            'message' => $update_result ? '修改成功' : '修改失败，未知错误',
        ];
    }

}
