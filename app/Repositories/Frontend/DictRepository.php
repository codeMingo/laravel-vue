<?php
namespace App\Repositories\Frontend;

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
     * 根据 text_en 获取value
     * @param  String $textEn text_en
     * @return Int
     */
    public function getDictValueByTextEn($text_en)
    {
        return Dict::where('text_en', $text_en)->value('value');
    }
}
