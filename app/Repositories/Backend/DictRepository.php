<?php
namespace App\Repositories\Backend;

use App\Models\Dict;

class DictRepository extends BaseRepository
{

    public function getDictListsByCode($code)
    {
        $dictLists = Dict::where('code', $code)->get();
        if ($dictLists->isEmpty()) {
            echo '参数错误，请联系管理员';
            exit();
        }
        foreach ($dictLists as $key => $item) {
            $tempList['value'] = $item->value;
            $tempList['text'] = $item->text;
            $resultData[] = $tempList;
        }
        return $resultData;
    }

    /**
     * 根据 text_en Arr 获取对应内容
     * @param  Array $code_arr [text_en]
     * @return Array [text_en => value]
     */
    public function getDictListsByTextEnArr($text_en_arr)
    {
        $dictLists = Dict::whereIn('text_en', $text_en_arr)->where('status', 1)->get();
        if (count($text_en_arr) !== count($dictLists)) {
            echo '参数错误，请联系管理员';
            exit();
        }
        foreach ($dictLists as $key => $item) {
            $resultData[$item->text_en] = $item->value;
        }
        return $resultData;
    }

    /**
     * 根据 text_en 获取value
     * @param  String $textEn text_en
     * @return Int
     */
    public function getDictValueByTextEn($text_en)
    {
        return Dict::where('text_en', $text_en)->first()->value;
    }
}
