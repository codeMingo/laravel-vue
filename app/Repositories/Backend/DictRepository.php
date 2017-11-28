<?php
namespace App\Repositories\Backend;

use App\Models\Dict;

class DictRepository extends CommonRepository
{

    /**
     * 获取字典通过code
     * @param  Array $code_arr
     * @return Array [text, value]
     */
    public function getDictListsByCodeArr($code_arr)
    {
        $lists = Dict::whereIn('code', $code_arr)->where('status', 1)->get();
        if ($lists->isEmpty()) {
            return [];
        }
        foreach ($lists as $key => $item) {
            $temp['value']         = $item->value;
            $temp['text']          = $item->text;
            $result[$item->code][] = $temp;
        }
        return $result;
    }

    /**
     * 判断dict是否存在
     * @param  Array $code_value_arr [code => $value]
     * @return Boolean
     */
    public function existDict($code_value_arr)
    {
        if (empty($code_value_arr)) {
            return false;
        }
        $code_arr = [];
        foreach ($code_value_arr as $code => $value) {
            $code_arr[] = $code;
        }
        $lists = Dict::where('status', 1)->whereIn('code', $code_arr)->get();
        if (empty($lists)) {
            return false;
        }
        $count = 0;
        foreach ($code_value_arr as $code => $value) {
            foreach ($lists as $key => $item) {
                if ($code == $item->code && $value == $item->value) {
                    $count++;
                }
            }
        }
        return count($code_value_arr) == $count;
    }
}
