<?php
namespace App\Repositories\Common;

use Qiniu\Auth;

class ApiRepository extends BaseRepository
{

    /**
     * 生成七牛上传的token
     * @return Array
     */
    public function createToken()
    {
        $auth   = new Auth(config('ububs.qiniuAccessKey'), config('ububs.qiniuSecretKey'));
        $bucket = 'linlm1994';
        $token = $auth->uploadToken($bucket);
        return [
            'uptoken' => $token,
        ];
    }


}
