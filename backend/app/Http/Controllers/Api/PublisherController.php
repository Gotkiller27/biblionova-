<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Publisher\StorePublisherRequest;
use App\Http\Requests\Publisher\UpdatePublisherRequest;
use App\Models\Publisher;
use App\Helpers\ApiResponse;
use App\Http\Resources\PublisherResource;

class PublisherController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Publisher::class);
        $publishers = Publisher::withCount('references')->latest()->paginate(15);
        return ApiResponse::paginated($publishers, PublisherResource::class, 'Publishers retrieved successfully');
    }

    public function store(StorePublisherRequest $request)
    {
        $this->authorize('create', Publisher::class);
        $publisher = Publisher::create($request->validated());

        activity()
            ->causedBy(auth()->user())
            ->performedOn($publisher)
            ->log('Publisher created');

        return ApiResponse::success(new PublisherResource($publisher), 'Publisher created successfully', 201);
    }

    public function show(Publisher $publisher)
    {
        $this->authorize('view', $publisher);
        return ApiResponse::success(new PublisherResource($publisher->load('references')), 'Publisher retrieved successfully');
    }

    public function update(UpdatePublisherRequest $request, Publisher $publisher)
    {
        $this->authorize('update', $publisher);
        $publisher->update($request->validated());

        activity()
            ->causedBy(auth()->user())
            ->performedOn($publisher)
            ->log('Publisher updated');

        return ApiResponse::success(new PublisherResource($publisher), 'Publisher updated successfully');
    }

    public function destroy(Publisher $publisher)
    {
        $this->authorize('delete', $publisher);
        $publisher->delete();

        activity()
            ->causedBy(auth()->user())
            ->performedOn($publisher)
            ->log('Publisher deleted');

        return ApiResponse::success(null, 'Publisher deleted successfully');
    }
}
