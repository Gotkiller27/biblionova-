<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Models\Category;
use App\Helpers\ApiResponse;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Category::class);
        $categories = Category::with('children')->whereNull('parent_id')->get();
        return ApiResponse::success(CategoryResource::collection($categories), 'Categories retrieved successfully');
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
        return ApiResponse::success(new CategoryResource($category->load(['parent', 'children', 'references'])), 'Category retrieved successfully');
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
        $category->delete();

        activity()
            ->causedBy(auth()->user())
            ->performedOn($category)
            ->log('Category deleted');

        return ApiResponse::success(null, 'Category deleted successfully');
    }
}
