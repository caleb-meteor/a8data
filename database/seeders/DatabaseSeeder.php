<?php

namespace Database\Seeders;

use App\Console\Commands\InitMenuCommand;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->create([
            'name'     => 'a8data',
            'username' => 'admin',
            'is_super' => true,
            'password' => env('ADMIN_PASSWORD'),
        ]);

        $this->command->call('app:init-menu');
    }
}
