<?php

namespace App\Http\Controllers\Admin;

use App\Filters\UserFilter;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(UserFilter $filter)
    {
        return $this->success(
            UserService::instance()->getUserList($filter)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255|unique:users',
            'username' => 'string|max:255',
            'password' => 'required|string|min:6',
            'menus'    => 'required|array'
        ]);

        return $this->success(
            UserService::instance()->createUser($data)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255', Rule::unique('users')->ignore($id),],
            'username' => 'string|max:255',
            'password' => 'string|min:6',
            'menus'    => 'array'
        ]);

        return $this->success(
            UserService::instance()->updateUser($id, $data)
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        UserService::instance()->deleteUser($id);
        return $this->success();
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @author Caleb 2025/5/15
     */
    public function unbind2fa(Request $request, int $id)
    {
        $user = $request->user();
        if (!$user->is_super) {
            return  $this->error('权限不足');
        }

        UserService::instance()->unbind2fa($id);

        return $this->success();
    }
}
