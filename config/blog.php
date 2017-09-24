<?php

return [
    /**
     * 自定义
     */
    'pageSize'          => env('APP_PAGE_SIZE', 20),
    'pictureSize'       => env('APP_PICTURE_SIZE', 500),
    'registerEmailTime' => env('APP_EMAIL_REGISTER_TIME', 86400),
    'qiniuAccessKey'    => env('QINIU_ACCESS_KEY', ''),
    'qiniuSecretKey'    => env('QINIU_SECRET_KEY', ''),
    'qiniuImageBucket'  => env('QINIU_IMAGE_BUCKET', ''),
    'qiniuBucketUrl'    => env('QINIU_BUCKET_URL', ''),
];
