<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer les permissions
        $permissions = [
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',
            'view authors',
            'create authors',
            'edit authors',
            'delete authors',
            'view publishers',
            'create publishers',
            'edit publishers',
            'delete publishers',
            'view keywords',
            'create keywords',
            'edit keywords',
            'delete keywords',
            'view references',
            'create references',
            'edit references',
            'delete references',
            'view deposit requests',
            'create deposit requests',
            'edit deposit requests',
            'delete deposit requests',
            'review deposit requests',
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // Créer les rôles et assigner les permissions
        $roles = [
            'admin' => $permissions, // Toutes les permissions
            'responsable_rh' => ['view users', 'create users', 'edit users'],
            'responsable_validation' => ['view deposit requests', 'review deposit requests'],
            'bibliothecaire' => [
                'view categories', 'create categories', 'edit categories',
                'view authors', 'create authors', 'edit authors',
                'view publishers', 'create publishers', 'edit publishers',
                'view keywords', 'create keywords', 'edit keywords',
                'view references', 'create references', 'edit references',
                'view deposit requests',
            ],
            'utilisateur' => [
                'view references',
                'view categories',
                'view authors',
                'view publishers',
                'view keywords',
                'create deposit requests',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = \Spatie\Permission\Models\Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);
            $role->syncPermissions($rolePermissions);
        }
    }
}
