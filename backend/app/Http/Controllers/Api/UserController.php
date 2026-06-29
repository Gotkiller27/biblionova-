<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\AssignRoleRequest;
use App\Http\Requests\User\AssignPermissionRequest;
use App\Models\User;
use App\Helpers\ApiResponse;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserCollection;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(protected UserService $userService)
    {
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $filters = [
            'search' => $request->search,
            'role' => $request->role,
            'status' => $request->status,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'with_trashed' => $request->boolean('with_trashed'),
            'only_trashed' => $request->boolean('only_trashed'),
        ];

        $users = $this->userService->getUsers(
            $filters,
            $request->per_page ?? 15,
            $request->sort_by ?? 'created_at',
            $request->sort_order ?? 'desc'
        );

        return ApiResponse::paginated($users, UserResource::class, 'Users retrieved successfully');
    }

    public function store(StoreUserRequest $request)
    {
        $this->authorize('create', User::class);
        $user = $this->userService->createUser($request->validated());

        activity()
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->log('User created');

        return ApiResponse::success(new UserResource($user->load('roles')), 'User created successfully', 201);
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);

        $user->load(['roles', 'permissions', 'depositRequests', 'uploadedReferences', 'reviews']);

        $statistics = [
            'deposit_requests_count' => $user->depositRequests->count(),
            'uploaded_references_count' => $user->uploadedReferences->count(),
            'reviews_count' => $user->reviews->count(),
        ];

        return ApiResponse::success([
            'user' => new UserResource($user),
            'statistics' => $statistics,
        ], 'User retrieved successfully');
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);
        $user = $this->userService->updateUser($user, $request->validated());

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

    public function restore(User $user)
    {
        $this->authorize('restore', $user);
        $user->restore();

        activity()
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->log('User restored');

        return ApiResponse::success(new UserResource($user->load('roles')), 'User restored successfully');
    }

    public function forceDelete(User $user)
    {
        $this->authorize('forceDelete', $user);
        $user->forceDelete();

        activity()
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->withProperties(['forced' => true])
            ->log('User permanently deleted');

        return ApiResponse::success(null, 'User permanently deleted');
    }

    // Role Management
    public function getRoles(User $user)
    {
        $this->authorize('view', $user);
        return ApiResponse::success($user->roles, 'User roles retrieved successfully');
    }

    public function assignRole(AssignRoleRequest $request, User $user)
    {
        $this->authorize('assignRole', $user);

        $this->userService->assignRole($user, $request->role);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->withProperties(['role' => $request->role])
            ->log('Role assigned to user');

        return ApiResponse::success(new UserResource($user->load('roles')), 'Role assigned successfully');
    }

    public function removeRole(Request $request, User $user)
    {
        $this->authorize('assignRole', $user);

        $request->validate(['role' => 'required|exists:roles,name']);

        $this->userService->removeRole($user, $request->role);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->withProperties(['role' => $request->role])
            ->log('Role removed from user');

        return ApiResponse::success(new UserResource($user->load('roles')), 'Role removed successfully');
    }

    public function syncRoles(Request $request, User $user)
    {
        $this->authorize('assignRole', $user);

        $request->validate(['roles' => 'required|array', 'roles.*' => 'exists:roles,name']);

        $this->userService->syncRoles($user, $request->roles);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->withProperties(['roles' => $request->roles])
            ->log('User roles synced');

        return ApiResponse::success(new UserResource($user->load('roles')), 'Roles synced successfully');
    }

    // Permission Management
    public function getPermissions(User $user)
    {
        $this->authorize('view', $user);
        return ApiResponse::success($user->permissions, 'User permissions retrieved successfully');
    }

    public function assignPermission(AssignPermissionRequest $request, User $user)
    {
        $this->authorize('assignPermission', $user);

        $this->userService->assignPermission($user, $request->permission);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->withProperties(['permission' => $request->permission])
            ->log('Permission assigned to user');

        return ApiResponse::success($user->permissions, 'Permission assigned successfully');
    }

    public function removePermission(Request $request, User $user)
    {
        $this->authorize('assignPermission', $user);

        $request->validate(['permission' => 'required|exists:permissions,name']);

        $this->userService->removePermission($user, $request->permission);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->withProperties(['permission' => $request->permission])
            ->log('Permission removed from user');

        return ApiResponse::success($user->permissions, 'Permission removed successfully');
    }

    public function syncPermissions(Request $request, User $user)
    {
        $this->authorize('assignPermission', $user);

        $request->validate(['permissions' => 'required|array', 'permissions.*' => 'exists:permissions,name']);

        $this->userService->syncPermissions($user, $request->permissions);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->withProperties(['permissions' => $request->permissions])
            ->log('User permissions synced');

        return ApiResponse::success($user->permissions, 'Permissions synced successfully');
    }

    // Status Management
    public function updateStatus(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $request->validate(['status' => 'required|in:active,inactive,suspended']);

        $this->userService->updateStatus($user, $request->status);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->withProperties(['status' => $request->status])
            ->log('User status updated');

        return ApiResponse::success(new UserResource($user), 'User status updated successfully');
    }

    // Export
    public function export(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $filters = [
            'search' => $request->search,
            'role' => $request->role,
            'status' => $request->status,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'with_trashed' => $request->with_trashed,
        ];

        $format = $request->format ?? 'xlsx';

        activity()
            ->causedBy(auth()->user())
            ->withProperties(['filters' => $filters, 'format' => $format])
            ->log('Users exported');

        return $this->userService->exportUsers($filters, $format);
    }
}
