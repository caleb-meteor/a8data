<?php
return [
    [
        'title' => '首页',
        'type' => 'route',
        'permission' => 'dashboard'
    ],
    [
        'title' => '用户管理',
        'type' => 'route',
        'permission' => 'users',
        'children' => [
            [
                'title' => '用户列表',
                'type' => 'button',
                'permission' => 'users.index'
            ],
            [
                'title' => '用户添加',
                'type' => 'button',
                'permission' => 'users.create'
            ],
            [
                'title' => '用户编辑',
                'type' => 'button',
                'permission' => 'users.update'
            ],
            [
                'title' => '用户删除',
                'type' => 'button',
                'permission' => 'users.destroy'
            ]
        ]
    ]
];
