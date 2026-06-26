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
            'full_name' => $this->first_name . ' ' . $this->last_name,
            'biography' => $this->biography,
            'nationality' => $this->nationality,
            'birth_date' => $this->birth_date,
            'death_date' => $this->death_date,
            'references_count' => $this->whenCounted('references'),
        ];
    }
}
