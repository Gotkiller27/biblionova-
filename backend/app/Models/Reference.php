<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Reference extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    /**
     * Configuration moderne de Spatie Activitylog
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('reference')
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $fillable = [
        'title',
        'subtitle',
        'abstract',
        'isbn',
        'doi',
        'issn',
        'publication_year',
        'language',
        'document_type',
        'category_id',
        'publisher_id',
        'uploaded_by',
        'bibliothecaire_id',
        'cover_image',
        'file_path',
        'pages',
        'download_count',
        'view_count',
        'status',
        'visibility',
        'availability',
    ];

    protected $casts = [
        'publication_year' => 'integer',
        'download_count' => 'integer',
        'view_count' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function authors()
    {
        return $this->belongsToMany(Author::class, 'reference_author');
    }

    public function keywords()
    {
        return $this->hasMany(ReferenceKeyword::class);
    }

    public function views()
    {
        return $this->hasMany(View::class);
    }

    public function downloads()
    {
        return $this->hasMany(Download::class);
    }

    public function revisions()
    {
        return $this->hasMany(DocumentRevision::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }

    public function citingCitations()
    {
        return $this->hasMany(Citation::class, 'citing_reference_id');
    }

    public function citedByCitations()
    {
        return $this->hasMany(Citation::class, 'cited_reference_id');
    }

    public function citingReferences()
    {
        return $this->belongsToMany(Reference::class, 'citations', 'cited_reference_id', 'citing_reference_id');
    }

    public function citedByReferences()
    {
        return $this->belongsToMany(Reference::class, 'citations', 'citing_reference_id', 'cited_reference_id');
    }

    public function incrementViewCount()
    {
        $this->increment('view_count');
        return $this;
    }

    public function incrementDownloadCount()
    {
        $this->increment('download_count');
        return $this;
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }

    public function scopePrivate($query)
    {
        return $query->where('visibility', 'private');
    }

    public function scopeRestricted($query)
    {
        return $query->where('visibility', 'restricted');
    }

    public function scopeAvailable($query)
    {
        return $query->where('availability', 'available');
    }

    public function scopeBorrowed($query)
    {
        return $query->where('availability', 'borrowed');
    }

    public function scopeReserved($query)
    {
        return $query->where('availability', 'reserved');
    }

    public function scopeUnavailable($query)
    {
        return $query->where('availability', 'unavailable');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('subtitle', 'like', "%{$search}%")
              ->orWhere('abstract', 'like', "%{$search}%")
              ->orWhere('isbn', 'like', "%{$search}%")
              ->orWhere('doi', 'like', "%{$search}%")
              ->orWhere('issn', 'like', "%{$search}%")
              ->orWhereHas('authors', function ($q) use ($search) {
                  $q->where('name', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%");
              })
              ->orWhereHas('keywords', function ($q) use ($search) {
                  $q->where('name', 'like', "%{$search}%");
              });
        });
    }

    public function scopeAdvancedSearch($query, $filters)
    {
        foreach ($filters as $key => $value) {
            if ($value === null || $value === '') continue;

            switch ($key) {
                case 'title':
                    $query->where('title', 'like', "%{$value}%");
                    break;
                case 'author':
                    $query->whereHas('authors', function ($q) use ($value) {
                        $q->where('name', 'like', "%{$value}%");
                    });
                    break;
                case 'category_id':
                    $query->where('category_id', $value);
                    break;
                case 'document_type':
                    $query->where('document_type', $value);
                    break;
                case 'language':
                    $query->where('language', $value);
                    break;
                case 'publication_year_from':
                    $query->where('publication_year', '>=', $value);
                    break;
                case 'publication_year_to':
                    $query->where('publication_year', '<=', $value);
                    break;
                case 'publisher_id':
                    $query->where('publisher_id', $value);
                    break;
                case 'isbn':
                    $query->where('isbn', 'like', "%{$value}%");
                    break;
                case 'doi':
                    $query->where('doi', 'like', "%{$value}%");
                    break;
                case 'issn':
                    $query->where('issn', 'like', "%{$value}%");
                    break;
                case 'visibility':
                    $query->where('visibility', $value);
                    break;
                case 'availability':
                    $query->where('availability', $value);
                    break;
                case 'keyword':
                    $query->whereHas('keywords', function ($q) use ($value) {
                        $q->where('name', 'like', "%{$value}%");
                    });
                    break;
            }
        }

        return $query;
    }

    public function scopeMultiCriteriaFilter($query, $criteria)
    {
        return $query->advancedSearch($criteria);
    }

    public function isFavoritedBy($userId)
    {
        return $this->favorites()->where('user_id', $userId)->exists();
    }

    public function getCitationCountAttribute()
    {
        return $this->citedByCitations()->count();
    }

    public function getFavoriteCountAttribute()
    {
        return $this->favorites()->count();
    }
}
