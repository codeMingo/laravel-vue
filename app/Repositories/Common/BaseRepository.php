<?php
namespace App\Repositories\Common;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Schema;

abstract class BaseRepository
{

    public $model;
    public $articleComment;
    public $articleRead;
    public $tags;
    public $interact;
    public $user;
    public $adminPermission;
    public $admin;
    public $dicts = [];

    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->dicts = $this->getAllDicts();
    }

    // 记录操作日志
    abstract protected function saveOperateRecord($input);

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
        return (bool) $this->model->parseSearch($where)->delete();
    }

    /**
     * 更新数据
     * @param  array  $where 查询条件
     * @param  array  $data  数据
     * @return boolean
     */
    public function updateByWhere(array $where, array $data)
    {
        return (bool) $this->model->parseSearch($where)->update($data);
    }

    /**
     * 判断是否存在一条数据
     * @param  array  $where        查询条件
     * @param  boolean $with_trashed 是否查询软删除数据
     * @return boolean
     */
    public function existList($where, $with_trashed = false)
    {
        return $this->model->parseExist($where);
    }

    /**
     * 获取数据详情根据id
     * @param  int $id 主键
     * @param  boolean $with_trashed 是否查询软删除数据
     * @return object
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
     * 获取一条数据
     * @param  array   $where        查询条件
     * @param  boolean $with_trashed 是否查询软删除数据
     * @return Object
     */
    public function getListByParseWheres($where, $with_trashed = false)
    {
        $query = $this->model->parseWheres($where);
        if ($with_trashed) {
            $query = $query->withTrashed();
        }
        return $query->first();
    }

    /**
     * 获取多条数据，pagination分页
     * @param  array   $where        查询条件
     * @param  boolean $with_trashed 是否查找软删除数据
     * @return Object
     */
    public function getListsByParseWheres($where = [], $with_trashed = false)
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
     * @param  array $where    查询条件
     * @param  string $field 字段
     * @return string
     */
    public function getValueByWhere($where, $field)
    {
        return $this->model->parseSearch($where)->value($field);
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

    /**
     * 获取字典redis
     * @return array
     */
    public function getAllDicts()
    {
        $lists  = Redis::hgetall('dicts');
        if (empty($lists)) {
            return false;
        }
        $result = [];
        if (!empty($lists)) {
            foreach ($lists as $key => $value) {
                $result[$key] = json_decode($value, true);
            }
        }
        return $result;
    }

    /**
     * 过滤，合并参数
     * @param  array $default_wheres 默认参数
     * @param  array $wheres         参数
     * @return array
     */
    public function parseParams($default_wheres, $wheres)
    {
        if (empty($wheres)) {
            return $default_wheres;
        }
        $result = [];
        foreach ($default_wheres as $type => $item) {
            if (!isset($wheres[$type])) {
                $result[$type] = $item;
                unset($wheres[$type]);
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
                unset($wheres[$type]);
            }
        }
        if (!empty($wheres)) {
            $result = array_merge($result, $wheres);
        }
        return $result;
    }
}
