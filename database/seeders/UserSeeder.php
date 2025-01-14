<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds to populate the 'users' table.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@example.com',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
            ],
            [
                'name' => 'Robert Johnson',
                'email' => 'robert@example.com',
            ],
            [
                'name' => 'Maria Garcia',
                'email' => 'maria@example.com',
            ],
            [
                'name' => 'David Wilson',
                'email' => 'david@example.com',
            ],
        ];

        foreach ($users as $user) {
            User::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => Hash::make('password123'),
                'api_token' => Str::random(60)
            ]);
        }
    }
}
