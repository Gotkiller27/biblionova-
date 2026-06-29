<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReferenceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'abstract' => $this->abstract,
            'isbn' => $this->isbn,
            'doi' => $this->doi,
            'issn' => $this->issn,
            'publication_year' => $this->publication_year,
            'language' => $this->language,
            'document_type' => $this->document_type,
            'category_id' => $this->category_id,
            'publisher_id' => $this->publisher_id,
            'uploaded_by' => $this->uploaded_by,
            'bibliothecaire_id' => $this->bibliothecaire_id,
            'cover_image' => $this->cover_image ? asset('storage/' . $this->cover_image) : null,
            'file_path' => $this->file_path ? asset('storage/' . $this->file_path) : null,
            'pages' => $this->pages,
            'download_count' => $this->download_count,
            'view_count' => $this->view_count,
            'status' => $this->status,
            'visibility' => $this->visibility,
            'availability' => $this->availability,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'publisher' => new PublisherResource($this->whenLoaded('publisher')),
            'uploader' => new UserResource($this->whenLoaded('uploader')),
            'bibliothecaire' => new UserResource($this->whenLoaded('bibliothecaire')),
            'authors' => AuthorResource::collection($this->whenLoaded('authors')),
            'keywords' => KeywordResource::collection($this->whenLoaded('keywords')),
            'revisions' => DocumentRevisionResource::collection($this->whenLoaded('revisions')),
            'citation_count' => $this->whenCounted('citedByCitations'),
            'favorite_count' => $this->whenCounted('favorites'),
        ];
    }
}
