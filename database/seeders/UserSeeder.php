<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin
        User::updateOrCreate(
            ['email' => 'admin@exputra.cloud'],
            [
                'name' => 'Administrator',
                'email_verified_at' => now(),
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'remember_token' => Str::random(10),
            ]
        );

        // Client
        User::updateOrCreate(
            ['email' => 'client@exputra.cloud'],
            [
                'name' => 'Client Demo',
                'email_verified_at' => now(),
                'password' => Hash::make('client123'),
                'role' => 'client',
                'remember_token' => Str::random(10),
            ]
        );
    }
}
