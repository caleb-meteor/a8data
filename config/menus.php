<?php
return [
    [
        'title'      => '首页',
        'type'       => 'route',
        'permission' => 'dashboard'
    ],
    [
        'title'      => '用户管理',
        'type'       => 'route',
        'permission' => 'users',
        'children'   => [
            ['title' => '用户列表', 'type' => 'button', 'permission' => 'users.index'],
            ['title' => '用户添加', 'type' => 'button', 'permission' => 'users.create'],
            ['title' => '用户编辑', 'type' => 'button', 'permission' => 'users.update'],
            ['title' => '用户删除', 'type' => 'button', 'permission' => 'users.destroy']
        ]
    ],
    [
        'title'      => '团队管理',
        'type'       => 'route',
        'permission' => 'teams',
        'children'   => [
            ['title' => '团队列表', 'type' => 'button', 'permission' => 'teams.index'],
            ['title' => '团队添加', 'type' => 'button', 'permission' => 'teams.create'],
            ['title' => '团队编辑', 'type' => 'button', 'permission' => 'teams.update'],
            ['title' => '团队删除', 'type' => 'button', 'permission' => 'teams.destroy']
        ]
    ],
    [
        'title'      => '产品管理',
        'type'       => 'route',
        'permission' => 'products',
        'children'   => [
            ['title' => '产品列表', 'type' => 'button', 'permission' => 'products.index'],
            ['title' => '产品添加', 'type' => 'button', 'permission' => 'products.create'],
            ['title' => '产品编辑', 'type' => 'button', 'permission' => 'products.update'],
            ['title' => '产品删除', 'type' => 'button', 'permission' => 'products.destroy']
        ]
    ],

];
