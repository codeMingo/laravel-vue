<?php
namespace App\Repositories\Common;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Schema;

abstract class BaseRepository
{

    public function __construct()
    {
        
    }

    // 记录操作日志
    abstract protected function saveOperateRecord($input);

    // 获取当前用户id
    abstract protected function getCurrentId();

    /**
     * 过滤，重组查询参数
     * @param  Array $params
     * @return Array [key => [condition=>'', value=>'']]
     */
    public function parseParams($table_name, $params)
    {
        if (empty($params)) {
            return [];
        }
        $field_lists = Schema::getColumnListing($table_name); // 获取数据表所有字段
        $param_rules = isset(config('ububs.param_rules')[$table_name]) ? config('ububs.param_rules')[$table_name] : []; // 获取过滤规则
        $result      = [];
        foreach ($params as $key => $value) {
            // 参数不在表内直接过滤
            if (!in_array($key, $field_lists) || $value === '' || $value === []) {
                continue;
            }
            // 参数过滤方式
            $result[$key] = [
                'condition' => isset($param_rules[$key]) ? $param_rules[$key] : '=',
                'value'     => $value,
            ];
        }
        return $result;
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
