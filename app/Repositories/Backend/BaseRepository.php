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
     * 过滤，重组查询参数
     * @param  Array $params
     * @return Array [key => [condition=>'', value=>'']]
     */
    public function parseParams($params)
    {
        if (empty($params)) {
            return [];
        }
        $field_lists = Schema::getColumnListing($this->table_name); // 数据表所有字段
        $param_rules = $this->params_rules; // 过滤规则
        $result = [];
        foreach ($params as $key => $value) {
            // 参数不在表内直接过滤
            if (!in_array($key, $field_lists) || $value === '' || $value === []) {
                continue;
            }
            // 参数过滤方式
            $result[$key] = [
                'condition' => (!empty($param_rules) && isset($param_rules[$key])) ? $param_rules[$key] : '=',
                'value' => $value
            ];
        }
        return $result;
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
            Log::info('RECORD FAIL：saveAdminOperateRecord is error，params :' . json_encode($input));
        }
    }
}
