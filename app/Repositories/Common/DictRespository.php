<?php
namespace App\Repositories\Common;

use App\Models\Dict;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DictRepository extends BaseRepository
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 记录操作日志
     * @param  Array  $input [action, params, text, status]
     * @return Array
     */
    public function saveOperateRecord($input)
    {

    }

    /**
     * 根据code获取列表
     * @param  String $code code
     * @return Object
     */
    public function getListsByCode($code)
    {
        if (!$code) {
            return [];
        }
        $result = DB::table('dicts')->where('code', $code)->where('status', 1)->get();
        return $result;
    }

    /**
     * 根据code_arr 获取列表
     * @param  Array $code_arr code数组
     * @return Object
     */
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
