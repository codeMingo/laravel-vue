<?php
namespace App\Repositories\Backend;

use App\Models\Admin;
use App\Models\AdminLoginRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class LoginRepository extends CommonRepository
{

    public function __construct(Admin $admin)
    {
        parent::__construct($admin);
    }

    /**
     * 登录
     * @param  Array $data [username, password]
     * @return Array
     */
    public function login($account, $password)
    {

        if (strpos($account, '@')) {
            //邮箱登录
            $flag = Auth::guard('admin')->attempt(['email' => $account, 'password' => $password, 'status' => 1]);
        } else {
            $flag = Auth::guard('admin')->attempt(['username' => $account, 'password' => $password, 'status' => 1]);
        }
        if (!$flag) {
            return false;
        }
        $result = Auth::guard('admin')->user();
        $this->model->where('id', $result['id'])->update([
            'last_login_time' => date('Y-m-d H:i:s', time()),
            'last_login_ip'   => getClientIp(),
        ]);
        return $result;
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
        return true;
    }
}
