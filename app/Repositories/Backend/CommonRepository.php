<?php
namespace App\Repositories\Backend;

use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CommonRepository extends BaseRepository
{
    /**
     * 清空redis缓存，并且重新生成缓存
     * @return [type] [description]
     */
    public function refreshAllRedisCache()
    {
        Redis::flushdb();

        // dicts字典表缓存
        $dict_lists = DB::table('dicts')->where('status', 1)->get();
        if (!empty($dict_lists)) {
            $dict_redis_key = 'dicts_';
            foreach ($dict_lists as $key => $dict) {
                Redis::hset('dicts_' . $dict->code, $dict->text_en, $dict->value);
            }
        }
        return true;
    }
}
