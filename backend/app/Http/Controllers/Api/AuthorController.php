<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Author\StoreAuthorRequest;
use App\Http\Requests\Author\UpdateAuthorRequest;
use App\Models\Author;
use App\Services\AuthorService;
use App\Helpers\ApiResponse;
use App\Http\Resources\AuthorResource;
use App\Http\Resources\AuthorCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AuthorController extends Controller
{
    protected AuthorService $authorService;

    public function __construct(AuthorService $authorService)
    {
        $this->authorService = $authorService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Author::class);

        $filters = [
            'search' => $request->search,
            'nationality' => $request->nationality,
            'status' => $request->status,
            'with_trashed' => $request->boolean('with_trashed', false),
            'only_trashed' => $request->boolean('only_trashed', false),
        ];

        $authors = $this->authorService->getAuthors(
            $filters,
            $request->input('per_page', 15),
            $request->input('sort_by', 'last_name'),
            $request->input('sort_order', 'asc')
        );

        return ApiResponse::paginated($authors, AuthorResource::class, 'Authors retrieved successfully');
    }

    public function store(StoreAuthorRequest $request)
    {
        $this->authorize('create', Author::class);

        $data = $request->validated();

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('authors', 'public');
            $data['photo'] = $photoPath;
        }

        $author = Author::create($data);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($author)
            ->log('Author created');

        return ApiResponse::success(new AuthorResource($author), 'Author created successfully', 201);
    }

    public function show(Author $author)
    {
        $this->authorize('view', $author);

        $author->load(['references', 'coAuthors']);

        $statistics = $this->authorService->getAuthorStatistics($author->id);

        return ApiResponse::success([
            'author' => new AuthorResource($author),
            'statistics' => $statistics,
        ], 'Author retrieved successfully');
    }

    public function update(UpdateAuthorRequest $request, Author $author)
    {
        $this->authorize('update', $author);

        $data = $request->validated();

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($author->photo) {
                Storage::disk('public')->delete($author->photo);
            }
            $photoPath = $request->file('photo')->store('authors', 'public');
            $data['photo'] = $photoPath;
        }

        // Handle photo deletion
        if ($request->has('delete_photo') && $request->boolean('delete_photo')) {
            if ($author->photo) {
                Storage::disk('public')->delete($author->photo);
                $data['photo'] = null;
            }
        }

        $author->update($data);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($author)
            ->log('Author updated');

        return ApiResponse::success(new AuthorResource($author), 'Author updated successfully');
    }

    public function destroy(Author $author)
    {
        $this->authorize('delete', $author);
        $author->delete();

        activity()
            ->causedBy(auth()->user())
            ->performedOn($author)
            ->log('Author deleted');

        return ApiResponse::success(null, 'Author deleted successfully');
    }

    public function restore(Author $author)
    {
        $this->authorize('restore', $author);
        $author->restore();

        activity()
            ->causedBy(auth()->user())
            ->performedOn($author)
            ->log('Author restored');

        return ApiResponse::success(new AuthorResource($author), 'Author restored successfully');
    }

    public function forceDelete(Author $author)
    {
        $this->authorize('forceDelete', $author);

        // Delete photo if exists
        if ($author->photo) {
            Storage::disk('public')->delete($author->photo);
        }

        $author->forceDelete();

        activity()
            ->causedBy(auth()->user())
            ->performedOn($author)
            ->withProperties(['force' => true])
            ->log('Author force deleted');

        return ApiResponse::success(null, 'Author permanently deleted successfully');
    }

    public function statistics()
    {
        $this->authorize('viewAny', Author::class);

        $statistics = $this->authorService->getStatistics();

        return ApiResponse::success($statistics, 'Author statistics retrieved successfully');
    }

    public function authorStatistics(Author $author)
    {
        $this->authorize('view', $author);

        $statistics = $this->authorService->getAuthorStatistics($author->id);

        return ApiResponse::success($statistics, 'Author statistics retrieved successfully');
    }

    public function search(Request $request)
    {
        $this->authorize('viewAny', Author::class);

        $request->validate([
            'search' => 'required|string|min:2',
            'limit' => 'nullable|integer|max:100',
        ]);

        $authors = $this->authorService->searchAuthors(
            $request->search,
            $request->input('limit', 20)
        );


        return ApiResponse::success(AuthorResource::collection($authors), 'Authors search results');
    }

    public function nationalities()
    {
        $this->authorize('viewAny', Author::class);

        $nationalities = $this->authorService->getNationalities();

        return ApiResponse::success($nationalities, 'Nationalities retrieved successfully');
    }

    public function topAuthors(Request $request)
    {
        $this->authorize('viewAny', Author::class);

        $limit = $request->input('limit', 10);

        $authors = $this->authorService->getTopAuthors($limit);

        return ApiResponse::success(AuthorResource::collection($authors), 'Top authors retrieved successfully');
    }

    public function recentAuthors(Request $request)
    {
        $this->authorize('viewAny', Author::class);

        $limit = $request->input('limit', 5);

        $authors = $this->authorService->getRecentAuthors($limit);

        return ApiResponse::success(AuthorResource::collection($authors), 'Recent authors retrieved successfully');
    }

    public function coAuthors(Author $author)
    {
        $this->authorize('view', $author);

        $coAuthors = $this->authorService->getCoAuthors($author->id);

        return ApiResponse::success(AuthorResource::collection($coAuthors), 'Co-authors retrieved successfully');
    }

    public function addCoAuthor(Request $request, Author $author)
    {
        $this->authorize('update', $author);

        $request->validate([
            'co_author_id' => 'required|exists:authors,id|different:' . $author->id,
        ]);

        try {
            $this->authorService->addCoAuthor($author->id, $request->co_author_id);

            activity()
                ->causedBy(auth()->user())
                ->performedOn($author)
                ->withProperties(['co_author_id' => $request->co_author_id])
                ->log('Co-author added');

            return ApiResponse::success(null, 'Co-author added successfully');
        } catch (\InvalidArgumentException $e) {
            return ApiResponse::error($e->getMessage(), [], 400);
        }
    }

    public function removeCoAuthor(Request $request, Author $author)
    {
        $this->authorize('update', $author);

        $request->validate([
            'co_author_id' => 'required|exists:authors,id',
        ]);

        $this->authorService->removeCoAuthor($author->id, $request->co_author_id);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($author)
            ->withProperties(['co_author_id' => $request->co_author_id])
            ->log('Co-author removed');

        return ApiResponse::success(null, 'Co-author removed successfully');
    }
}
