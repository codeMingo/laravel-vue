<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Base extends Model
{

    /**
     * 查询
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  Array $param_rules 查询条件[key, value]
     * @return \Illuminate\Database\Eloquent\Builder  $query
     */
    public function scopeParseWheres($query, $param_rules)
    {
        if (empty($param_rules)) {
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

        foreach ($param_rules as $key => $item) {
            $condition = isset($condition_arr[$item['condition']]) ? $condition_arr[$item['condition']] : 'where';
            if ($item['condition'] == 'like') {
                $query->where($key, $item['condition'], '%' . $item['value'] . '%');
            } else {
                $query->$condition($key, $item['value']);
            }
        }

        return $query;
    }
}
