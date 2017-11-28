<?php
namespace App\Repositories\Common;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

abstract class BaseRepository
{
    protected static $instance;

    //获取实例化
    public static function getInstance()
    {
        $class = get_called_class();
        if (!isset(self::$instance[$class])) {
            self::$instance[$class] = new $class;
        }
        return self::$instance[$class];
    }

    // 记录操作日志
    abstract protected function saveOperateRecord($input);

    // 获取当前用户id
    abstract protected function getCurrentId();

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
     * 获取redis的值，hash
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
            $field_arr = array_values($item);
            foreach ($field_arr as $field) {
                $result[$key][$field] = Redis::hget('dicts_' . $key, $field);
            }
        }
        return $result;
    }
}
