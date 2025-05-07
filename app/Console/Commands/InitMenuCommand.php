<?php

namespace App\Console\Commands;

use App\Models\Menu;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InitMenuCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:init-menu';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '初始化菜单数据';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Menu::query()->truncate();
        DB::table('user_menus')->truncate();
        $menus = config('menus');
        $this->createMenus($menus);
        $user = User::query()->where('is_super', true)->first();
        if($user){
            $user->menus()->sync(Menu::query()->pluck('id'));
        }
    }

    public function createMenus(array $menus, $parentId = 0)
    {
        foreach ($menus as $menuData) {
            $children = $menuData['children'] ?? [];
            unset($menuData['children']);

            $menuData['pid'] = $parentId;
            $menu = Menu::query()->create($menuData);

            if (!empty($children)) {
               $this->createMenus($children, $menu->id);
            }
        }
    }
}
