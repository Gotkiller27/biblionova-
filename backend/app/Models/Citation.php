<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Citation extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    /**
     * Configuration moderne de Spatie Activitylog
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('citation')
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $fillable = [
        'citing_reference_id',
        'cited_reference_id',
        'context',
        'citation_style',
        'page_number',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'page_number' => 'integer',
    ];

    public function citingReference()
    {
        return $this->belongsTo(Reference::class, 'citing_reference_id');
    }

    public function citedReference()
    {
        return $this->belongsTo(Reference::class, 'cited_reference_id');
    }

    // Scopes
    public function scopeForReference($query, $referenceId)
    {
        return $query->where('citing_reference_id', $referenceId)
                     ->orWhere('cited_reference_id', $referenceId);
    }

    public function scopeCiting($query, $referenceId)
    {
        return $query->where('citing_reference_id', $referenceId);
    }

    public function scopeCitedBy($query, $referenceId)
    {
        return $query->where('cited_reference_id', $referenceId);
    }

    public function scopeByStyle($query, $style)
    {
        return $query->where('citation_style', $style);
    }
}
