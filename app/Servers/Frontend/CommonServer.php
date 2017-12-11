<?php
namespace App\Servers\Frontend;

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
        if (Auth::guard('web')->check()) {
            return Auth::guard('web')->id();
        } else {
            return 0;
        }
    }
}
