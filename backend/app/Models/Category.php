<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'status',
    ];

    /**
     * Configuration moderne de Spatie Activitylog
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('category')
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = static::generateUniqueSlug($category->name, $category->parent_id);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = static::generateUniqueSlug($category->name, $category->parent_id);
            }
        });
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('name');
    }

    public function recursiveChildren()
    {
        return $this->children()->with('recursiveChildren');
    }

    public function references()
    {
        return $this->hasMany(Reference::class);
    }

    public function allReferences()
    {
        return $this->hasMany(Reference::class)->orWhereIn('category_id', $this->descendants()->pluck('id'));
    }

    // Hierarchy methods
    public function ancestors()
    {
        $ancestors = collect();
        $parent = $this->parent;

        while ($parent) {
            $ancestors->push($parent);
            $parent = $parent->parent;
        }

        return $ancestors;
    }

    public function descendants()
    {
        return $this->recursiveChildren()->get()->flatMap(function ($category) {
            return $category->descendants()->push($category);
        });
    }

    public function depth()
    {
        return $this->ancestors()->count();
    }

    public function isRoot()
    {
        return is_null($this->parent_id);
    }

    public function isLeaf()
    {
        return $this->children()->count() === 0;
    }

    public function getFullPathAttribute()
    {
        $path = collect([$this->name]);
        $parent = $this->parent;

        while ($parent) {
            $path->prepend($parent->name);
            $parent = $parent->parent;
        }

        return $path->join(' > ');
    }

    public function getFullSlugPathAttribute()
    {
        $path = collect([$this->slug]);
        $parent = $this->parent;

        while ($parent) {
            $path->prepend($parent->slug);
            $parent = $parent->parent;
        }

        return $path->join('/');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeWithChildren($query)
    {
        return $query->with('children');
    }

    public function scopeWithRecursiveChildren($query)
    {
        return $query->with('recursiveChildren');
    }

    public function scopeWithAncestors($query)
    {
        return $query->with('parent.parent');
    }

    // Helper methods
    protected static function generateUniqueSlug($name, $parentId = null)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)
            ->where('id', '!=', request()->route('category')?->id ?? null)
            ->where('parent_id', $parentId)
            ->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        return $slug;
    }

    public function getReferenceCountAttribute()
    {
        return $this->references()->count() + $this->descendants()->sum(function ($descendant) {
            return $descendant->references()->count();
        });
    }
}