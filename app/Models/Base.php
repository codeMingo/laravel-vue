<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Base extends Model
{

    /**
     * 查询
     * @param  $query
     * @param  array $where 查询条件
     * @return $query
     * $where = [
     *     'filter' => ['id', 'title'],
     *     'search' => [
     *         'id' => 1,
     *         'title' => ['like', '林联敏'],
     *         'category_id' => ['in', [1, 2, 3]],
     *         'type' => ['not_in', [1, 2, 3]],
     *         'type' => ['or', 1],
     *         'type' => ['between', 1, 20],
     *         'type' => ['not_between', 1, 20],
     *         'id' => ['<', 5],
     *         'id' => ['>', 5],
     *     ],
     *     'sort' => ['id' => 'desc', 'created_at' => 'asc']
     * ]
     */
    public function scopeParseWheres($query, $where)
    {
        if (empty($where)) {
            return $query;
        }

        $condition_arr = [
            '='           => 'where',
            'between'     => 'whereBetween',
            'not_between' => 'whereNotBetween',
            'in'          => 'whereIn',
            'or'          => 'orWhere',
            'not_in'      => 'whereNotIn',
        ];

        foreach ($where as $type => $item) {
            // 字段筛选
            if (isset($item['filter']) && !empty($item['filter'])) {
                $query->select($item['filter']);
                unset($item['filter']);
            }
            // 查询条件
            if (isset($item['search']) && !empty($item['search'])) {
                foreach ($item['search'] as $field_key => $value) {
                    // =
                    if (is_string($value)) {
                        $query->where($field_key, $value);
                        continue;
                    }

                    $field_condition = isset($value[0]) ?? '';
                    $field_value = isset($value[1]) ?? '';
                    if (!$field_condition || !$field_value) {
                        continue;
                    }

                    $condition = isset($condition_arr[$field_condition]) ?? '';

                    // <  >  <=  !=
                    if (!$condition) {
                        $query->where($field_key, $condition, $field_value);
                        continue;
                    }

                    if ($condition == 'like') {
                        $field_value = '%' . $field_value . '%';
                    }

                    if ($condition == 'whereBetween' || $condition == 'whereNotBetween') {
                        $field_value_two = isset($value[2]) ?? '';
                        $query->$condition($field_key, $field_value, $field_value_two);
                        continue;
                    }
                    $query->$condition($field_key, $field_value);
                }
            }
            // 排序
            if (isset($item['sort']) && !empty($item['sort'])) {
                foreach ($item['sort'] as $field_key => $sort_type) {
                    $query->orderBy($field_key, $sort_type);
                }
            }
        }
        return $query;
    }

}
