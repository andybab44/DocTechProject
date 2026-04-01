<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Admin User',      'email' => 'admin@example.com',      'role' => Role::Admin],
            ['name' => 'Doctor User',     'email' => 'doctor@example.com',     'role' => Role::Doctor],
            ['name' => 'Technician User', 'email' => 'technician@example.com', 'role' => Role::Technician],
        ];

        foreach ($users as $data) {
            User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'     => $data['name'],
                    'password' => Hash::make('password'),
                    'role'     => $data['role'],
                ]
            );
        }
    }
}
