<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class DepositRequestReview extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    /**
     * Configuration moderne de Spatie Activitylog
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('deposit_request_review')
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $fillable = [
        'deposit_request_id',
        'reviewer_id',
        'reviewer_role',
        'decision',
        'justification',
    ];

    public function depositRequest()
    {
        return $this->belongsTo(DepositRequest::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
