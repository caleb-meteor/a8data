<?php

use Illuminate\Support\Str;

if (!function_exists('format_tree')) {
    function format_tree($data, int $parentId = 0): array
    {
        $tree = [];


        $items = array_column($data, null, 'id');

        // 遍历引用数组，建立父子关系
        foreach ($items as &$item) {
            if ($item['pid'] == $parentId) {
                $tree[] = &$item; // 顶级节点放入 tree 中
            } else {
                // 将当前节点加入到其父节点的 children 中
                $items[$item['pid']]['children'][] = &$item;
            }
        }

        return $tree;
    }
}
