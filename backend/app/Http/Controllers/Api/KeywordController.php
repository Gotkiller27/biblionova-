<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Keyword\StoreKeywordRequest;
use App\Http\Requests\Keyword\UpdateKeywordRequest;
use App\Models\Keyword;
use App\Helpers\ApiResponse;
use App\Http\Resources\KeywordResource;
use Illuminate\Http\Request;

class KeywordController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Keyword::class);
        $keywords = Keyword::when($request->search, fn($q) => $q->search($request->search))
            ->withCount('references')
            ->latest()
            ->paginate(15);
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
        return ApiResponse::success(new KeywordResource($keyword->load('references')), 'Keyword retrieved successfully');
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
}
