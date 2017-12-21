<?php
namespace App\Repositories\Common;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Qiniu\Auth;

class ApiRepository extends BaseRepository
{

    public function __construct()
    {

    }

    /**
     * 记录操作日志
     * @param  Array  $input [action, params, text, status]
     * @return Array
     */
    public function saveOperateRecord($input)
    {}

    /**
     * 生成七牛上传的token
     * @return Array
     */
    public function createToken()
    {
        $auth   = new Auth(config('ububs.qiniu_access_key'), config('ububs.qiniu_secret_key'));
        $bucket = config('ububs.qiniu_face_bucket');
        $token  = $auth->uploadToken($bucket);
        return [
            'uptoken' => $token,
        ];
    }

    /**
     * 清空redis缓存，并且重新生成缓存
     * @return boolean
     */
    public function refreshCache()
    {
        Redis::flushdb();

        // dicts字典表缓存
        $lists = DB::table('dicts')->select(['code', 'text_en', 'value'])->where('status', 1)->get();
        if (!empty($lists)) {
            $temp_lists = [];
            foreach ($lists as $key => $value) {
                $temp_lists[$value->code][$value->text_en] = $value->value;
            }
            foreach ($temp_lists as $key => $value) {
                Redis::hset('dicts', $key, json_encode($value));
            }
        }
        // 文章列表缓存
        // 留言列表缓存
        // 推荐文章缓存
        // 热门文章缓存
        // 视频列表缓存
        return true;
    }

}
