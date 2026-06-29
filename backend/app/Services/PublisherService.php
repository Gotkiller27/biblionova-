<?php

namespace App\Services;

use App\Models\Publisher;
use Illuminate\Pagination\LengthAwarePaginator;

class PublisherService
{
    public function getPublishers(array $filters = [], int $perPage = 15, string $sortBy = 'name', string $sortOrder = 'asc'): LengthAwarePaginator
    {
        $query = Publisher::query();

        // Search
        if (isset($filters['search'])) {
            $query->search($filters['search']);
        }

        // Filter by country
        if (isset($filters['country']) && $filters['country']) {
            $query->byCountry($filters['country']);
        }

        // Filter by city
        if (isset($filters['city']) && $filters['city']) {
            $query->byCity($filters['city']);
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
            'total_publishers' => Publisher::count(),
            'total_references' => Publisher::withCount('references')->get()->sum('references_count'),
            'countries' => Publisher::select('country')
                ->whereNotNull('country')
                ->groupBy('country')
                ->orderByRaw('COUNT(*) DESC')
                ->limit(10)
                ->get()
                ->map(function ($item) {
                    return [
                        'country' => $item->country,
                        'count' => Publisher::where('country', $item->country)->count(),
                    ];
                }),
            'top_publishers' => Publisher::withCount('references')
                ->orderBy('references_count', 'desc')
                ->limit(10)
                ->get(),
            'recent_publishers' => Publisher::orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
        ];
    }

    public function getPublisherStatistics(int $publisherId): array
    {
        $publisher = Publisher::withCount('references')->findOrFail($publisherId);

        return [
            'publisher_id' => $publisher->id,
            'name' => $publisher->name,
            'references_count' => $publisher->references_count,
            'country' => $publisher->country,
            'city' => $publisher->city,
        ];
    }

    public function searchPublishers(string $searchTerm, int $limit = 20): \Illuminate\Database\Eloquent\Collection
    {
        return Publisher::search($searchTerm)
            ->withCount('references')
            ->limit($limit)
            ->get();
    }

    public function getCountries(): array
    {
        return Publisher::select('country')
            ->whereNotNull('country')
            ->distinct()
            ->orderBy('country')
            ->pluck('country')
            ->toArray();
    }

    public function getCities(): array
    {
        return Publisher::select('city')
            ->whereNotNull('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city')
            ->toArray();
    }

    public function getTopPublishers(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Publisher::withCount('references')
            ->orderBy('references_count', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getRecentPublishers(int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return Publisher::orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
