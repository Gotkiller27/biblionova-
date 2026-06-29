<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CitationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'citing_reference_id' => $this->citing_reference_id,
            'cited_reference_id' => $this->cited_reference_id,
            'context' => $this->context,
            'citation_style' => $this->citation_style,
            'page_number' => $this->page_number,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'citing_reference' => new ReferenceResource($this->whenLoaded('citingReference')),
            'cited_reference' => new ReferenceResource($this->whenLoaded('citedReference')),
        ];
    }
}
