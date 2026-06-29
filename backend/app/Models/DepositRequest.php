<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class DepositRequest extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    /**
     * Configuration moderne de Spatie Activitylog
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('deposit_request')
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $fillable = [
        'applicant_id',
        'assigned_manager_id',
        'title',
        'description',
        'proposed_file',
        'status',
        'submission_status',
        'submitted_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    public function applicant()
    {
        return $this->belongsTo(User::class, 'applicant_id');
    }

    public function assignedManager()
    {
        return $this->belongsTo(User::class, 'assigned_manager_id');
    }

    public function reviews()
    {
        return $this->hasMany(DepositRequestReview::class);
    }

    public function attachments()
    {
        return $this->hasMany(DepositRequestAttachment::class);
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('submission_status', 'draft');
    }

    public function scopeSubmitted($query)
    {
        return $query->where('submission_status', 'submitted');
    }

    public function scopeCancelled($query)
    {
        return $query->whereNotNull('cancelled_at');
    }

    public function scopeNotCancelled($query)
    {
        return $query->whereNull('cancelled_at');
    }

    // Helper methods
    public function submit()
    {
        $this->update([
            'submission_status' => 'submitted',
            'submitted_at' => now(),
        ]);
    }

    public function cancel($reason = null)
    {
        $this->update([
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);
    }

    public function isDraft()
    {
        return $this->submission_status === 'draft';
    }

    public function isSubmitted()
    {
        return $this->submission_status === 'submitted';
    }

    public function isCancelled()
    {
        return $this->cancelled_at !== null;
    }
}
