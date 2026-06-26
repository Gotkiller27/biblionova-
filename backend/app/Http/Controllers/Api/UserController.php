<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Helpers\ApiResponse;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', User::class);
        $users = User::with('roles')->latest()->paginate(15);
        return ApiResponse::paginated($users, UserResource::class, 'Users retrieved successfully');
    }

    public function store(StoreUserRequest $request)
    {
        $this->authorize('create', User::class);
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        if (isset($data['role'])) {
            $user->assignRole($data['role']);
        }

        activity()
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->log('User created');

        return ApiResponse::success(new UserResource($user->load('roles')), 'User created successfully', 201);
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);
        return ApiResponse::success(new UserResource($user->load('roles')), 'User retrieved successfully');
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);
        $data = $request->validated();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        if (isset($data['role'])) {
            $user->syncRoles([$data['role']]);
        }

        activity()
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->log('User updated');

        return ApiResponse::success(new UserResource($user->load('roles')), 'User updated successfully');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        $user->delete();

        activity()
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->log('User deleted');

        return ApiResponse::success(null, 'User deleted successfully');
    }
}
