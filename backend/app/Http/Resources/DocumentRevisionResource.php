<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentRevisionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reference_id' => $this->reference_id,
            'bibliothecaire_id' => $this->bibliothecaire_id,
            'action' => $this->action,
            'previous_data' => $this->previous_data,
            'new_data' => $this->new_data,
            'created_at' => $this->created_at,
            'bibliothecaire' => new UserResource($this->whenLoaded('bibliothecaire')),
        ];
    }
}
