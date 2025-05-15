<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuthService;
use App\Services\MenuService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'name'     => 'required',
            'password' => 'required',
            'otp'      => 'sometimes',
        ]);

        return $this->success(AuthService::instance()->login($request->name, $request->password, $request->all()));
    }

    public function menus(Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        return $this->success(MenuService::instance()->getAllMenus($user));
    }

    public function user(Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        return $this->success(array_merge($user->toArray(), MenuService::instance()->getMenusAndPermissions($user)));
    }

    public function bind2fa(Request $request)
    {
        $request->validate([
            'otp' => 'required',
        ]);

        AuthService::instance()->verify2fa($request->user(), $request->otp);
        
        return $this->success();
    }
}
