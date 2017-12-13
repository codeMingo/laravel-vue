<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    protected $server;

    public function __construct()
    {

    }

    /**
     * 响应返回
     * @param  array  $result 返回内容
     * @return Json
     */
    public function responseResult(array $result = [])
    {
        // 返回失败信息
        if (isset($result['code'])) {
            $code    = isset($result['code'][0]) ? $result['code'][0] : '';
            $module  = isset($result['code'][1]) ? $result['code'][1] : '';
            $message = isset(config('response_code.' . $module)[$code]) ? config('response_code.' . $module)[$code] : '';
            if (!$code || !$module || !$message) {
                return response()->json([
                    'status'  => 0,
                    'data'    => [],
                    'message' => '系统发生错误，请联系管理员，QQ:292304400，gmail：linlm1994@gmail.com',
                ]);
            }
            // 返回成功信息
            return response()->json([
                'status'  => 0,
                'data'    => [],
                'message' => $message,
            ]);
        }
        // 返回成功信息
        return response()->json([
            'status'  => 1,
            'data'    => isset($result[1]) ? $result[1] : [],
            'message' => isset($result[0]) ? $result[0] : '成功',
        ]);
    }
}
