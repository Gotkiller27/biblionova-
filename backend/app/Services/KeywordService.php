<?php

namespace App\Services;

use App\Models\Keyword;
use Illuminate\Pagination\LengthAwarePaginator;

class KeywordService
{
    public function getKeywords(array $filters = [], int $perPage = 15, string $sortBy = 'name', string $sortOrder = 'asc'): LengthAwarePaginator
    {
        $query = Keyword::query();

        if (isset($filters['search'])) {
            $query->search($filters['search']);
        }

        if (isset($filters['with_trashed']) && $filters['with_trashed']) {
            $query->withTrashed();
        }

        if (isset($filters['only_trashed']) && $filters['only_trashed']) {
            $query->onlyTrashed();
        }

        $query->withCount('references');
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    public function getStatistics(): array
    {
        return [
            'total_keywords' => Keyword::count(),
            'total_references' => Keyword::withCount('references')->get()->sum('references_count'),
            'most_used_keywords' => Keyword::withCount('references')
                ->orderBy('references_count', 'desc')
                ->limit(10)
                ->get(),
            'trending_keywords' => Keyword::trending()
                ->withCount('references')
                ->limit(10)
                ->get(),
            'recent_keywords' => Keyword::orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),
        ];
    }

    public function getKeywordStatistics(int $keywordId): array
    {
        $keyword = Keyword::withCount('references')->findOrFail($keywordId);

        return [
            'keyword_id' => $keyword->id,
            'name' => $keyword->name,
            'slug' => $keyword->slug,
            'usage_count' => $keyword->usage_count,
            'popularity_score' => $keyword->popularity_score,
            'references_count' => $keyword->references_count,
        ];
    }

    public function searchKeywords(string $searchTerm, int $limit = 20): \Illuminate\Database\Eloquent\Collection
    {
        return Keyword::search($searchTerm)
            ->withCount('references')
            ->limit($limit)
            ->get();
    }

    public function getSuggestions(string $partial, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Keyword::where('name', 'like', "{$partial}%")
            ->orderBy('popularity_score', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getTagCloud(int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return Keyword::withCount('references')
            ->where('references_count', '>', 0)
            ->orderBy('references_count', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($keyword) {
                $maxCount = Keyword::max('usage_count');
                $minCount = Keyword::min('usage_count');
                
                if ($maxCount > $minCount) {
                    $weight = (($keyword->usage_count - $minCount) / ($maxCount - $minCount)) * 100;
                } else {
                    $weight = 50;
                }
                
                $keyword->weight = max(10, min(40, 10 + ($weight * 0.3)));
                return $keyword;
            });
    }

    public function getTopKeywords(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Keyword::withCount('references')
            ->orderBy('references_count', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getRecentKeywords(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Keyword::orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getTrendingKeywords(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Keyword::trending()
            ->withCount('references')
            ->limit($limit)
            ->get();
    }

    public function importKeywords(array $keywords): array
    {
        $imported = 0;
        $skipped = 0;
        $errors = [];

        foreach ($keywords as $keywordData) {
            try {
                $keyword = Keyword::firstOrCreate(
                    ['name' => $keywordData['name']],
                    [
                        'slug' => $keywordData['slug'] ?? null,
                        'description' => $keywordData['description'] ?? null,
                        'usage_count' => $keywordData['usage_count'] ?? 0,
                        'popularity_score' => $keywordData['popularity_score'] ?? 0,
                    ]
                );

                if ($keyword->wasRecentlyCreated) {
                    $imported++;
                } else {
                    $skipped++;
                }
            } catch (\Exception $e) {
                $errors[] = [
                    'keyword' => $keywordData['name'] ?? 'unknown',
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'imported' => $imported,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }

    public function exportKeywords(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $keywords = Keyword::withCount('references')
            ->orderBy('name')
            ->get()
            ->map(function ($keyword) {
                return [
                    'id' => $keyword->id,
                    'name' => $keyword->name,
                    'slug' => $keyword->slug,
                    'description' => $keyword->description,
                    'usage_count' => $keyword->usage_count,
                    'popularity_score' => $keyword->popularity_score,
                    'references_count' => $keyword->references_count,
                    'created_at' => $keyword->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $keyword->updated_at->format('Y-m-d H:i:s'),
                ];
            })
            ->toArray();

        $filename = 'keywords_export_' . date('Y-m-d_H-i-s') . '.json';
        $filepath = storage_path('app/exports/' . $filename);
        
        if (!file_exists(storage_path('app/exports'))) {
            mkdir(storage_path('app/exports'), 0755, true);
        }
        
        file_put_contents($filepath, json_encode($keywords, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return response()->download($filepath)->deleteFileAfterSend(true);
    }
}
