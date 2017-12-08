<?php
namespace App\Repositories\Common;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Schema;

abstract class BaseRepository
{

    public $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    // 记录操作日志
    abstract protected function saveOperateRecord($input);

    // 获取当前用户id
    abstract protected function getCurrentId();

    /**
     * 插入一条数据
     * @param  Array  $data 数据
     * @return Object
     */
    public function insertData(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * 插入多条数据
     * @param  Array $data 插入数据
     * @return Bool
     */
    public function insertMultipleData(Array $data)
    {
        return $this->model->insert($data);
    }

    /**
     * 删除一条或多条数据
     * @param  Array|Int $ids 删除的主键id
     * @return Bool
     */
    public function deleteDataById($ids)
    {
        return (bool) $this->model->destroy($ids);
    }

    /**
     * 删除数据(条件)
     * @param  Array  $where 删除条件
     * @return Bool
     */
    public function deleteByWhere(array $where)
    {
        return (bool) $this->model->parseWheres($where)->delete();
    }

    /**
     * 查询详情
     * @param  Int $id 日志id
     * @param  Bool $with_trashed 查询软删除数据
     * @return Array
     */
    public function getDetail($id, $with_trashed = false)
    {
        $query = $this->model;
        if ($with_trashed) {
            $query = $query->withTrashed();
        }
        return $query->find($id);
    }

    /**
     * 获取当前表对象的表结构
     * @return Array
     */
    public function getTableColumns($table_name = '')
    {
        $table_name = empty($table_name) ? $this->mdoel->getTable() : $table_name;
        return Schema::getColumnListing($table_name);
    }

    /**
     * 过滤，重组查询参数
     * @param  Array $params
     * @return Array [key => [condition=>'', value=>'']]
     */
    public function parseParams(Array $params, $table_name = '')
    {
        if (empty($params)) {
            return [];
        }
        $table_name = empty($table_name) ? $this->mdoel->getTable() : $table_name;
        $field_lists = $this->getTableColumns($table_name);
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
