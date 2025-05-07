<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\User;
use Caleb\Practice\Service;

class MenuService extends Service
{
    public function getAllMenus(User $user)
    {
        $menus = $user->menus;
        $menus->transform(function ($menu) {
            unset($menu['pivot']);
            return $menu;
        });
        return format_tree($menus->toArray());
    }

    public function getMenusAndPermissions(User $user)
    {
        $menus = $user->menus;

        $menus->transform(function ($menu) {
            unset($menu['pivot']);
            return $menu;
        });

        $pageMenus   = $menus->filter(fn(Menu $menu) => $menu->type == 'route');
        $permissions = $menus->filter(fn(Menu $menu) => $menu->type == 'button');

        return [
            'menus'       => format_tree($pageMenus->toArray()),
            'permissions' => $permissions->pluck('permission')->toArray()
        ];
    }

    public function hasPermission($permission, User $user)
    {
        $menu = Menu::query()->where('permission', $permission)->first();
        if(!$menu){
            return true;
        }
        return $user->menus()->where('menus.id', $menu->id)->exists();
    }
}
