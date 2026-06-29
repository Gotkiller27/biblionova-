<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Keyword\StoreKeywordRequest;
use App\Http\Requests\Keyword\UpdateKeywordRequest;
use App\Models\Keyword;
use App\Helpers\ApiResponse;
use App\Http\Resources\KeywordResource;
use App\Services\KeywordService;
use Illuminate\Http\Request;

class KeywordController extends Controller
{
    protected KeywordService $keywordService;

    public function __construct(KeywordService $keywordService)
    {
        $this->keywordService = $keywordService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Keyword::class);

        $filters = [
            'search' => $request->search,
            'with_trashed' => $request->boolean('with_trashed'),
            'only_trashed' => $request->boolean('only_trashed'),
        ];

        $keywords = $this->keywordService->getKeywords(
            $filters,
            $request->input('per_page', 15),
            $request->input('sort_by', 'name'),
            $request->input('sort_order', 'asc')
        );

        return ApiResponse::paginated($keywords, KeywordResource::class, 'Keywords retrieved successfully');
    }

    public function store(StoreKeywordRequest $request)
    {
        $this->authorize('create', Keyword::class);
        $keyword = Keyword::create($request->validated());

        activity()
            ->causedBy(auth()->user())
            ->performedOn($keyword)
            ->log('Keyword created');

        return ApiResponse::success(new KeywordResource($keyword), 'Keyword created successfully', 201);
    }

    public function show(Keyword $keyword)
    {
        $this->authorize('view', $keyword);
        $statistics = $this->keywordService->getKeywordStatistics($keyword->id);

        return ApiResponse::success([
            'keyword' => new KeywordResource($keyword->load('references')),
            'statistics' => $statistics,
        ], 'Keyword retrieved successfully');
    }

    public function update(UpdateKeywordRequest $request, Keyword $keyword)
    {
        $this->authorize('update', $keyword);
        $keyword->update($request->validated());

        activity()
            ->causedBy(auth()->user())
            ->performedOn($keyword)
            ->log('Keyword updated');

        return ApiResponse::success(new KeywordResource($keyword), 'Keyword updated successfully');
    }

    public function destroy(Keyword $keyword)
    {
        $this->authorize('delete', $keyword);
        $keyword->delete();

        activity()
            ->causedBy(auth()->user())
            ->performedOn($keyword)
            ->log('Keyword deleted');

        return ApiResponse::success(null, 'Keyword deleted successfully');
    }

    public function restore(Keyword $keyword)
    {
        $this->authorize('restore', $keyword);
        $keyword->restore();

        activity()
            ->causedBy(auth()->user())
            ->performedOn($keyword)
            ->log('Keyword restored');

        return ApiResponse::success(new KeywordResource($keyword), 'Keyword restored successfully');
    }

    public function forceDelete(Keyword $keyword)
    {
        $this->authorize('forceDelete', $keyword);
        $keyword->forceDelete();

        activity()
            ->causedBy(auth()->user())
            ->performedOn($keyword)
            ->log('Keyword force deleted');

        return ApiResponse::success(null, 'Keyword permanently deleted successfully');
    }

    public function statistics()
    {
        $this->authorize('viewAny', Keyword::class);
        $statistics = $this->keywordService->getStatistics();

        return ApiResponse::success($statistics, 'Keyword statistics retrieved successfully');
    }

    public function keywordStatistics(Keyword $keyword)
    {
        $this->authorize('view', $keyword);
        $statistics = $this->keywordService->getKeywordStatistics($keyword->id);

        return ApiResponse::success($statistics, 'Keyword statistics retrieved successfully');
    }

    public function search(Request $request)
    {
        $this->authorize('viewAny', Keyword::class);

        $request->validate([
            'search' => 'required|string',
            'limit' => 'nullable|integer|max:100',
        ]);

        $keywords = $this->keywordService->searchKeywords(
            $request->search,
            $request->input('limit', 20)
        );

        return ApiResponse::success(KeywordResource::collection($keywords), 'Search results retrieved successfully');
    }

    public function suggestions(Request $request)
    {
        $this->authorize('viewAny', Keyword::class);

        $request->validate([
            'partial' => 'required|string',
            'limit' => 'nullable|integer|max:50',
        ]);

        $suggestions = $this->keywordService->getSuggestions(
            $request->partial,
            $request->input('limit', 10)
        );

        return ApiResponse::success(KeywordResource::collection($suggestions), 'Suggestions retrieved successfully');
    }

    public function tagCloud(Request $request)
    {
        $this->authorize('viewAny', Keyword::class);

        $limit = $request->input('limit', 50);
        $tagCloud = $this->keywordService->getTagCloud($limit);

        return ApiResponse::success($tagCloud, 'Tag cloud retrieved successfully');
    }

    public function topKeywords(Request $request)
    {
        $this->authorize('viewAny', Keyword::class);

        $limit = $request->input('limit', 10);
        $keywords = $this->keywordService->getTopKeywords($limit);

        return ApiResponse::success(KeywordResource::collection($keywords), 'Top keywords retrieved successfully');
    }

    public function recentKeywords(Request $request)
    {
        $this->authorize('viewAny', Keyword::class);

        $limit = $request->input('limit', 10);
        $keywords = $this->keywordService->getRecentKeywords($limit);

        return ApiResponse::success(KeywordResource::collection($keywords), 'Recent keywords retrieved successfully');
    }

    public function trendingKeywords(Request $request)
    {
        $this->authorize('viewAny', Keyword::class);

        $limit = $request->input('limit', 10);
        $keywords = $this->keywordService->getTrendingKeywords($limit);

        return ApiResponse::success(KeywordResource::collection($keywords), 'Trending keywords retrieved successfully');
    }

    public function import(Request $request)
    {
        $this->authorize('create', Keyword::class);

        $request->validate([
            'keywords' => 'required|array',
            'keywords.*.name' => 'required|string|max:255',
        ]);

        $result = $this->keywordService->importKeywords($request->keywords);

        activity()
            ->causedBy(auth()->user())
            ->log('Keywords imported', ['imported' => $result['imported'], 'skipped' => $result['skipped']]);

        return ApiResponse::success($result, 'Keywords imported successfully');
    }

    public function export()
    {
        $this->authorize('viewAny', Keyword::class);

        return $this->keywordService->exportKeywords();
    }
}
