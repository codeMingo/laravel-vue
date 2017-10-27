<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Base extends Model
{

    /**
     * 查询
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  Array $where_arr 查询条件[key, value, condition]
     * @return \Illuminate\Database\Eloquent\Builder  $query
     */
    public function scopeParseWheres($query, $where_arr)
    {
        if (empty($where_arr)) {
            return $query;
        }

        $condition_arr = [
            'between'     => 'whereBetween',
            'not_between' => 'whereNotBetween',
            'in'          => 'whereIn',
            'or'          => 'orWhere',
            'not_in'      => 'whereNotIn',
        ];
        foreach ($where_arr as $key => $item) {
            if (is_string($item)) {
                $query->where($key, $item);
                continue;
            }
            if (!isset($item['value'])) {
                continue;
            }
            if (!isset($item['condition'])) {
                $query->where($key, $item['value']);
                continue;
            }
            if (isset($condition_arr[$item['condition']])) {
                $where_condition = $condition_arr[$item['condition']];
                $query->$where_condition($key, $item['value']);
            }else {
                $where_value = $item['condition'] == 'like' ? '%' . $item['value'] . '%' : $item['value'];
                $query->where($key, $item['condition'], $where_value);
            }
        }

        return $query;
    }
}
