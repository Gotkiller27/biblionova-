<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reference\StoreReferenceRequest;
use App\Http\Requests\Reference\UpdateReferenceRequest;
use App\Models\Reference;
use App\Services\ReferenceService;
use App\Helpers\ApiResponse;
use App\Http\Resources\ReferenceResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReferenceController extends Controller
{
    public function __construct(private ReferenceService $referenceService) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Reference::class);
        $references = Reference::with(['category', 'publisher', 'authors', 'keywords'])
            ->when($request->search, fn($q) => $q->search($request->search))
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15);
        return ApiResponse::paginated($references, ReferenceResource::class, 'References retrieved successfully');
    }

    public function store(StoreReferenceRequest $request)
    {
        $this->authorize('create', Reference::class);
        $reference = $this->referenceService->createReference($request->validated(), auth()->user());
        return ApiResponse::success(new ReferenceResource($reference->load(['category', 'publisher', 'authors', 'keywords'])), 'Reference created successfully', 201);
    }

    public function show(Reference $reference)
    {
        $this->authorize('view', $reference);
        $this->referenceService->recordView($reference, auth()->user());
        return ApiResponse::success(new ReferenceResource($reference->load(['category', 'publisher', 'uploader', 'bibliothecaire', 'authors', 'keywords', 'revisions'])), 'Reference retrieved successfully');
    }

    public function update(UpdateReferenceRequest $request, Reference $reference)
    {
        $this->authorize('update', $reference);
        $reference = $this->referenceService->updateReference($reference, $request->validated(), auth()->user());
        return ApiResponse::success(new ReferenceResource($reference->load(['category', 'publisher', 'authors', 'keywords'])), 'Reference updated successfully');
    }

    public function destroy(Reference $reference)
    {
        $this->authorize('delete', $reference);
        $this->referenceService->deleteReference($reference, auth()->user());
        return ApiResponse::success(null, 'Reference deleted successfully');
    }

    public function download(Reference $reference)
    {
        $this->authorize('download', $reference);
        $this->referenceService->recordDownload($reference, auth()->user());
        return Storage::disk('public')->download($reference->file_path);
    }
}
