<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;

class UserService
{
    public function getUsers(array $filters = [], int $perPage = 15, string $sortBy = 'created_at', string $sortOrder = 'desc'): LengthAwarePaginator
    {
        $query = User::with('roles', 'permissions');

        // Search
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filters
        if (isset($filters['role'])) {
            $query->whereHas('roles', function ($q) use ($filters) {
                $q->where('name', $filters['role']);
            });
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['from_date'])) {
            $query->whereDate('created_at', '>=', $filters['from_date']);
        }

        if (isset($filters['to_date'])) {
            $query->whereDate('created_at', '<=', $filters['to_date']);
        }

        // Include trashed
        if (isset($filters['with_trashed']) && $filters['with_trashed']) {
            $query->withTrashed();
        }

        // Only trashed
        if (isset($filters['only_trashed']) && $filters['only_trashed']) {
            $query->onlyTrashed();
        }

        // Sorting
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    public function createUser(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        if (isset($data['role'])) {
            $user->assignRole($data['role']);
        }

        return $user;
    }

    public function updateUser(User $user, array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        if (isset($data['role'])) {
            $user->syncRoles([$data['role']]);
        }

        return $user;
    }

    public function assignRole(User $user, string $role): void
    {
        $user->assignRole($role);
    }

    public function removeRole(User $user, string $role): void
    {
        $user->removeRole($role);
    }

    public function syncRoles(User $user, array $roles): void
    {
        $user->syncRoles($roles);
    }

    public function assignPermission(User $user, string $permission): void
    {
        $user->givePermissionTo($permission);
    }

    public function removePermission(User $user, string $permission): void
    {
        $user->revokePermissionTo($permission);
    }

    public function syncPermissions(User $user, array $permissions): void
    {
        $user->syncPermissions($permissions);
    }

    public function updateStatus(User $user, string $status): void
    {
        $user->update(['status' => $status]);
    }

    public function exportUsers(array $filters = [], string $format = 'xlsx')
    {
        $export = new UsersExport($filters);

        if ($format === 'csv') {
            return Excel::download($export, 'users_' . date('Y-m-d') . '.csv');
        }

        return Excel::download($export, 'users_' . date('Y-m-d') . '.xlsx');
    }
}
