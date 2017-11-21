<?php

return [
    // 过滤参数规则， 默认就是 = ，不用写
    'param_rules' => [
        // 管理员
        'admins' => [
            'username' => 'like',
            'email'    => 'like',
        ],
        // 管理员权限
        'admin_permissions' => [
            'text'               => 'like',
            'permission_include' => 'in',
        ],
        // 文章
        'articles' => [
            'title'   => 'like',
            'auther'  => 'like',
            'tag_ids' => 'in',
        ]
    ]
];
