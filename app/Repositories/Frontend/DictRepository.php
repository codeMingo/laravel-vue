<?php
namespace App\Repositories\Frontend;

use App\Models\Dict;
use Illuminate\Support\Facades\DB;

class DictRepository extends BaseRepository
{

    /**
     * 获取字典通过code
     * @param  String $code
     * @return object
     */
    public function getListsByCode($code)
    {
        $lists = Dict::where('code', $code)->where('status', 1)->get();
        if ($lists->isEmpty()) {
            return [];
        }
        return $lists;
    }

    /**
     * 获取字典value通过code 和 text_en
     * @param  String $code
     * @param  String $text_en
     * @return Int    value
     */
    public function getValueByCodeAndTextEn($code, $text_en)
    {
        return DB::table('dicts')->where('code', $code)->where('text_en', $text_en)->where('status', 1)->value('value');
    }

    /**
     * 获取字典通过code
     * @param  String $code
     * @return object
     */
    public function getKeyValueByCode($code)
    {
        $lists = Dict::where('code', $code)->where('status', 1)->get();
        if ($lists->isEmpty()) {
            return [];
        }
        $result = [];
        foreach ($lists as $key => $value) {
            $result[$value->text_en] = $value->value;
        }
        return $result;
    }
}
