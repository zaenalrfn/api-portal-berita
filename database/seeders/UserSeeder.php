<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin
        $admin = User::create([
            'name' => 'Admin Portal',
            'email' => 'admin@portal.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('admin');

        // User biasa
        $user = User::create([
            'name' => 'User Biasa',
            'email' => 'user@portal.com',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('user');
    }
}
