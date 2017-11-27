<?php

return [
    // 网站名称
    'website_name'             => 'ububs编程',
    // 网站地址
    'website_url'              => 'http://www.ububs.com',
    // 网站密匙
    'website_encrypt'          => '$1$D.1.QW1.$cA1J0g5JjRf0Li0WHBhnQ1',
    // 七牛参数
    'qiniuAccessKey'           => env('QINIU_ACCESS_KEY', ''),
    'qiniuSecretKey'           => env('QINIU_SECRET_KEY', ''),
    'qiniuImageBucket'         => env('QINIU_IMAGE_BUCKET', ''),
    'qiniuBucketUrl'           => env('QINIU_BUCKET_URL', ''),
    // 重复请求限制次数
    'repeat_more_operate'      => 10,
    'repeat_more_operate_time' => 3600,
    // 重复请求限制时间
    // 过滤参数规则， 默认就是 = ，不用写
    'param_rules'              => [
        // 管理员
        'admins'            => [
            'username' => 'like',
            'email'    => 'like',
        ],
        // 管理员权限
        'admin_permissions' => [
            'text'               => 'like',
            'permission_include' => 'in',
        ],
        // 文章
        'articles'          => [
            'title'   => 'like',
            'auther'  => 'like',
            'tag_ids' => 'in',
        ],
        // 互动
        'interactes' => [
        ],
        // 视频
        'videos' => [
            'title'   => 'like',
            'auther'  => 'like',
            'tag_ids' => 'in',
        ]
    ],
];
