<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Author\StoreAuthorRequest;
use App\Http\Requests\Author\UpdateAuthorRequest;
use App\Models\Author;
use App\Helpers\ApiResponse;
use App\Http\Resources\AuthorResource;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Author::class);
        $authors = Author::when($request->search, fn($q) => $q->search($request->search))
            ->withCount('references')
            ->latest()
            ->paginate(15);
        return ApiResponse::paginated($authors, AuthorResource::class, 'Authors retrieved successfully');
    }

    public function store(StoreAuthorRequest $request)
    {
        $this->authorize('create', Author::class);
        $author = Author::create($request->validated());

        activity()
            ->causedBy(auth()->user())
            ->performedOn($author)
            ->log('Author created');

        return ApiResponse::success(new AuthorResource($author), 'Author created successfully', 201);
    }

    public function show(Author $author)
    {
        $this->authorize('view', $author);
        return ApiResponse::success(new AuthorResource($author->load('references')), 'Author retrieved successfully');
    }

    public function update(UpdateAuthorRequest $request, Author $author)
    {
        $this->authorize('update', $author);
        $author->update($request->validated());

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
}
