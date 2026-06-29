<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Models\User;
use App\Helpers\ApiResponse;
use App\Http\Resources\ProfileResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user()->load(['roles', 'permissions', 'depositRequests', 'uploadedReferences', 'reviews']);

        $statistics = [
            'deposit_requests_count' => $user->depositRequests->count(),
            'uploaded_references_count' => $user->uploadedReferences->count(),
            'reviews_count' => $user->reviews->count(),
        ];

        return ApiResponse::success([
            'user' => new ProfileResource($user),
            'statistics' => $statistics,
        ], 'Profile retrieved successfully');
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = auth()->user();
        $data = $request->validated();

        $user->update($data);

        activity()
            ->causedBy($user)
            ->performedOn($user)
            ->log('Profile updated');

        return ApiResponse::success(new ProfileResource($user->load('roles')), 'Profile updated successfully');
    }

    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $user = auth()->user();

        // Delete old avatar if exists
        if ($user->avatar) {
            Storage::disk('public')->delete('avatars/' . $user->avatar);
        }

        // Store new avatar
        $file = $request->file('avatar');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('avatars', $fileName, 'public');

        $user->update(['avatar' => $fileName]);

        activity()
            ->causedBy($user)
            ->performedOn($user)
            ->log('Avatar updated');

        return ApiResponse::success(['avatar' => $user->avatar_url], 'Avatar uploaded successfully');
    }

    public function deleteAvatar()
    {
        $user = auth()->user();

        if ($user->avatar) {
            Storage::disk('public')->delete('avatars/' . $user->avatar);
            $user->update(['avatar' => null]);

            activity()
                ->causedBy($user)
                ->performedOn($user)
                ->log('Avatar deleted');
        }

        return ApiResponse::success(['avatar' => $user->avatar_url], 'Avatar deleted successfully');
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return ApiResponse::error('Current password is incorrect', [], 422);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        activity()
            ->causedBy($user)
            ->performedOn($user)
            ->log('Password changed');

        return ApiResponse::success(null, 'Password changed successfully');
    }
}
