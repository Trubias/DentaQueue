<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'Admin@gmail.com'],
            [
                'name' => 'Administrator',
                'email' => 'Admin@gmail.com',
                'age' => 21,
                'sex' => 'Male',
                'password' => Hash::make('123'),
                'role' => 'admin',
            ]
        );
    }
}