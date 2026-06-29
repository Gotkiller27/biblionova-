<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Reference;
use Illuminate\Support\Collection;

class CategoryService
{
    /**
     * Get category tree structure
     */
    public function getCategoryTree(?int $parentId = null): Collection
    {
        $query = Category::query();
        
        if ($parentId === null) {
            $query->roots();
        } else {
            $query->where('parent_id', $parentId);
        }
        
        return $query->with('recursiveChildren')
            ->orderBy('name')
            ->get()
            ->map(function ($category) {
                return $this->formatCategoryNode($category);
            });
    }

    /**
     * Get flat list with hierarchy info
     */
    public function getFlatCategoryList(): Collection
    {
        return Category::with('parent')
            ->orderBy('name')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'full_path' => $category->full_path,
                    'slug' => $category->slug,
                    'full_slug_path' => $category->full_slug_path,
                    'parent_id' => $category->parent_id,
                    'status' => $category->status,
                    'depth' => $category->depth(),
                    'is_root' => $category->isRoot(),
                    'is_leaf' => $category->isLeaf(),
                ];
            });
    }

    /**
     * Get category statistics
     */
    public function getCategoryStatistics(?Category $category = null): array
    {
        if ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'full_path' => $category->full_path,
                'direct_references_count' => $category->references()->count(),
                'total_references_count' => $category->reference_count,
                'direct_children_count' => $category->children()->count(),
                'total_children_count' => $category->descendants()->count(),
                'depth' => $category->depth(),
                'is_root' => $category->isRoot(),
                'is_leaf' => $category->isLeaf(),
                'status' => $category->status,
            ];
        }

        // Global statistics
        return [
            'total_categories' => Category::count(),
            'root_categories' => Category::roots()->count(),
            'active_categories' => Category::active()->count(),
            'inactive_categories' => Category::where('status', 'inactive')->count(),
            'max_depth' => Category::maxDepth(),
            'categories_with_references' => Category::whereHas('references')->count(),
            'leaf_categories' => Category::whereDoesntHave('children')->count(),
        ];
    }

    /**
     * Get available parent categories (excluding current category and its descendants)
     */
    public function getAvailableParents(?Category $excludeCategory = null): Collection
    {
        $query = Category::query();

        if ($excludeCategory) {
            $excludedIds = collect([$excludeCategory->id])
                ->concat($excludeCategory->descendants()->pluck('id'));

            $query->whereNotIn('id', $excludedIds);
        }

        return $query->orderBy('name')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->full_path,
                    'depth' => $category->depth(),
                ];
            });
    }

    /**
     * Search categories
     */
    public function searchCategories(string $search, array $filters = []): Collection
    {
        $query = Category::query();

        // Search in name and description
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });

        // Apply filters
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['parent_id'])) {
            if ($filters['parent_id'] === 'null') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $filters['parent_id']);
            }
        }

        if (isset($filters['with_trashed']) && $filters['with_trashed']) {
            $query->withTrashed();
        }

        if (isset($filters['only_trashed']) && $filters['only_trashed']) {
            $query->onlyTrashed();
        }

        return $query->with('parent')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get category path for breadcrumbs
     */
    public function getCategoryPath(Category $category): Collection
    {
        return $category->ancestors()->reverse()->push($category);
    }

    /**
     * Move category to new parent
     */
    public function moveCategory(Category $category, ?int $newParentId): Category
    {
        // Validate the move
        if ($newParentId) {
            $newParent = Category::find($newParentId);
            
            if (!$newParent) {
                throw new \Exception('Parent category not found');
            }

            // Check for circular reference
            if ($this->wouldCreateCircularReference($category, $newParentId)) {
                throw new \Exception('Cannot move category: would create circular reference');
            }

            // Check depth limit
            if ($newParent->depth() >= 10) {
                throw new \Exception('Cannot move category: would exceed maximum depth of 10');
            }
        }

        $category->parent_id = $newParentId;
        $category->save();

        return $category->fresh();
    }

    /**
     * Format category node for tree display
     */
    protected function formatCategoryNode(Category $category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'full_path' => $category->full_path,
            'description' => $category->description,
            'parent_id' => $category->parent_id,
            'status' => $category->status,
            'depth' => $category->depth(),
            'is_root' => $category->isRoot(),
            'is_leaf' => $category->isLeaf(),
            'references_count' => $category->reference_count,
            'children' => $category->children->map(function ($child) {
                return $this->formatCategoryNode($child);
            })->toArray(),
        ];
    }

    /**
     * Check if moving would create circular reference
     */
    protected function wouldCreateCircularReference(Category $category, int $newParentId): bool
    {
        $currentParent = Category::find($newParentId);
        
        while ($currentParent) {
            if ($currentParent->id === $category->id) {
                return true;
            }
            $currentParent = $currentParent->parent;
        }
        
        return false;
    }

    /**
     * Get categories by reference count (for statistics)
     */
    public function getCategoriesByReferenceCount(int $limit = 10): Collection
    {
        return Category::withCount('references')
            ->orderBy('references_count', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->full_path,
                    'references_count' => $category->references_count,
                ];
            });
    }

    /**
     * Get recent categories
     */
    public function getRecentCategories(int $limit = 5): Collection
    {
        return Category::latest()
            ->limit($limit)
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'full_path' => $category->full_path,
                    'created_at' => $category->created_at,
                ];
            });
    }
}
