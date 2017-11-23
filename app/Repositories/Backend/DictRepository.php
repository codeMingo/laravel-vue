<?php
namespace App\Repositories\Backend;

use App\Models\Dict;

class DictRepository extends BaseRepository
{

    /**
     * 获取字典通过code
     * @param  Array $code
     * @return Array [text, value]
     */
    public function getDictListsByCode($code_arr)
    {
        $dictLists = Dict::where('code', $code_arr)->get();
        if ($dictLists->isEmpty()) {
            return [];
        }
        foreach ($dictLists as $key => $item) {
            $temp_list['value'] = $item->value;
            $temp_list['text']  = $item->text;
            $result[]           = $temp_list;
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
        foreach ($code_value_arr as $code => $value) {
            $code_arr[] = $code;
        }
        $dictLists = Dict::where('status', 1)->whereIn('code', $code_arr)->get();
        $count     = 0;
        foreach ($code_value_arr as $code => $value) {
            foreach ($dictLists as $key => $item) {
                if ($code == $item->code && $value == $item->value) {
                    $count++;
                }
            }
        }
        return count($code_value_arr) === $count ? true : false;
    }
}
