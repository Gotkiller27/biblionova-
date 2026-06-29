<?php

namespace App\Services;

use App\Models\Author;
use Illuminate\Pagination\LengthAwarePaginator;

class AuthorService
{
    public function getAuthors(array $filters = [], int $perPage = 15, string $sortBy = 'last_name', string $sortOrder = 'asc'): LengthAwarePaginator
    {
        $query = Author::query();

        // Search
        if (isset($filters['search'])) {
            $query->search($filters['search']);
        }

        // Filter by nationality
        if (isset($filters['nationality']) && $filters['nationality']) {
            $query->byNationality($filters['nationality']);
        }

        // Filter by alive/deceased status
        if (isset($filters['status'])) {
            if ($filters['status'] === 'alive') {
                $query->alive();
            } elseif ($filters['status'] === 'deceased') {
                $query->deceased();
            }
        }

        // Include trashed
        if (isset($filters['with_trashed']) && $filters['with_trashed']) {
            $query->withTrashed();
        }

        // Only trashed
        if (isset($filters['only_trashed']) && $filters['only_trashed']) {
            $query->onlyTrashed();
        }

        // With references count
        $query->withCount('references');

        // Sort
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    public function getStatistics(): array
    {
        return [
            'total_authors' => Author::count(),
            'alive_authors' => Author::alive()->count(),
            'deceased_authors' => Author::deceased()->count(),
            'total_references' => Author::withCount('references')->get()->sum('references_count'),
            'nationalities' => Author::select('nationality')
                ->whereNotNull('nationality')
                ->groupBy('nationality')
                ->orderByRaw('COUNT(*) DESC')
                ->limit(10)
                ->get()
                ->map(function ($item) {
                    return [
                        'nationality' => $item->nationality,
                        'count' => Author::where('nationality', $item->nationality)->count(),
                    ];
                }),
            'top_authors' => Author::withCount('references')
                ->orderBy('references_count', 'desc')
                ->limit(10)
                ->get(),
            'recent_authors' => Author::orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
        ];
    }

    public function getAuthorStatistics(int $authorId): array
    {
        $author = Author::withCount('references')->findOrFail($authorId);

        return [
            'author_id' => $author->id,
            'full_name' => $author->full_name,
            'references_count' => $author->references_count,
            'co_authors_count' => $author->coAuthors()->count(),
            'age' => $author->age,
            'is_deceased' => $author->is_deceased,
            'nationality' => $author->nationality,
        ];
    }

    public function searchAuthors(string $searchTerm, int $limit = 20): \Illuminate\Database\Eloquent\Collection
    {
        return Author::search($searchTerm)
            ->withCount('references')
            ->limit($limit)
            ->get();
    }

    public function getNationalities(): array
    {
        return Author::select('nationality')
            ->whereNotNull('nationality')
            ->distinct()
            ->orderBy('nationality')
            ->pluck('nationality')
            ->toArray();
    }

    public function getTopAuthors(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Author::withCount('references')
            ->orderBy('references_count', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getRecentAuthors(int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return Author::orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function addCoAuthor(int $authorId, int $coAuthorId): void
    {
        $author = Author::findOrFail($authorId);
        $coAuthor = Author::findOrFail($coAuthorId);

        // Prevent adding self as co-author
        if ($authorId === $coAuthorId) {
            throw new \InvalidArgumentException('Un auteur ne peut pas être son propre co-auteur');
        }

        // Check if relationship already exists
        if ($author->coAuthors()->where('co_author_id', $coAuthorId)->exists()) {
            throw new \InvalidArgumentException('Ce co-auteur est déjà associé');
        }

        $author->coAuthors()->attach($coAuthorId);
    }

    public function removeCoAuthor(int $authorId, int $coAuthorId): void
    {
        $author = Author::findOrFail($authorId);
        $author->coAuthors()->detach($coAuthorId);
    }

    public function getCoAuthors(int $authorId): \Illuminate\Database\Eloquent\Collection
    {
        $author = Author::findOrFail($authorId);
        return $author->coAuthors()->withCount('references')->get();
    }
}
