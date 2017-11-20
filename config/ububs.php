<?php

return [
    // 过滤参数规则
    'param_rules' => [
        'admins' => [
            'id' => '=',
            'username' => 'like',
            'email' => 'like',
            'permission_id' => '=',
            'status' => '='
        ],
        'admin_permissions' => [
            'id' => '=',
            'text' => 'like',
            'permission_include' => 'in',
            'status' => '='
        ]
    ]
];
