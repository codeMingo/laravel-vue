<?php

return [
    // 分页
    'pageSize'          => env('APP_PAGE_SIZE', 20),

    // 图片大小限制
    'pictureSize'       => env('APP_PICTURE_SIZE', 500),

    // 注册激活邮件的有效时间
    'registerEmailTime' => env('APP_EMAIL_REGISTER_TIME', 86400),

    // 七牛密钥
    'qiniuAccessKey'    => env('QINIU_ACCESS_KEY', ''),
    'qiniuSecretKey'    => env('QINIU_SECRET_KEY', ''),

    // 七牛bucket
    'qiniuImageBucket'  => env('QINIU_IMAGE_BUCKET', ''),
    'qiniuBucketUrl'    => env('QINIU_BUCKET_URL', ''),
];
