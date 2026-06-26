<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepositRequestReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'deposit_request_id' => $this->deposit_request_id,
            'reviewer_id' => $this->reviewer_id,
            'reviewer_role' => $this->reviewer_role,
            'decision' => $this->decision,
            'justification' => $this->justification,
            'created_at' => $this->created_at,
            'reviewer' => new UserResource($this->whenLoaded('reviewer')),
        ];
    }
}
