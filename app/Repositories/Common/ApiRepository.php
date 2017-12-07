<?php
namespace App\Repositories\Common;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Qiniu\Auth;

class ApiRepository extends BaseRepository
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取当前用户id
     * @return Int
     */
    public function getCurrentId()
    {}

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
     * @return [type] [description]
     */
    public function refreshCache()
    {
        Redis::flushdb();

        // 记录是否生成缓存
        Redis::set('has_cache', 1);

        // dicts字典表缓存
        $dict_lists = DB::table('dicts')->where('status', 1)->get();
        if (!empty($dict_lists)) {
            $dict_redis_key = 'dicts_';
            foreach ($dict_lists as $key => $dict) {
                Redis::hset('dicts_' . $dict->code, $dict->text_en, $dict->value);
            }
        }

        // 文章列表缓存
        // 留言列表缓存
        // 推荐文章缓存
        // 热门文章缓存
        // 视频列表缓存
        return responseResult(true);
    }

}
