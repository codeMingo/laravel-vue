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
            if (!$this->repeatOperation($request->path())) {
                return response()->json([
                    'status' => 0,
                    'data' => [],
                    'message' => '请求过于频繁，请不要重复操作'
                ]);
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
        if (Redis::exists('limit_time_arr')) {
            $limit_arr = Redis::hgetAll('limit_time_arr');
        } else {
            $limit_arr = DictRepository::getInstance()->getDictListsByTextEnArr(['repeat_limit_time', 'repeat_limit_times']);
            foreach ($limit_arr as $key => $value) {
                Redis:: hset('limit_time_arr', $key, $value);
            }
        }
        $redisLimitKey   = 'limit_time:' . getClientIp() . ':' . $action;
        $redisLimitExist = Redis::exists($redisLimitKey);
        if ($redisLimitExist && Redis::get($redisLimitKey) > $limit_arr['repeat_limit_times']) {
            return false;
        }
        if ($redisLimitExist) {
            Redis::incr($redisLimitKey);
        } else {
            Redis::set($redisLimitKey, 1);
        }
        Redis::expire($redisLimitKey, $limit_arr['repeat_limit_time']);
        return true;
    }
}
