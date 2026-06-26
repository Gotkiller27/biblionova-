<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->avatar_url,
            'status' => $this->status,
            'email_verified_at' => $this->email_verified_at,
            'last_login_at' => $this->last_login_at,
            'created_at' => $this->created_at,
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            'permissions' => PermissionResource::collection($this->whenLoaded('permissions')),
            'statistics' => [
                'deposit_requests_count' => $this->whenLoaded('depositRequests', $this->depositRequests_count ?? $this->depositRequests->count()),
                'uploaded_references_count' => $this->whenLoaded('uploadedReferences', $this->uploaded_references_count ?? $this->uploadedReferences->count()),
                'reviews_count' => $this->whenLoaded('reviews', $this->reviews_count ?? $this->reviews->count()),
            ],
        ];
    }
}
