<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@example.com',
            'phone' => '0123456789',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        User::create([
            'first_name' => 'RH',
            'last_name' => 'Manager',
            'email' => 'rh@example.com',
            'phone' => '0123456788',
            'password' => Hash::make('password'),
            'role' => 'responsable_rh',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        User::create([
            'first_name' => 'Validation',
            'last_name' => 'Manager',
            'email' => 'validation@example.com',
            'phone' => '0123456787',
            'password' => Hash::make('password'),
            'role' => 'responsable_demande',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        User::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'user@example.com',
            'phone' => '0123456786',
            'password' => Hash::make('password'),
            'role' => 'user',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
    }
}
