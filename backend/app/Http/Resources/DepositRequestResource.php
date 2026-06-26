<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepositRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'category_id' => $this->category_id,
            'applicant_id' => $this->applicant_id,
            'assigned_manager_id' => $this->assigned_manager_id,
            'authors' => $this->authors,
            'publisher' => $this->publisher,
            'publication_year' => $this->publication_year,
            'language' => $this->language,
            'file_path' => $this->file_path ? asset('storage/' . $this->file_path) : null,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'applicant' => new UserResource($this->whenLoaded('applicant')),
            'assigned_manager' => new UserResource($this->whenLoaded('assignedManager')),
            'reviews' => DepositRequestReviewResource::collection($this->whenLoaded('reviews')),
        ];
    }
}
