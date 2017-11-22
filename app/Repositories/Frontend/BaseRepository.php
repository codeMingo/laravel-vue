<?php
namespace App\Repositories\Frontend;

use App\Models\Dict;
use App\Models\UserOperateRecord;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Auth;

abstract class BaseRepository
{
    protected static $instance;
    const ERROR_STATUS   = 0; // 失败状态
    const SUCCESS_STATUS = 1; // 成功状态
    protected $current_model; // Model类

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
            if (!in_array($key, $field_lists) || $value === '' || $value === [] || $value === null) {
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
        UserOperateRecord::create([
            'user_id'    => Auth::guard('web')->id(),
            'action'     => validateValue($input['action']),
            'params'     => json_encode($input['params']),
            'text'       => validateValue($input['text'], 'string', '操作成功'),
            'ip_address' => getClientIp(),
            'status'     => validateValue($input['status'], 'int', 1),
        ]);
    }
}
