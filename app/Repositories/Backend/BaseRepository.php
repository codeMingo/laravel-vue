<?php
namespace App\Repositories\Backend;

use App\Models\Dict;

abstract class BaseRepository
{
    protected static $instance;
    const ERROR_STATUS   = 0; //失败状态
    const SUCCESS_STATUS = 1; //成功状态

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
     * 获取字段
     * @param  Array $code_arr
     * @return Array
     */
    public function getDicts($code_arr)
    {
        $result = [];
        if (!empty($code_arr) && is_array($code_arr)) {
            $lists = Dict::whereIn('code', $code_arr)->get();
            foreach ($code_arr as $item) {
                $result[$item] = $lists->where('code', $item)->values()->toArray();
            }
        }
        return $result;
    }
}
