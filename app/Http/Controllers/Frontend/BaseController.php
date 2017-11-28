<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Repositories\Common\ApiRepository;
use Illuminate\Support\Facades\Redis;

class BaseController extends Controller
{
    public function __construct()
    {
        // 判断是否生成缓存
        if (!Redis::exist('has_cache')) {
            ApiRepository::getInstance()->refreshCache();
        }
        $this->middleware(function ($request, $next) {
            // 请求频繁直接返回
            if (!$this->repeatMoreOperate($request)) {
                return response()->json([
                    'status'  => 0,
                    'data'    => [],
                    'message' => '请求过于频繁，请不要重复操作',
                ]);
            }
            return $next($request);
        });
    }

    /**
     * 重复请求限制
     * @param  Request  $request 请求
     * @return bool
     */
    public function repeatMoreOperate($request)
    {
        if ($request->isMethod('get')) {
            return true;
        }

        $redis_key = 'repeat_more:' . getClientIp() . ':' . $request->path();
        if (Redis::exists($redis_key) && Redis::get($redis_key) > config('ububs.repeat_max_limit')) {
            return false;
        }
        Redis::setnx($redis_key, 1);
        Redis::incr($redis_key);
        Redis::expire($redis_key, config('ububs.repeat_max_time_limit'));
        return true;
    }
}
