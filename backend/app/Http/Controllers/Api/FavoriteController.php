<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Favorite\StoreFavoriteRequest;
use App\Http\Requests\Favorite\UpdateFavoriteRequest;
use App\Http\Requests\Favorite\ToggleFavoriteRequest;
use App\Http\Resources\FavoriteResource;
use App\Http\Resources\ReferenceResource;
use App\Models\Favorite;
use App\Models\Reference;
use App\Services\FavoriteService;
use Illuminate\Http\Request;

class FavoriteController extends BaseApiController
{
    public function __construct(protected FavoriteService $favoriteService)
    {
        $this->authorizeResource(Favorite::class, 'favorite');
    }

    /**
     * GET /favorites - Liste des favoris de l'utilisateur connecté
     */
    public function index(Request $request)
    {
        $favorites = Favorite::with(['reference.category', 'reference.publisher', 'reference.authors'])
            ->forUser($request->user()->id)
            ->when($request->search, fn($q) => $q->whereHas('reference', fn($r) => $r->search($request->search)))
            ->latest()
            ->paginate(15);

        return $this->paginated($favorites, FavoriteResource::class, 'Favoris récupérés avec succès');
    }

    /**
     * POST /favorites - Ajouter un favori
     */
    public function store(StoreFavoriteRequest $request)
    {
        $favorite = $this->favoriteService->createFavorite(
            $request->user()->id,
            $request->reference_id,
            $request->notes
        );

        if ($favorite->wasRecentlyCreated) {
            return $this->created(
                new FavoriteResource($favorite->load(['reference.category', 'reference.publisher', 'reference.authors'])),
                'Favori ajouté avec succès'
            );
        }

        return $this->success(
            new FavoriteResource($favorite->load(['reference.category', 'reference.publisher', 'reference.authors'])),
            'Favori existe déjà'
        );
    }

    /**
     * GET /favorites/{favorite} - Afficher un favori
     */
    public function show(Favorite $favorite)
    {
        return $this->success(
            new FavoriteResource($favorite->load(['reference.category', 'reference.publisher', 'reference.authors', 'reference.keywords'])),
            'Favori récupéré avec succès'
        );
    }

    /**
     * PUT /favorites/{favorite} - Mettre à jour un favori
     */
    public function update(UpdateFavoriteRequest $request, Favorite $favorite)
    {
        $favorite = $this->favoriteService->updateFavorite($favorite, $request->validated());

        return $this->success(
            new FavoriteResource($favorite),
            'Favori mis à jour avec succès'
        );
    }

    /**
     * DELETE /favorites/{favorite} - Supprimer un favori
     */
    public function destroy(Favorite $favorite)
    {
        $this->favoriteService->deleteFavorite($favorite);

        return $this->success(null, 'Favori supprimé avec succès');
    }

    /**
     * POST /references/{reference}/favorite - Ajouter/Retirer un favori
     */
    public function toggle(ToggleFavoriteRequest $request, Reference $reference)
    {
        $result = $this->favoriteService->toggleFavorite(
            $request->user()->id,
            $reference,
            $request->notes
        );

        if (!$result['favorited']) {
            return $this->success(['favorited' => false], 'Favori retiré avec succès');
        }

        return $this->created(
            new FavoriteResource($result['favorite']->load('reference')),
            'Favori ajouté avec succès'
        );
    }

    /**
     * GET /favorites/check/{reference} - Vérifier si un document est favori
     */
    public function check(Request $request, Reference $reference)
    {
        $isFavorited = $reference->isFavoritedBy($request->user()->id);

        return $this->success(['favorited' => $isFavorited], 'Statut du favori');
    }

    /**
     * GET /favorites/references - Liste des documents favoris
     */
    public function references(Request $request)
    {
        $references = Reference::with(['category', 'publisher', 'authors', 'keywords'])
            ->whereHas('favorites', fn($q) => $q->where('user_id', $request->user()->id))
            ->when($request->search, fn($q) => $q->search($request->search))
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->document_type, fn($q) => $q->where('document_type', $request->document_type))
            ->latest()
            ->paginate(15);

        return $this->paginated($references, ReferenceResource::class, 'Documents favoris récupérés avec succès');
    }
}
