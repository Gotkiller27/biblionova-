<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User management
            'create users',
            'edit users',
            'delete users',
            'view users',
            
            // Deposit requests
            'create deposit requests',
            'view own deposit requests',
            'view all deposit requests',
            'assign deposit requests',
            'approve deposit requests',
            'reject deposit requests',
            'request second review',
            'override decisions',
            
            // References
            'create references',
            'edit references',
            'delete references',
            'view references',
            'publish references',
            'archive references',
            
            // Categories & Metadata
            'manage categories',
            'manage authors',
            'manage publishers',
            'manage keywords',
            
            // Statistics & Reports
            'view statistics',
            'export data',
            
            // System
            'manage system settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // 1. Admin - Full access
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // 2. Responsable RH - User management
        $rhRole = Role::firstOrCreate(['name' => 'responsable_rh']);
        $rhRole->givePermissionTo([
            'create users',
            'edit users',
            'view users',
            'view statistics',
        ]);

        // 3. Responsable Validation - Deposit request validation
        $validationRole = Role::firstOrCreate(['name' => 'responsable_validation']);
        $validationRole->givePermissionTo([
            'view all deposit requests',
            'approve deposit requests',
            'reject deposit requests',
            'request second review',
            'view statistics',
        ]);

        // 4. Bibliothécaire - Reference management and publishing
        $bibliothecaireRole = Role::firstOrCreate(['name' => 'bibliothecaire']);
        $bibliothecaireRole->givePermissionTo([
            'create references',
            'edit references',
            'view references',
            'publish references',
            'archive references',
            'manage categories',
            'manage authors',
            'manage publishers',
            'manage keywords',
            'view all deposit requests',
            'view statistics',
        ]);

        // 5. Utilisateur - Basic user permissions
        $userRole = Role::firstOrCreate(['name' => 'utilisateur']);
        $userRole->givePermissionTo([
            'create deposit requests',
            'view own deposit requests',
            'view references',
        ]);
    }
}