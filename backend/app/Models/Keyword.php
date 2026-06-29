<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Str;

class Keyword extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'usage_count',
        'popularity_score',
    ];

    protected $casts = [
        'usage_count' => 'integer',
        'popularity_score' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($keyword) {
            if (empty($keyword->slug)) {
                $keyword->slug = Str::slug($keyword->name);
            }
        });

        static::updating(function ($keyword) {
            if (empty($keyword->slug)) {
                $keyword->slug = Str::slug($keyword->name);
            }
        });
    }

    /**
     * Configuration moderne de Spatie Activitylog
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('keyword')
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function references()
    {
        return $this->belongsToMany(Reference::class, 'keyword_reference');
    }

    public function referencesCount()
    {
        return $this->references()->count();
    }

    public function incrementUsage()
    {
        $this->increment('usage_count');
        $this->increment('popularity_score');
    }

    public function decrementUsage()
    {
        $this->decrement('usage_count');
        $this->decrement('popularity_score');
    }

    // Scopes
    public function scopeSearch($query, $searchTerm)
    {
        return $query->where('name', 'like', "%{$searchTerm}%")
            ->orWhere('description', 'like', "%{$searchTerm}%");
    }

    public function scopePopular($query)
    {
        return $query->orderBy('popularity_score', 'desc');
    }

    public function scopeMostUsed($query)
    {
        return $query->orderBy('usage_count', 'desc');
    }

    public function scopeTrending($query)
    {
        return $query->where('updated_at', '>=', now()->subDays(7))
            ->orderBy('popularity_score', 'desc');
    }

    public function scopeWithReferencesCount($query)
    {
        return $query->withCount('references');
    }
}
