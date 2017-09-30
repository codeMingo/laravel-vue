<?php
namespace App\Repositories\Backend;

use App\Models\Admin;
use App\Models\AdminLoginRecord;
use App\Repositories\Backend\DictRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class LoginRepository extends BaseRepository
{

    /**
     * 登录
     * @param  Array $data [username, password]
     * @param  Request $request
     * @return Array
     */
    public function login($input, $request)
    {
        $username   = isset($input['username']) ? strval($input['username']) : '';
        $password   = isset($input['password']) ? strval($input['password']) : '';
        $ip_address = $request->get_client_ip();

        if (!$username || !$password) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '登录失败，必填字段不得为空',
            ];
        }

        // redis 限制用户、ip不可登录
        if (Redis::sismember('limitBackendLoginUser', $username) || Redis::sismember('limitBackendLoginIp', $ip_address)) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '登录失败，您已被限制登录，请联系管理员',
            ];
        }

        // redis 连续登录错误超过10次，1小时内禁止登录
        $redisKey   = 'backednLoginTimes:' . $ip_address;
        $redisExist = Redis::exists($redisKey);
        if ($redisExist && Redis::get($redisKey) > 10) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '登录失败，登录次数超过限制，请一小时后操作',
            ];
        }

        if (!Auth::guard('admin')->attempt(['username' => $username, 'password' => $password])) {
            // redis 记录登录错误次数
            if ($redisExist) {
                Redis::incr($redisKey);
            } else {
                Redis::set($redisKey, 1);
            }
            Redis::expire($redisKey, DictRepository::getInstance()->getDictValueByTextEn('backend_login_limit_time'));

            AdminLoginRecord::create([
                'admin_id'   => '',
                'params'     => json_encode($input),
                'text'       => '登录失败，用户名或密码错误',
                'ip_address' => ;
                'status'     => 0,
            ]);
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '登录失败，用户名或密码错误',
            ];
        }

        // 登录成功
        Redis::del($redisKey);

        $adminList = Auth::guard('admin')->user();

        if (!$adminList->status) {
            Auth::guard('admin')->logout();

            // 记录登录日志
            AdminLoginRecord::create([
                'admin_id'   => $adminList->id,
                'params'     => json_encode($input),
                'text'       => '登录失败，帐号被限制',
                'ip_address' => $ip_address;
                'status'     => 0,
            ]);

            // redis 加入限制名单
            Redis::sadd('limitBackendLoginUser', $username);

            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [];
                'message' => '登录失败，帐号被限制',
            ];
        };

        $updateResult = Admin::where('id', $adminList->id)->update([
            'last_login_ip'   => $ip_address,
            'last_login_time' => date('Y-m-d H:i:s', time()),
        ]);

        // 记录登录日志
        AdminLoginRecord::create([
            'admin_id'   => $adminList->id,
            'params'     => json_encode($input),
            'text'       => !$updateResult ? '登录失败，发生未知错误' : '登录成功',
            'ip_address' => $ip_address;
            'status'     => !!$updateResult,
        ]);

        $returnData['data'] = [
            'username' => $adminList->username,
            'email'    => $adminList->email,
        ];
        return [
            'status'  => !$updateResult ? Parent::ERROR_STATUS : Parent::SUCCESS_STATUS,
            'data'    => !$updateResult ? [] : $returnData,
            'message' => !$updateResult ? '登录失败，发生未知错误' : '登录成功',
        ];
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
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'message' => '退出成功',
        ];
    }
}
