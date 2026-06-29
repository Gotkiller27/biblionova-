<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Publisher\StorePublisherRequest;
use App\Http\Requests\Publisher\UpdatePublisherRequest;
use App\Models\Publisher;
use App\Helpers\ApiResponse;
use App\Http\Resources\PublisherResource;
use App\Services\PublisherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublisherController extends Controller
{
    protected PublisherService $publisherService;

    public function __construct(PublisherService $publisherService)
    {
        $this->publisherService = $publisherService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Publisher::class);

        $filters = [
            'search' => $request->search,
            'country' => $request->country,
            'city' => $request->city,
            'with_trashed' => $request->boolean('with_trashed'),
            'only_trashed' => $request->boolean('only_trashed'),
        ];

        $publishers = $this->publisherService->getPublishers(
            $filters,
            $request->input('per_page', 15),
            $request->input('sort_by', 'name'),
            $request->input('sort_order', 'asc')
        );

        return ApiResponse::paginated($publishers, PublisherResource::class, 'Publishers retrieved successfully');
    }

    public function store(StorePublisherRequest $request)
    {
        $this->authorize('create', Publisher::class);

        $data = $request->validated();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('publishers', 'public');
            $data['logo'] = $logoPath;
        }

        $publisher = Publisher::create($data);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($publisher)
            ->log('Publisher created');

        return ApiResponse::success(new PublisherResource($publisher), 'Publisher created successfully', 201);
    }

    public function show(Publisher $publisher)
    {
        $this->authorize('view', $publisher);
        $statistics = $this->publisherService->getPublisherStatistics($publisher->id);

        return ApiResponse::success([
            'publisher' => new PublisherResource($publisher->load('references')),
            'statistics' => $statistics,
        ], 'Publisher retrieved successfully');
    }

    public function update(UpdatePublisherRequest $request, Publisher $publisher)
    {
        $this->authorize('update', $publisher);

        $data = $request->validated();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($publisher->logo) {
                Storage::disk('public')->delete($publisher->logo);
            }
            $logoPath = $request->file('logo')->store('publishers', 'public');
            $data['logo'] = $logoPath;
        }

        // Handle logo deletion
        if ($request->has('delete_logo') && $request->boolean('delete_logo')) {
            if ($publisher->logo) {
                Storage::disk('public')->delete($publisher->logo);
            }
            $data['logo'] = null;
        }

        $publisher->update($data);

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

    public function restore(Publisher $publisher)
    {
        $this->authorize('restore', $publisher);
        $publisher->restore();

        activity()
            ->causedBy(auth()->user())
            ->performedOn($publisher)
            ->log('Publisher restored');

        return ApiResponse::success(new PublisherResource($publisher), 'Publisher restored successfully');
    }

    public function forceDelete(Publisher $publisher)
    {
        $this->authorize('forceDelete', $publisher);

        // Delete logo
        if ($publisher->logo) {
            Storage::disk('public')->delete($publisher->logo);
        }

        $publisher->forceDelete();

        activity()
            ->causedBy(auth()->user())
            ->performedOn($publisher)
            ->log('Publisher force deleted');

        return ApiResponse::success(null, 'Publisher permanently deleted successfully');
    }

    public function statistics()
    {
        $this->authorize('viewAny', Publisher::class);
        $statistics = $this->publisherService->getStatistics();

        return ApiResponse::success($statistics, 'Publisher statistics retrieved successfully');
    }

    public function publisherStatistics(Publisher $publisher)
    {
        $this->authorize('view', $publisher);
        $statistics = $this->publisherService->getPublisherStatistics($publisher->id);

        return ApiResponse::success($statistics, 'Publisher statistics retrieved successfully');
    }

    public function search(Request $request)
    {
        $this->authorize('viewAny', Publisher::class);

        $request->validate([
            'search' => 'required|string',
            'limit' => 'nullable|integer|max:100',
        ]);

        $publishers = $this->publisherService->searchPublishers(
            $request->search,
            $request->input('limit', 20)
        );

        return ApiResponse::success(PublisherResource::collection($publishers), 'Search results retrieved successfully');
    }

    public function countries()
    {
        $this->authorize('viewAny', Publisher::class);
        $countries = $this->publisherService->getCountries();

        return ApiResponse::success($countries, 'Countries retrieved successfully');
    }

    public function cities()
    {
        $this->authorize('viewAny', Publisher::class);
        $cities = $this->publisherService->getCities();

        return ApiResponse::success($cities, 'Cities retrieved successfully');
    }

    public function topPublishers(Request $request)
    {
        $this->authorize('viewAny', Publisher::class);

        $limit = $request->input('limit', 10);
        $publishers = $this->publisherService->getTopPublishers($limit);

        return ApiResponse::success(PublisherResource::collection($publishers), 'Top publishers retrieved successfully');
    }

    public function recentPublishers(Request $request)
    {
        $this->authorize('viewAny', Publisher::class);

        $limit = $request->input('limit', 5);
        $publishers = $this->publisherService->getRecentPublishers($limit);

        return ApiResponse::success(PublisherResource::collection($publishers), 'Recent publishers retrieved successfully');
    }
}
