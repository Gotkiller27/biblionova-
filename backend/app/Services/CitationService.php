<?php

namespace App\Services;

use App\Models\Citation;
use App\Models\Reference;

class CitationService
{
    public function createCitation(array $data): Citation
    {
        return Citation::create($data);
    }

    public function createCitationBetweenReferences(
        Reference $citing,
        Reference $cited,
        ?string $context = null,
        string $style = 'apa',
        ?int $pageNumber = null
    ): Citation {
        return Citation::firstOrCreate(
            [
                'citing_reference_id' => $citing->id,
                'cited_reference_id' => $cited->id,
            ],
            [
                'context' => $context,
                'citation_style' => $style,
                'page_number' => $pageNumber,
            ]
        );
    }

    public function updateCitation(Citation $citation, array $data): Citation
    {
        $citation->update($data);
        return $citation->load(['citingReference', 'citedReference']);
    }

    public function deleteCitation(Citation $citation): void
    {
        $citation->delete();
    }
}
