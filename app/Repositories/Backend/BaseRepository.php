<?php
namespace App\Repositories\Backend;

use App\Models\AdminOperateRecord;
use App\Models\Dict;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
            'status' => $status,
            'data' =>  $data,
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
        $result = [];
        foreach ($params as $key => $value) {
            // 参数不在表内直接过滤
            if (!in_array($key, $field_lists) || $value === '' || $value === []) {
                continue;
            }
            // 参数过滤方式
            $result[$key] = [
                'condition' => isset($param_rules[$key]) ? $param_rules[$key] : '=',
                'value' => $value
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
        try {
            AdminOperateRecord::create([
                'admin_id'   => Auth::guard('admin')->id(),
                'action'     => $input['action'],
                'params'     => json_encode($input['params']),
                'text'       => $input['text'],
                'ip_address' => getClientIp(),
                'status'     => $input['status'],
            ]);
        } catch (Exception $e) {
            file_put_contents('../storage/errorLogs/backend/' . data('Y-m-d', time()) . '.txt', 'function saveOperateRecord is error，params :' . json_encode($input) . PHP_EOL, FILE_APPEND);
        }
    }


}
