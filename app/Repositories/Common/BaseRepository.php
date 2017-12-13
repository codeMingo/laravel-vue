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
     * @param  int $id 主键
     * @param  array $where 查询条件
     * @param  boolean $with_trashed 查询软删除数据
     * @return object
     */
    public function getDetail($id, $where = [], $with_trashed = false)
    {
        $query = $this->model;
        if (!empty($where)) {
            $query = $query->parseWheres($where);
        }
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
    public function getLists($where = [], $with_trashed = false)
    {
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
    public function getAllLists($where = [], $with_trashed = false)
    {
        $query = $this->model->parseWheres($where);
        if ($with_trashed) {
            $query = $query->withTrashed();
        }
        return $query->get();
    }

    /**
     * 获取某条数据的值
     * @param  Int $id    id
     * @param  string $field 字段
     * @return string
     */
    public function getValueById($id, $field)
    {
        return $this->model->where('id', $id)->value($field);
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

    public function parseWheres($default_wheres, $wheres)
    {
        if (empty($wheres)) {
            return $default_wheres;
        }
        $result = [];
        foreach ($default_wheres as $type => $item) {
            if (!isset($wheres[$type])) {
                $result[$type] = $item;
                continue;
            }

            if (is_array($item) && is_array($wheres[$type])) {
                foreach ($item as $key => $value) {
                    if (isset($wheres[$type][$key])) {
                        $result[$type][$key] = $wheres[$type][$key];
                    } else {
                        $result[$type][$key] = $value;
                    }
                }
            }
        }
        return $result;
    }
}
