<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Redis;
use App\Repositories\Frontend\DictRepository;

class BaseController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // 请求频繁直接返回
            if (!$request->isMethod('get')) {
                if (!$this->repeatOperation($request->path())) {
                    return response()->json([
                        'status' => 0,
                        'data' => [],
                        'message' => '请求过于频繁，请不要重复操作'
                    ]);
                }
            }
            return $next($request);
        });
    }

    /**
     * 重复请求限制
     * @param  String  $action 请求的路由
     * @return bool
     */
    public function repeatOperation($action)
    {
        return true;
    }
}
