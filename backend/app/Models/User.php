<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions; // <-- AJOUT de l'import pour les options
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes, LogsActivity;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'avatar',
        'password',
        'status',
        'email_verified_at',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Configuration moderne de Spatie Activitylog (Remplace les anciennes propriétés statiques)
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('user')         // Remplace protected static $logName = 'user';
            ->logFillable()              // Remplace protected static $logFillable = true;
            ->logOnlyDirty()             // Remplace protected static $logOnlyDirty = true;
            ->dontSubmitEmptyLogs();
    }

    /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function depositRequests()
    {
        return $this->hasMany(DepositRequest::class, 'applicant_id');
    }

    public function assignedRequests()
    {
        return $this->hasMany(DepositRequest::class, 'assigned_manager_id');
    }

    public function reviews()
    {
        return $this->hasMany(DepositRequestReview::class, 'reviewer_id');
    }

    public function uploadedReferences()
    {
        return $this->hasMany(Reference::class, 'uploaded_by');
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/avatars/' . $this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->first_name . ' ' . $this->last_name) . '&background=random';
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}