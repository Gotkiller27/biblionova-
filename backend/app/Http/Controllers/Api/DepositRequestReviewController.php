<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseApiController;
use App\Http\Resources\DepositRequestReviewResource;
use App\Models\DepositRequestReview;
use Illuminate\Http\Request;

class DepositRequestReviewController extends BaseApiController
{
    public function __construct()
    {
        $this->authorizeResource(DepositRequestReview::class, 'deposit_request_review');
    }

    /**
     * Liste toutes les reviews (admin uniquement)
     */
    public function index()
    {
        $reviews = DepositRequestReview::with(['depositRequest', 'reviewer'])
            ->latest()
            ->paginate(15);

        return $this->paginated($reviews, DepositRequestReviewResource::class, 'Reviews récupérées avec succès');
    }

    /**
     * Afficher une review spécifique
     */
    public function show(DepositRequestReview $depositRequestReview)
    {
        return $this->success(
            new DepositRequestReviewResource($depositRequestReview->load(['depositRequest.applicant', 'reviewer'])),
            'Review récupérée avec succès'
        );
    }

    /**
     * Mettre à jour une review (uniquement par son auteur)
     */
    public function update(Request $request, DepositRequestReview $depositRequestReview)
    {
        $validated = $request->validate([
            'justification' => ['nullable', 'string'],
        ]);

        $depositRequestReview->update($validated);

        return $this->success(
            new DepositRequestReviewResource($depositRequestReview->load(['depositRequest', 'reviewer'])),
            'Review mise à jour avec succès'
        );
    }

    /**
     * Supprimer une review (admin uniquement)
     */
    public function destroy(DepositRequestReview $depositRequestReview)
    {
        $depositRequestReview->delete();

        return $this->success(null, 'Review supprimée avec succès');
    }
}
