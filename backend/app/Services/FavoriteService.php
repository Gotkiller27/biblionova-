<?php

namespace App\Services;

use App\Models\Favorite;
use App\Models\Reference;

class FavoriteService
{
    public function createFavorite(int $userId, int $referenceId, ?string $notes = null): Favorite
    {
        return Favorite::firstOrCreate(
            ['user_id' => $userId, 'reference_id' => $referenceId],
            ['notes' => $notes]
        );
    }

    public function toggleFavorite(int $userId, Reference $reference, ?string $notes = null): array
    {
        $favorite = Favorite::where('user_id', $userId)
            ->where('reference_id', $reference->id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return ['favorited' => false, 'favorite' => null];
        }

        $favorite = Favorite::create([
            'user_id' => $userId,
            'reference_id' => $reference->id,
            'notes' => $notes,
        ]);

        return ['favorited' => true, 'favorite' => $favorite];
    }

    public function updateFavorite(Favorite $favorite, array $data): Favorite
    {
        $favorite->update($data);
        return $favorite->load(['reference.category', 'reference.publisher', 'reference.authors']);
    }

    public function deleteFavorite(Favorite $favorite): void
    {
        $favorite->delete();
    }
}
