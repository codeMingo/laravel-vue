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
    public function insertMultipleData(array $data)
    {
        return $this->model->insert($data);
    }

    /**
     * 删除一条或多条数据
     * @param  Array|Int $ids 删除的主键id
     * @return Bool
     */
    public function deleteById($ids)
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
     * @param  Int $id 主键
     * @param  Array $where 查询条件
     * @param  Bool $with_trashed 查询软删除数据
     * @return Array
     */
    public function getDetail($id, $where = [], $with_trashed = false)
    {
        $where = $this->parseParams($where);
        $query = $this->model->parseWheres($where);
        if ($with_trashed) {
            $query = $query->withTrashed();
        }
        return $query->find($id);
    }

    /**
     * 获取一条数据
     * @param  Array $where 查询条件
     * @return Object
     */
    public function getListByWhere($where)
    {
        $query = $this->model;
        if (empty($where)) {
            return $query->model->first();
        }
        foreach ($where as $key => $item) {
            if (is_string($item)) {
                $query->where($key, $item);
            } else {
                if (!isset($item[0]) || !isset($item[1])) {
                    continue;
                }
                switch ($item[0]) {
                    case '=':
                        $query->where($item[0], $item[1]);
                        break;
                    case '!=':
                        $query->where($item[0], '!=', $item[1]);
                        break;
                    case 'or':
                        $query->orWhere($item[0], $item[1]);
                        break;
                    case 'like':
                        $query->where($item[0], 'like', '%' . $item[1] . '%');
                        break;
                    case 'in':
                        $query->whereIn($item[0], $item[1]);
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }
        return $query->first();
    }

    /**
     * 获取多条数据，pagination分页
     * @param  array   $where        查询条件
     * @param  boolean $with_trashed 是否查找软删除数据
     * @return Object
     */
    public function getPaginateLists($where = [], $with_trashed = false)
    {
        $where = $this->parseParams($where);
        $query = $this->model->parseWheres($where);
        if ($with_trashed) {
            $query = $query->withTrashed();
        }
        return $query->paginate();
    }

    /**
     * 获取多条数据，不分页
     * @param  array   $where        查询条件
     * @param  boolean $with_trashed 是否查找软删除数据
     * @return Object
     */
    public function getLists($where = [], $with_trashed = false)
    {
        $where = $this->parseParams($where);
        $query = $this->model->parseWheres($where);
        if ($with_trashed) {
            $query = $query->withTrashed();
        }
        return $query->get();
    }

    /**
     * 获取当前表对象的表结构
     * @return Array
     */
    public function getTableColumns($table_name = '')
    {
        $table_name = empty($table_name) ? $this->model->getTable() : $table_name;
        return Schema::getColumnListing($table_name);
    }

    /**
     * 过滤，重组查询参数
     * @param  Array $params
     * @return Array [key => [condition=>'', value=>'']]
     */
    public function parseParams(array $params, $table_name = '')
    {
        if (empty($params)) {
            return [];
        }
        $table_name  = empty($table_name) ? $this->model->getTable() : $table_name;
        $field_lists = $this->getTableColumns($table_name);
        $param_rules = isset(config('param_rules.param_rules')[$table_name]) ? config('param_rules.param_rules')[$table_name] : []; // 获取过滤规则
        $result      = [];
        foreach ($params as $key => $value) {
            // 参数不在表内直接过滤
            $select_filter = ['__select__', '__not_select__', '__relation_table__', '__order_by__'];
            if ((!in_array($key, $field_lists) || $value === '' || $value === []) && !in_array($key, $select_filter)) {
                continue;
            }
            // 得到最后参数
            if (in_array($key, $select_filter)) {
                if (!is_array($value)) {
                    continue;
                }
                // 关联其他表
                if ($key === '__relation_table__') {
                    $result['__relation_table__'] = $value;
                    continue;
                }
                // 排序
                if ($key === '__order_by__') {
                    $result['__order_by__'] = $value;
                    continue;
                }
                $fields = [];
                foreach ($value as $field) {
                    if (in_array($field, $field_lists)) {
                        $fields[] = $field;
                    }
                }
                if ($key === '__select__') {
                    $result['__select__'] = $fields;
                } elseif ($key === '__not_select__') {
                    $result['__select__'] = array_diff($field_lists, $fields);
                }
                continue;
            }
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
