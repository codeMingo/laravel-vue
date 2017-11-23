<?php
namespace App\Repositories\Common;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

abstract class BaseRepository
{
    protected static $instance;
    protected $user_id;

    //获取实例化
    public static function getInstance()
    {
        $class = get_called_class();
        if (!isset(self::$instance[$class])) {
            self::$instance[$class] = new $class;
        }
        return self::$instance[$class];
    }

    /**
     * 响应返回
     * @param  bool $status  true or false
     * @param  array  $data    返回结果集
     * @param  string $message 消息提示
     * @return json
     */
    public function responseResult($status, $data = [], $message = '')
    {
        return [
            'status'  => $status,
            'data'    => $data,
            'message' => $message === '' ? (!$status ? '失败' : '成功') : $message,
        ];
    }

    /**
     * 获取当前用户id
     * @return Int
     */
    public function getUserId()
    {
        if (Auth::guard('web')->check()) {
            return Auth::guard('web')->id();
        } else {
            return 0;
        }
    }

    /**
     * 获取redis的值，string  和 hash
     * @param  Array $key_arr [key => [filed1, filed2], key, ...]
     * @return Array
     */
    public function getRedisDictLists($key_arr)
    {
        $result = [];
        if (empty($key_arr)) {
            return $result;
        }
        $flag = true;
        foreach ($key_arr as $key => $item) {
            $redis_key = 'dicts_' . $key;
            // 表示为 string 类型
            if (is_string($item)) {
                // 如果redis值为空，则清除所有缓存，重新生成
                if ($flag && !Redis::exists($item)) {
                    $flag = false;
                    $this->refreshRedisCache();
                }
                $result[$item] = Redis::get($item);
            } else {
                // 表示为 hash
                $field_arr = array_values($item);
                foreach ($field_arr as $field) {
                    if ($flag && !Redis::hexists($redis_key, $field)) {
                        $flag = false;
                        $this->refreshRedisCache();
                    }
                    $result[$key][$field] = Redis::hget($redis_key, $field);
                }
            }
        }
        return $result;
    }

    /**
     * 清空redis缓存，并且重新生成缓存
     * @return [type] [description]
     */
    public function refreshRedisCache()
    {
        Redis::flushdb();

        // dicts字典表缓存
        $dict_lists = DB::table('dicts')->where('status', 1)->get();
        if (!empty($dict_lists)) {
            $dict_redis_key = 'dicts_';
            foreach ($dict_lists as $key => $dict) {
                Redis::hset('dicts_' . $dict->code, $dict->text_en, $dict->value);
            }
        }
        return true;
    }
}
