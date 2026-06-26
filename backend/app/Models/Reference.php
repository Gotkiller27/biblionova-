<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reference extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'abstract',
        'isbn',
        'publication_year',
        'language',
        'document_type',
        'category_id',
        'publisher_id',
        'uploaded_by',
        'cover_image',
        'file_path',
        'pages',
        'download_count',
        'view_count',
        'status',
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
}
