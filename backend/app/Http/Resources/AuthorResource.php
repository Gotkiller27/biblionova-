<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'biography' => $this->biography,
            'photo' => $this->photo,
            'photo_url' => $this->photo_url,
            'nationality' => $this->nationality,
            'birth_date' => $this->birth_date ? $this->birth_date->format('Y-m-d') : null,
            'death_date' => $this->death_date ? $this->death_date->format('Y-m-d') : null,
            'age' => $this->age,
            'is_deceased' => $this->is_deceased,
            'references_count' => $this->whenCounted('references'),
            'co_authors_count' => $this->whenCounted('coAuthors'),
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
