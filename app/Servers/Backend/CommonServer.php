<?php
namespace App\Servers\Backend;

use App\Servers\Common\BaseServer;
use Illuminate\Support\Facades\Auth;

class CommonServer extends BaseServer
{
    /**
     * 获取当前用户id
     * @return Int
     */
    public function getCurrentId()
    {
        if (Auth::guard('admin')->check()) {
            return Auth::guard('admin')->id();
        } else {
            return 0;
        }
    }
}
