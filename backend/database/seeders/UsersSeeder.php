<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        // Users for all roles with BiblioNova theme
        $users = [
            // Admin
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'email' => 'admin@biblionova.com',
                'password' => 'admin123',
                'role' => 'admin',
                'status' => 'active'
            ],
            
            // Responsable RH
            [
                'first_name' => 'Marie',
                'last_name' => 'Ressources',
                'email' => 'rh@biblionova.com',
                'password' => 'rh123',
                'role' => 'responsable_rh',
                'status' => 'active'
            ],
            
            // Responsable Validation
            [
                'first_name' => 'Jean',
                'last_name' => 'Validation',
                'email' => 'validation@biblionova.com',
                'password' => 'validation123',
                'role' => 'responsable_validation',
                'status' => 'active'
            ],
            
            // Bibliothécaire
            [
                'first_name' => 'Sophie',
                'last_name' => 'Bibliothèque',
                'email' => 'bibliothecaire@biblionova.com',
                'password' => 'biblio123',
                'role' => 'bibliothecaire',
                'status' => 'active'
            ],
            
            // Utilisateur standard
            [
                'first_name' => 'Pierre',
                'last_name' => 'Utilisateur',
                'email' => 'user@biblionova.com',
                'password' => 'user123',
                'role' => 'utilisateur',
                'status' => 'active'
            ],
            
            // Utilisateurs supplémentaires pour tests
            [
                'first_name' => 'Alice',
                'last_name' => 'Martin',
                'email' => 'alice.martin@biblionova.com',
                'password' => 'alice123',
                'role' => 'utilisateur',
                'status' => 'active'
            ],
            
            [
                'first_name' => 'Bob',
                'last_name' => 'Dupont',
                'email' => 'bob.dupont@biblionova.com',
                'password' => 'bob123',
                'role' => 'utilisateur',
                'status' => 'active'
            ],
            
            [
                'first_name' => 'Claire',
                'last_name' => 'Durand',
                'email' => 'claire.durand@biblionova.com',
                'password' => 'claire123',
                'role' => 'bibliothecaire',
                'status' => 'active'
            ],
        ];

        foreach ($users as $userData) {
            // Create user
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'first_name' => $userData['first_name'],
                    'last_name' => $userData['last_name'],
                    'password' => Hash::make($userData['password']),
                    'status' => $userData['status'],
                ]
            );

            // Assign role
            $user->assignRole($userData['role']);

            echo "✅ Utilisateur créé: {$userData['email']} - Rôle: {$userData['role']}\n";
        }
    }
}