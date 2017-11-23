<?php
namespace App\Repositories\Backend;

use App\Models\Admin;
use App\Models\AdminLoginRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class LoginRepository extends BaseRepository
{

    /**
     * 登录
     * @param  Array $data [username, password]
     * @return Array
     */
    public function login($input)
    {
        $username   = isset($input['username']) ? strval($input['username']) : '';
        $password   = isset($input['password']) ? strval($input['password']) : '';
        $ip_address = getClientIp();

        if (!$username || !$password) {
            return $this->responseResult(false, [], '登录失败，必填字段不得为空');
        }

        // redis 限制用户、ip不可登录
        if (Redis::sismember('limitBackendLoginUser', $username) || Redis::sismember('limitBackendLoginIp', $ip_address)) {
            return $this->responseResult(false, [], '登录失败，您已被限制登录，请联系管理员');
        }

        // redis 连续登录错误超过10次，1小时内禁止登录
        $redisKey   = 'backednLoginTimes:' . $ip_address;
        $redisExist = Redis::exists($redisKey);
        if ($redisExist && Redis::get($redisKey) > 10) {
            return $this->responseResult(false, [], '登录失败，登录次数超过限制，请稍后操作');
        }

        if (!Auth::guard('admin')->attempt(['username' => $username, 'password' => $password])) {
            // redis 记录登录错误次数
            if ($redisExist) {
                Redis::incr($redisKey);
            } else {
                Redis::set($redisKey, 1);
            }
            Redis::expire($redisKey, 10);

            AdminLoginRecord::create([
                'params'     => json_encode($input),
                'text'       => '登录失败，帐号被限制',
                'ip_address' => $ip_address,
                'status'     => 0,
            ]);
            return $this->responseResult(false, [], '登录失败，用户名或密码错误');
        }

        // 登录成功
        Redis::del($redisKey);

        $list = Auth::guard('admin')->user();

        if (!$list->status) {
            Auth::guard('admin')->logout();

            // 记录登录日志
            AdminLoginRecord::create([
                'admin_id'   => $list->id,
                'params'     => json_encode($input),
                'text'       => '登录失败，帐号被限制',
                'ip_address' => $ip_address,
                'status'     => 0,
            ]);

            // redis 加入限制名单
            Redis::sadd('limitBackendLoginUser', $username);

            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '登录失败，帐号被限制',
            ];
        };

        // 更新信息
        Admin::where('id', $list->id)->update([
            'last_login_ip'   => $ip_address,
            'last_login_time' => date('Y-m-d H:i:s', time()),
        ]);

        // 记录登录日志
        AdminLoginRecord::create([
            'admin_id'   => $list->id,
            'params'     => json_encode($input),
            'text'       => '登录成功',
            'ip_address' => $ip_address,
            'status'     => 1,
        ]);

        $result['data'] = [
            'username'        => $list->username,
            'email'           => $list->email,
            'permission_text' => DB::table('admin_permissions')->where('id', $list->permission_id)->where('status', 1)->value('text'),
        ];
        return $this->responseResult(true, $result, '登录成功');
    }

    /**
     * 获取登录信息
     * @return Array
     */
    public function loginStatus()
    {
        $result = [];
        if (Auth::guard('admin')->check()) {
            $list           = Auth::guard('admin')->user();
            $result['list'] = [
                'username'        => $list->username,
                'email'           => $list->email,
                'permission_text' => DB::table('admin_permissions')->where('id', $list->permission_id)->where('status', 1)->value('text'),
            ];
        }
        return $this->responseResult(true, $result);
    }

    public function reset($input)
    {

    }

    /**
     * 退出
     * @return Array
     */
    public function logout()
    {
        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        }
        return $this->responseResult(true, [], '退出成功');
    }
}
