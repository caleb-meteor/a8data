<?php

namespace App\Services;

use App\Models\User;
use Caleb\Practice\Service;

class AuthService extends Service
{
    public function login(string $username, string $password)
    {
        $user = User::query()->where('name', $username)->first();
        if(!$user){
            $this->throwAppException('user not found');
        }

        if(!password_verify($password, $user->password)){
            $this->throwAppException('password error');
        }

        // 保留该用户最新的20条记录
        $oldToken = $user->tokens()->orderByDesc('id')->skip(20)->first();

        if($oldToken){
            $user->tokens()->where('id','<', $oldToken->id)->delete();
        }

        return $user->createToken('admin')->plainTextToken;
    }
}
