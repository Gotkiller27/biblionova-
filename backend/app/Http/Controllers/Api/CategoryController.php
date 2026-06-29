<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Models\Category;
use App\Helpers\ApiResponse;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CategoryCollection;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Category::class);

        $query = Category::query();

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('parent_id')) {
            if ($request->parent_id === 'null') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $request->parent_id);
            }
        }

        if ($request->boolean('with_trashed')) {
            $query->withTrashed();
        }

        if ($request->boolean('only_trashed')) {
            $query->onlyTrashed();
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        // Eager loading
        if ($request->boolean('with_children')) {
            $query->with('children');
        }

        if ($request->boolean('with_parent')) {
            $query->with('parent');
        }

        // Pagination or tree view
        if ($request->boolean('tree')) {
            $categories = $this->categoryService->getCategoryTree();
            return ApiResponse::success($categories, 'Category tree retrieved successfully');
        }

        $categories = $query->paginate($request->get('per_page', 15));

        return ApiResponse::paginated($categories, CategoryResource::class, 'Categories retrieved successfully');
    }

    public function store(StoreCategoryRequest $request)
    {
        $this->authorize('create', Category::class);
        $category = Category::create($request->validated());

        activity()
            ->causedBy(auth()->user())
            ->performedOn($category)
            ->log('Category created');

        return ApiResponse::success(new CategoryResource($category), 'Category created successfully', 201);
    }

    public function show(Category $category)
    {
        $this->authorize('view', $category);
        $category->load(['parent', 'children', 'references']);
        
        return ApiResponse::success([
            'category' => new CategoryResource($category),
            'statistics' => $this->categoryService->getCategoryStatistics($category),
        ], 'Category retrieved successfully');
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $this->authorize('update', $category);
        $category->update($request->validated());

        activity()
            ->causedBy(auth()->user())
            ->performedOn($category)
            ->log('Category updated');

        return ApiResponse::success(new CategoryResource($category), 'Category updated successfully');
    }

    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);
        
        if ($category->references()->exists() || $category->children()->exists()) {
            return ApiResponse::error('Cannot delete category with references or children', 400);
        }
        
        $category->delete();

        activity()
            ->causedBy(auth()->user())
            ->performedOn($category)
            ->log('Category deleted');

        return ApiResponse::success(null, 'Category deleted successfully');
    }

    public function restore(Category $category)
    {
        $this->authorize('restore', $category);
        $category->restore();

        activity()
            ->causedBy(auth()->user())
            ->performedOn($category)
            ->log('Category restored');

        return ApiResponse::success(new CategoryResource($category), 'Category restored successfully');
    }

    public function forceDelete(Category $category)
    {
        $this->authorize('forceDelete', $category);
        
        if ($category->references()->withTrashed()->exists() || $category->children()->withTrashed()->exists()) {
            return ApiResponse::error('Cannot force delete category with references or children', 400);
        }
        
        $category->forceDelete();

        activity()
            ->causedBy(auth()->user())
            ->performedOn($category)
            ->log('Category permanently deleted');

        return ApiResponse::success(null, 'Category permanently deleted');
    }

    public function tree()
    {
        $this->authorize('viewAny', Category::class);
        $tree = $this->categoryService->getCategoryTree();
        
        return ApiResponse::success($tree, 'Category tree retrieved successfully');
    }

    public function flatList()
    {
        $this->authorize('viewAny', Category::class);
        $list = $this->categoryService->getFlatCategoryList();
        
        return ApiResponse::success($list, 'Category flat list retrieved successfully');
    }

    public function statistics(?Category $category = null)
    {
        $this->authorize('viewAny', Category::class);
        $statistics = $this->categoryService->getCategoryStatistics($category);
        
        return ApiResponse::success($statistics, 'Category statistics retrieved successfully');
    }

    public function availableParents(Category $excludeCategory = null)
    {
        $this->authorize('viewAny', Category::class);
        $parents = $this->categoryService->getAvailableParents($excludeCategory);
        
        return ApiResponse::success($parents, 'Available parent categories retrieved successfully');
    }

    public function move(Request $request, Category $category)
    {
        $this->authorize('update', $category);
        
        $request->validate([
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        try {
            $category = $this->categoryService->moveCategory($category, $request->parent_id);

            activity()
                ->causedBy(auth()->user())
                ->performedOn($category)
                ->log('Category moved');

            return ApiResponse::success(new CategoryResource($category), 'Category moved successfully');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 400);
        }
    }

    public function path(Category $category)
    {
        $this->authorize('view', $category);
        $path = $this->categoryService->getCategoryPath($category);
        
        return ApiResponse::success(CategoryResource::collection($path), 'Category path retrieved successfully');
    }

    public function search(Request $request)
    {
        $this->authorize('viewAny', Category::class);
        
        $request->validate([
            'search' => 'required|string|min:2',
        ]);

        $filters = [
            'status' => $request->get('status'),
            'parent_id' => $request->get('parent_id'),
            'with_trashed' => $request->boolean('with_trashed'),
            'only_trashed' => $request->boolean('only_trashed'),
        ];

        $categories = $this->categoryService->searchCategories($request->search, $filters);
        
        return ApiResponse::success(CategoryResource::collection($categories), 'Categories search results retrieved successfully');
    }

    public function topCategories(Request $request)
    {
        $this->authorize('viewAny', Category::class);
        $limit = $request->get('limit', 10);
        $categories = $this->categoryService->getCategoriesByReferenceCount($limit);
        
        return ApiResponse::success($categories, 'Top categories by reference count retrieved successfully');
    }

    public function recentCategories(Request $request)
    {
        $this->authorize('viewAny', Category::class);
        $limit = $request->get('limit', 5);
        $categories = $this->categoryService->getRecentCategories($limit);
        
        return ApiResponse::success($categories, 'Recent categories retrieved successfully');
    }
}

