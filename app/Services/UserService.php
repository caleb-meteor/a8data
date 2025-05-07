<?php

namespace App\Services;

use App\Models\User;
use Caleb\Practice\QueryFilter;
use Caleb\Practice\Service;

class UserService extends Service
{
    /**
     * @param QueryFilter $filter
     * @return \Illuminate\Pagination\LengthAwarePaginator
     * @author Caleb 2025/5/7
     */
    public function getUserList(QueryFilter $filter)
    {
        return User::filter($filter)->with('menus')
            ->orderByDesc('is_super')->orderBy('id')
            ->paginate();
    }

    /**
     * @param array $data
     * @return User|\Illuminate\Database\Eloquent\Model
     * @author Caleb 2025/5/7
     */
    public function createUser(array $data)
    {
        $user = User::query()->create($data);

        if (isset($data['menus'])) {
            $user->menus()->sync($data['menus']);
        }

        return $user;
    }

    public function getUser(int|User $user)
    {
        return $user instanceof User ? $user : User::query()->findOrFail($user);
    }

    public function updateUser(int|User $user, array $data)
    {
        $user = $this->getUser($user);
        $user->update($data);
        // 非超级用户时，更新菜单
        if (!$user->is_super && isset($data['menus'])) {
            $user->menus()->sync($data['menus']);
        }
        return $user;
    }

    public function deleteUser(int|User $user)
    {
        $user = $this->getUser($user);
        if ($user->is_super) {
            $this->throwAppException('超级管理员不能删除');
        }
        return $user->delete();
    }
}
