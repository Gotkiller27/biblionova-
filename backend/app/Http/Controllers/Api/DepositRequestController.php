<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\AssignManagerRequest;
use App\Http\Requests\ReviewDepositRequestRequest;
use App\Http\Requests\StoreDepositRequestRequest;
use App\Http\Requests\UpdateDepositRequestRequest;
use App\Http\Requests\DepositRequest\StoreAttachmentRequest;
use App\Http\Resources\DepositRequestResource;
use App\Http\Resources\DepositRequestAttachmentResource;
use App\Models\DepositRequest;
use App\Models\DepositRequestAttachment;
use App\Models\Reference;
use App\Services\DepositRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DepositRequestController extends BaseApiController
{
    public function __construct(protected DepositRequestService $depositRequestService)
    {
        $this->authorizeResource(DepositRequest::class, 'deposit_request');
    }

    /**
     * Liste toutes les demandes (admin uniquement)
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', DepositRequest::class);

        $requests = DepositRequest::with(['applicant', 'assignedManager', 'reviews.reviewer'])
            ->when($request->search, fn($q) => $q->where('title', 'like', "%{$request->search}%"))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15);

        return $this->paginated($requests, DepositRequestResource::class, 'Demandes récupérées avec succès');
    }

    /**
     * Créer une nouvelle demande
     */
    public function store(StoreDepositRequestRequest $request)
    {
        $data = $request->validated();
        $data['applicant_id'] = $request->user()->id;
        $data['status'] = 'pending';

        // Upload du fichier
        if ($request->hasFile('proposed_file')) {
            $data['proposed_file'] = $request->file('proposed_file')->store('deposit-requests', 'public');
        }

        $depositRequest = DepositRequest::create($data);

        return $this->created(
            new DepositRequestResource($depositRequest->load(['applicant', 'reviews'])),
            'Demande créée avec succès'
        );
    }

    /**
     * Afficher une demande spécifique
     */
    public function show(DepositRequest $depositRequest)
    {
        return $this->success(
            new DepositRequestResource($depositRequest->load(['applicant', 'assignedManager', 'reviews.reviewer'])),
            'Demande récupérée avec succès'
        );
    }

    /**
     * Mettre à jour une demande (uniquement si pending)
     */
    public function update(UpdateDepositRequestRequest $request, DepositRequest $depositRequest)
    {
        $data = $request->validated();

        // Upload du nouveau fichier si fourni
        if ($request->hasFile('proposed_file')) {
            // Supprimer l'ancien fichier
            if ($depositRequest->proposed_file) {
                Storage::disk('public')->delete($depositRequest->proposed_file);
            }
            $data['proposed_file'] = $request->file('proposed_file')->store('deposit-requests', 'public');
        }

        $depositRequest->update($data);

        return $this->success(
            new DepositRequestResource($depositRequest->load(['applicant', 'reviews'])),
            'Demande mise à jour avec succès'
        );
    }

    /**
     * Supprimer une demande (uniquement si pending)
     */
    public function destroy(DepositRequest $depositRequest)
    {
        // Supprimer le fichier
        if ($depositRequest->proposed_file) {
            Storage::disk('public')->delete($depositRequest->proposed_file);
        }

        $depositRequest->delete();

        return $this->success(null, 'Demande supprimée avec succès');
    }

    /**
     * GET /deposit-requests/my-requests - Mes demandes
     */
    public function myRequests(Request $request)
    {
        $requests = DepositRequest::with(['assignedManager', 'reviews.reviewer'])
            ->where('applicant_id', $request->user()->id)
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15);

        return $this->paginated($requests, DepositRequestResource::class, 'Vos demandes récupérées avec succès');
    }

    /**
     * GET /deposit-requests/assigned - Demandes qui me sont assignées
     */
    public function assigned(Request $request)
    {
        $user = $request->user();

        if (!$user->hasRole('responsable_validation')) {
            return $this->forbidden('Accès non autorisé');
        }

        $requests = DepositRequest::with(['applicant', 'reviews.reviewer'])
            ->where('assigned_manager_id', $user->id)
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15);

        return $this->paginated($requests, DepositRequestResource::class, 'Demandes assignées récupérées avec succès');
    }

    /**
     * GET /deposit-requests/pending - Demandes en attente
     */
    public function pending(Request $request)
    {
        $user = $request->user();

        if (!$user->hasAnyRole(['responsable_validation', 'admin'])) {
            return $this->forbidden('Accès non autorisé');
        }

        $requests = DepositRequest::with(['applicant', 'assignedManager', 'reviews.reviewer'])
            ->whereIn('status', ['pending', 'second_review'])
            ->latest()
            ->paginate(15);

        return $this->paginated($requests, DepositRequestResource::class, 'Demandes en attente récupérées avec succès');
    }

    /**
     * POST /deposit-requests/{id}/assign - Assigner un responsable
     */
    public function assign(AssignManagerRequest $request, DepositRequest $depositRequest)
    {
        $this->authorize('assign', $depositRequest);

        $depositRequest->update([
            'assigned_manager_id' => $request->manager_id,
        ]);

        return $this->success(
            new DepositRequestResource($depositRequest->load(['applicant', 'assignedManager', 'reviews'])),
            'Responsable assigné avec succès'
        );
    }

    /**
     * POST /deposit-requests/{id}/approve - Approuver une demande
     */
    public function approve(ReviewDepositRequestRequest $request, DepositRequest $depositRequest)
    {
        $this->authorize('review', $depositRequest);

        $user = $request->user();

        // Créer la review
        $depositRequest->reviews()->create([
            'reviewer_id' => $user->id,
            'reviewer_role' => $user->roles->first()?->name ?? 'user',
            'decision' => 'approve',
            'justification' => $request->justification,
        ]);

        // Mettre à jour le statut
        $newStatus = $user->hasRole('admin') ? 'approved' : 'approved_by_manager';
        $depositRequest->update(['status' => $newStatus]);

        return $this->success(
            new DepositRequestResource($depositRequest->load(['applicant', 'reviews.reviewer'])),
            'Demande approuvée avec succès'
        );
    }

    /**
     * POST /deposit-requests/{id}/reject - Rejeter une demande
     */
    public function reject(ReviewDepositRequestRequest $request, DepositRequest $depositRequest)
    {
        $this->authorize('review', $depositRequest);

        $user = $request->user();

        // Créer la review
        $depositRequest->reviews()->create([
            'reviewer_id' => $user->id,
            'reviewer_role' => $user->roles->first()?->name ?? 'user',
            'decision' => 'reject',
            'justification' => $request->justification,
        ]);

        // Mettre à jour le statut
        $newStatus = $user->hasRole('admin') ? 'rejected' : 'rejected_by_manager';
        $depositRequest->update(['status' => $newStatus]);

        return $this->success(
            new DepositRequestResource($depositRequest->load(['applicant', 'reviews.reviewer'])),
            'Demande rejetée avec succès'
        );
    }

    /**
     * POST /deposit-requests/{id}/second-review - Demander un second avis
     */
    public function secondReview(ReviewDepositRequestRequest $request, DepositRequest $depositRequest)
    {
        $this->authorize('review', $depositRequest);

        $user = $request->user();

        // Créer la review
        $depositRequest->reviews()->create([
            'reviewer_id' => $user->id,
            'reviewer_role' => $user->roles->first()?->name ?? 'user',
            'decision' => 'second_review',
            'justification' => $request->justification,
        ]);

        // Mettre à jour le statut
        $depositRequest->update(['status' => 'second_review']);

        return $this->success(
            new DepositRequestResource($depositRequest->load(['applicant', 'reviews.reviewer'])),
            'Second avis demandé avec succès'
        );
    }

    /**
     * POST /deposit-requests/{id}/publish - Publier une demande approuvée
     */
    public function publish(Request $request, DepositRequest $depositRequest)
    {
        $this->authorize('publish', $depositRequest);

        if ($depositRequest->status !== 'approved') {
            return $this->error(
                'Seules les demandes approuvées peuvent être publiées',
                null,
                400
            );
        }

        // Créer la référence dans le catalogue
        $reference = Reference::create([
            'title' => $depositRequest->title,
            'uploaded_by' => $depositRequest->applicant_id,
            'bibliothecaire_id' => $request->user()->id,
            'status' => 'published',
            // Ajouter d'autres champs selon votre schéma Reference
        ]);

        // Mettre à jour le statut de la demande
        $depositRequest->update(['status' => 'published']);

        return $this->success([
            'deposit_request' => new DepositRequestResource($depositRequest),
            'reference_id' => $reference->id,
        ], 'Demande publiée avec succès');
    }

    /**
     * POST /deposit-requests/{id}/submit - Soumettre une demande en brouillon
     */
    public function submit(Request $request, DepositRequest $depositRequest)
    {
        $this->authorize('update', $depositRequest);

        try {
            $depositRequest = $this->depositRequestService->submitDepositRequest($depositRequest);
            return $this->success(
                new DepositRequestResource($depositRequest),
                'Demande soumise avec succès'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 400);
        }
    }

    /**
     * POST /deposit-requests/{id}/cancel - Annuler une demande
     */
    public function cancel(Request $request, DepositRequest $depositRequest)
    {
        $this->authorize('update', $depositRequest);

        try {
            $depositRequest = $this->depositRequestService->cancelDepositRequest($depositRequest, $request->reason ?? null);
            return $this->success(
                new DepositRequestResource($depositRequest),
                'Demande annulée avec succès'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 400);
        }
    }

    /**
     * GET /deposit-requests/{id}/attachments - Liste des pièces jointes
     */
    public function attachments(Request $request, DepositRequest $depositRequest)
    {
        $attachments = $depositRequest->attachments()
            ->when($request->file_type, fn($q) => $q->byType($request->file_type))
            ->latest()
            ->paginate(15);

        return $this->paginated($attachments, DepositRequestAttachmentResource::class, 'Pièces jointes récupérées avec succès');
    }

    /**
     * POST /deposit-requests/{id}/attachments - Ajouter une pièce jointe
     */
    public function storeAttachment(StoreAttachmentRequest $request, DepositRequest $depositRequest)
    {
        $this->authorize('update', $depositRequest);

        $attachment = $this->depositRequestService->addAttachment(
            $depositRequest,
            $request->file('file'),
            $request->file_type,
            $request->description
        );

        return $this->created(
            new DepositRequestAttachmentResource($attachment),
            'Pièce jointe ajoutée avec succès'
        );
    }

    /**
     * DELETE /deposit-requests/{id}/attachments/{attachment} - Supprimer une pièce jointe
     */
    public function deleteAttachment(Request $request, DepositRequest $depositRequest, DepositRequestAttachment $attachment)
    {
        $this->authorize('update', $depositRequest);

        try {
            $this->depositRequestService->deleteAttachment($depositRequest, $attachment);
            return $this->success(null, 'Pièce jointe supprimée avec succès');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 404);
        }
    }

    /**
     * GET /deposit-requests/{id}/draft - Récupérer les brouillons de l'utilisateur
     */
    public function drafts(Request $request)
    {
        $drafts = $this->depositRequestService->getDrafts($request->user()->id)
            ->paginate(15);

        return $this->paginated($drafts, DepositRequestResource::class, 'Brouillons récupérés avec succès');
    }
}
