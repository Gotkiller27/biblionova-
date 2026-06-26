<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\StoreDocumentRevisionRequest;
use App\Http\Resources\DocumentRevisionResource;
use App\Models\DocumentRevision;
use Illuminate\Http\Request;

class DocumentRevisionController extends BaseApiController
{
    public function __construct()
    {
        $this->authorizeResource(DocumentRevision::class, 'document_revision');
    }

    /**
     * Liste toutes les révisions (bibliothécaire et admin)
     */
    public function index(Request $request)
    {
        $revisions = DocumentRevision::with(['reference', 'bibliothecaire'])
            ->when($request->reference_id, fn($q) => $q->where('reference_id', $request->reference_id))
            ->when($request->action, fn($q) => $q->where('action', $request->action))
            ->latest()
            ->paginate(15);

        return $this->paginated($revisions, DocumentRevisionResource::class, 'Révisions récupérées avec succès');
    }

    /**
     * Créer une nouvelle révision
     */
    public function store(StoreDocumentRevisionRequest $request)
    {
        $data = $request->validated();
        $data['bibliothecaire_id'] = $request->user()->id;

        $revision = DocumentRevision::create($data);

        return $this->created(
            new DocumentRevisionResource($revision->load(['reference', 'bibliothecaire'])),
            'Révision créée avec succès'
        );
    }

    /**
     * Afficher une révision spécifique
     */
    public function show(DocumentRevision $documentRevision)
    {
        return $this->success(
            new DocumentRevisionResource($documentRevision->load(['reference', 'bibliothecaire'])),
            'Révision récupérée avec succès'
        );
    }

    /**
     * Mettre à jour une révision
     */
    public function update(Request $request, DocumentRevision $documentRevision)
    {
        $validated = $request->validate([
            'action' => ['sometimes', 'in:creation,modification,correction,archivage'],
            'commentaire' => ['nullable', 'string'],
        ]);

        $documentRevision->update($validated);

        return $this->success(
            new DocumentRevisionResource($documentRevision->load(['reference', 'bibliothecaire'])),
            'Révision mise à jour avec succès'
        );
    }

    /**
     * Supprimer une révision
     */
    public function destroy(DocumentRevision $documentRevision)
    {
        $documentRevision->delete();

        return $this->success(null, 'Révision supprimée avec succès');
    }

    /**
     * GET /document-revisions/reference/{reference_id} - Révisions d'une référence
     */
    public function byReference($referenceId)
    {
        $revisions = DocumentRevision::with(['bibliothecaire'])
            ->where('reference_id', $referenceId)
            ->latest()
            ->get();

        return $this->collection($revisions, DocumentRevisionResource::class, 'Révisions de la référence récupérées avec succès');
    }
}
