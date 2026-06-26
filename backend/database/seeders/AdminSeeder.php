<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. On capture l'admin dans la variable $admin (et on retire 'role')
        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@example.com',
            'phone' => '0123456789',
            'password' => Hash::make('password'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // On lui donne tous les rôles d'un coup comme tu le souhaitais !
        $admin->assignRole(['admin', 'responsable_demande', 'responsable_rh', 'utilisateur']);

        // 2. Création du RH (on retire 'role' pour éviter le plantage SQL)
        $rh = User::create([
            'first_name' => 'RH',
            'last_name' => 'Manager',
            'email' => 'rh@example.com',
            'phone' => '0123456788',
            'password' => Hash::make('password'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        $rh->assignRole('responsable_rh');

        // 3. Création du Validation Manager (on retire 'role')
        $validation = User::create([
            'first_name' => 'Validation',
            'last_name' => 'Manager',
            'email' => 'validation@example.com',
            'phone' => '0123456787',
            'password' => Hash::make('password'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        $validation->assignRole('responsable_demande');

        // 4. Création de l'utilisateur de test (on retire 'role')
        $user = User::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'user@example.com',
            'phone' => '0123456786',
            'password' => Hash::make('password'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        $user->assignRole('utilisateur');
    }
}