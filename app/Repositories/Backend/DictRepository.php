<?php
namespace App\Repositories\Backend;

use App\Models\Dict;
use Illuminate\Support\Facades\DB;

class DictRepository extends CommonRepository
{

    public function getListsByCodeArr($code_arr)
    {
        $result = [];
        if (empty($code_arr)) {
            return $result;
        }
        $lists = DB::table('dicts')->whereIn('code', $code_arr)->where('status', 1)->get();
        foreach ($lists as $key => $list) {
            $result[$list->code][] = $list;
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
