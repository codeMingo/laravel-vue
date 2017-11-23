<?php
namespace App\Repositories\Backend;

use DB;
use Illuminate\Support\Facades\Redis;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

class CommonRepository extends BaseRepository
{

    /**
     * 保存图片
     * @param  FILE $input
     * @return Array
     */
    public function uploadImage($input)
    {
        $oldImagesName      = $input->getClientOriginalName();
        $fileTmpPath        = $input->getLinkTarget(); // 要上传文件的本地路径
        $imageExtensionName = $input->getClientOriginalExtension();
        $imageSize          = $input->getSize() / 1024; // 单位为KB

        if (!in_array(strtolower($imageExtensionName), ['jpeg', 'jpg', 'gpeg', 'png'])) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '请上传正确的图片',
            ];
        }
        if ($imageSize > config('blog.pictureSize')) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '上传的图片不得大于' . config('blog.pictureSize') . 'KB',
            ];
        }
        // redis记录该ip上传图片次数，一小时只允许上传10张

        // 七牛上传图片
        $auth = new Auth(config('blog.qiniuAccessKey'), config('blog.qiniuSecretKey'));

        $bucket = config('blog.qiniuImageBucket');
        // 生成上传Token
        $token = $auth->uploadToken($bucket);
        // 构建 UploadManager 对象
        $uploadMgr = new UploadManager();
        // 上传到七牛后保存的文件名
        $newImagesName   = md5(time()) . random_int(5, 5) . "." . $imageExtensionName;
        list($ret, $err) = $uploadMgr->putFile($token, $newImagesName, $fileTmpPath);
        if ($err !== null) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '上传失败',
            ];
        }
        $ret['imageUrl'] = config('blog.qiniuBucketUrl') . '/' . $ret['key'];
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => $ret,
            'message' => '上传成功',
        ];
    }

    public function updateRedis()
    {
        $backendLimitLoginUserArray = $backendLimitLoginIpArray = $frontendLimitLoginIpArray = [];

        // 记录操作日志
        Parent::saveOperateRecord([
            'action' => 'Common/updateRedis',
            'params' => [
                'ip_address' => getClientIp(),
            ],
            'text'   => 'redis缓存更新',
            'status' => 1,
        ]);

        // 清除所有的redis
        Redis::flushall();

        // 后台限制登录账号
        $backendLimitLoginLists = DB::select('select username from admin where status = 0');
        foreach ($backendLimitLoginLists as $key => $item) {
            $backendLimitLoginUserArray[] = $item->username;
        }
        Redis::sadd('limitBackendLoginUser', $backendLimitLoginUserArray);

        // 后台限制登录ip
        $limitIpList = DB::select('select ip_address from ip_limit_login where status = 1');
        foreach ($limitIpList as $key => $item) {
            if ($item->type == 1) {
                //前台
                $backendLimitLoginIpArray[] = $item->ip_address;
            } else {
                $frontendLimitLoginIpArray[] = $item->ip_address;
            }
        }
        Redis::sadd('limitBackendLoginIp', $backendLimitLoginIpArray);
        Redis::sadd('limitFrontendLoginIp', $frontendLimitLoginIpArray);

        // 文章缓存

        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => [],
            'message' => 'redis更新成功',
        ];

    }
}
