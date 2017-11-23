<?php
namespace App\Repositories\Backend;

use App\Models\AdminOperateRecord;
use App\Models\Dict;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

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
     * 获取字典数据
     * @param  Array $code_arr
     * @return Object
     */
    public function getDictsByCodeArr($code_arr)
    {
        $result = [];
        if (!empty($code_arr) && is_array($code_arr)) {
            $result = Dict::whereIn('code', $code_arr)->get();
        }
        return $result;
    }

    /**
     * 记录操作日志
     * @param  Array  $input [action, params, text, status]
     * @return Array
     */
    public function saveOperateRecord($input)
    {
        AdminOperateRecord::create([
            'admin_id'   => Auth::guard('admin')->id(),
            'action'     => $input['action'],
            'params'     => json_encode($input['params']),
            'text'       => $input['text'],
            'ip_address' => getClientIp(),
            'status'     => $input['status'],
        ]);
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
        foreach ($key_arr as $redis_key => $item) {
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
                    $result[$redis_key][$field] = Redis::hget($redis_key, $field);
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
