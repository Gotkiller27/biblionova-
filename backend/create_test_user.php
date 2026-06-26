<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Créer l'utilisateur admin de test
$user = \App\Models\User::firstOrCreate(
    ['email' => 'admin@nexadoc.com'],
    [
        'first_name' => 'Admin',
        'last_name' => 'Test', 
        'password' => bcrypt('password123'),
        'status' => 'active'
    ]
);

// Assigner le rôle admin
$user->assignRole('admin');

echo "✅ Utilisateur admin créé:\n";
echo "Email: admin@nexadoc.com\n";
echo "Mot de passe: password123\n";
echo "Rôle: admin\n\n";

// Créer un utilisateur normal de test
$user2 = \App\Models\User::firstOrCreate(
    ['email' => 'user@nexadoc.com'],
    [
        'first_name' => 'Utilisateur',
        'last_name' => 'Test',
        'password' => bcrypt('password123'), 
        'status' => 'active'
    ]
);

$user2->assignRole('utilisateur');

echo "✅ Utilisateur standard créé:\n";
echo "Email: user@nexadoc.com\n";
echo "Mot de passe: password123\n";
echo "Rôle: utilisateur\n";