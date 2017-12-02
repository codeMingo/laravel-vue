<?php

return [
    // 网站名称
    'website_name'          => env('WEBSITE_NAME', 'ububs编程'),
    // 网站地址
    'website_url'           => env('WEBSITE_URL', 'http://www.ububs.com'),
    // 网站密匙
    'website_encrypt'       => env('WEBSITE_ENCRYPT', '$1$D.1.QW1.$cA1J0g5JjRf0Li0WHBhnQ1'),
    // 七牛参数
    'qiniu_access_key'      => env('QINIU_ACCESS_KEY', ''),
    'qiniu_secret_key'      => env('QINIU_SECRET_KEY', ''),
    'qiniu_face_bucket'     => env('QINIU_FACE_BUCKET', ''),
    'qiniu_face_bucket_url' => env('QINIU_FACE_BUCKET_URL', ''),
    // 重复请求限制次数
    'repeat_max_limit'      => env('REPEAT_MAX_LIMIT', 100),
    // 重复请求限制时间
    'repeat_max_time_limit' => env('REPEAT_MAX_TIME_LIMIT', 3600),

    // 过滤参数规则， 默认就是 = ，不用写
    'param_rules'           => [
        // 管理员
        'admins'            => [
            'username' => 'like',
            'email'    => 'like',
        ],
        // 用户
        'users'            => [
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
        'interactes'        => [
        ],
        // 视频
        'videos'            => [
            'title'   => 'like',
            'auther'  => 'like',
            'tag_ids' => 'in',
        ],
        // 菜单
        'categories'        => [
            'title' => 'like',
        ],
    ],
];
