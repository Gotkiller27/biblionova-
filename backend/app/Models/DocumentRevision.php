<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class DocumentRevision extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    /**
     * Configuration moderne de Spatie Activitylog
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('document_revision')
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $fillable = [
        'reference_id',
        'bibliothecaire_id',
        'action',
        'commentaire',
    ];

    public function reference()
    {
        return $this->belongsTo(Reference::class);
    }

    public function bibliothecaire()
    {
        return $this->belongsTo(User::class, 'bibliothecaire_id');
    }
}
