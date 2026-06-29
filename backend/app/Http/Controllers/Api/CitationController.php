<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Citation\StoreCitationRequest;
use App\Http\Requests\Citation\UpdateCitationRequest;
use App\Http\Requests\Citation\CiteReferenceRequest;
use App\Http\Resources\CitationResource;
use App\Http\Resources\ReferenceResource;
use App\Models\Citation;
use App\Models\Reference;
use App\Services\CitationService;
use Illuminate\Http\Request;

class CitationController extends BaseApiController
{
    public function __construct(protected CitationService $citationService)
    {
        $this->authorizeResource(Citation::class, 'citation');
    }

    /**
     * GET /citations - Liste des citations
     */
    public function index(Request $request)
    {
        $citations = Citation::with(['citingReference', 'citedReference'])
            ->when($request->reference_id, fn($q) => $q->forReference($request->reference_id))
            ->when($request->citation_style, fn($q) => $q->byStyle($request->citation_style))
            ->latest()
            ->paginate(15);

        return $this->paginated($citations, CitationResource::class, 'Citations récupérées avec succès');
    }

    /**
     * POST /citations - Créer une citation
     */
    public function store(StoreCitationRequest $request)
    {
        $citation = $this->citationService->createCitation($request->validated());

        return $this->created(
            new CitationResource($citation->load(['citingReference', 'citedReference'])),
            'Citation créée avec succès'
        );
    }

    /**
     * GET /citations/{citation} - Afficher une citation
     */
    public function show(Citation $citation)
    {
        return $this->success(
            new CitationResource($citation->load(['citingReference', 'citedReference'])),
            'Citation récupérée avec succès'
        );
    }

    /**
     * PUT /citations/{citation} - Mettre à jour une citation
     */
    public function update(UpdateCitationRequest $request, Citation $citation)
    {
        $citation = $this->citationService->updateCitation($citation, $request->validated());

        return $this->success(
            new CitationResource($citation),
            'Citation mise à jour avec succès'
        );
    }

    /**
     * DELETE /citations/{citation} - Supprimer une citation
     */
    public function destroy(Citation $citation)
    {
        $this->citationService->deleteCitation($citation);

        return $this->success(null, 'Citation supprimée avec succès');
    }

    /**
     * GET /references/{reference}/citations - Citations d'un document
     */
    public function forReference(Request $request, Reference $reference)
    {
        $citations = Citation::with(['citingReference', 'citedReference'])
            ->forReference($reference->id)
            ->latest()
            ->paginate(15);

        return $this->paginated($citations, CitationResource::class, 'Citations du document récupérées avec succès');
    }

    /**
     * GET /references/{reference}/citing - Documents cités par ce document
     */
    public function citing(Request $request, Reference $reference)
    {
        $references = Reference::with(['category', 'publisher', 'authors'])
            ->whereHas('citedByCitations', fn($q) => $q->where('citing_reference_id', $reference->id))
            ->latest()
            ->paginate(15);

        return $this->paginated($references, ReferenceResource::class, 'Documents cités récupérés avec succès');
    }

    /**
     * GET /references/{reference}/cited-by - Documents qui citent ce document
     */
    public function citedBy(Request $request, Reference $reference)
    {
        $references = Reference::with(['category', 'publisher', 'authors'])
            ->whereHas('citingCitations', fn($q) => $q->where('cited_reference_id', $reference->id))
            ->latest()
            ->paginate(15);

        return $this->paginated($references, ReferenceResource::class, 'Documents qui citent récupérés avec succès');
    }

    /**
     * POST /references/{citing}/cite/{cited} - Créer une citation entre deux documents
     */
    public function cite(CiteReferenceRequest $request, Reference $citing, Reference $cited)
    {
        $citation = $this->citationService->createCitationBetweenReferences(
            $citing,
            $cited,
            $request->context,
            $request->citation_style ?? 'apa',
            $request->page_number
        );

        if ($citation->wasRecentlyCreated) {
            return $this->created(
                new CitationResource($citation->load(['citingReference', 'citedReference'])),
                'Citation créée avec succès'
            );
        }

        return $this->success(
            new CitationResource($citation->load(['citingReference', 'citedReference'])),
            'Citation existe déjà'
        );
    }

    /**
     * GET /references/{reference}/citation-count - Nombre de citations
     */
    public function count(Request $request, Reference $reference)
    {
        $count = $reference->citation_count;

        return $this->success(['count' => $count], 'Nombre de citations');
    }
}
