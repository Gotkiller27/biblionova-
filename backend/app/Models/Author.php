<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Author extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'first_name',
        'last_name',
        'biography',
        'photo',
        'nationality',
        'birth_date',
        'death_date',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'death_date' => 'date',
    ];

    /**
     * Configuration moderne de Spatie Activitylog
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('author')
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getPhotoUrlAttribute()
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        return asset('images/default-author.png');
    }

    public function getAgeAttribute()
    {
        if ($this->death_date) {
            return $this->birth_date->diffInYears($this->death_date);
        }
        if ($this->birth_date) {
            return $this->birth_date->diffInYears(now());
        }
        return null;
    }

    public function getIsDeceasedAttribute()
    {
        return !is_null($this->death_date);
    }

    public function references()
    {
        return $this->belongsToMany(Reference::class, 'reference_author');
    }

    public function coAuthors()
    {
        return $this->belongsToMany(Author::class, 'author_co_author', 'author_id', 'co_author_id');
    }

    public function referencesCount()
    {
        return $this->references()->count();
    }

    // Scopes
    public function scopeSearch($query, $searchTerm)
    {
        return $query->where('first_name', 'like', "%{$searchTerm}%")
            ->orWhere('last_name', 'like', "%{$searchTerm}%")
            ->orWhere('biography', 'like', "%{$searchTerm}%");
    }

    public function scopeByNationality($query, $nationality)
    {
        return $query->where('nationality', $nationality);
    }

    public function scopeAlive($query)
    {
        return $query->whereNull('death_date');
    }

    public function scopeDeceased($query)
    {
        return $query->whereNotNull('death_date');
    }

    public function scopeWithReferencesCount($query)
    {
        return $query->withCount('references');
    }
}
