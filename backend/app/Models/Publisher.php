<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Publisher extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'name',
        'description',
        'logo',
        'country',
        'city',
        'website',
        'email',
        'phone',
    ];

    /**
     * Configuration moderne de Spatie Activitylog
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('publisher')
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }
        return asset('images/default-publisher.png');
    }

    public function getFullAddressAttribute()
    {
        $address = [];
        if ($this->city) {
            $address[] = $this->city;
        }
        if ($this->country) {
            $address[] = $this->country;
        }
        return implode(', ', $address) ?: 'Non spécifiée';
    }

    public function references()
    {
        return $this->hasMany(Reference::class);
    }

    public function referencesCount()
    {
        return $this->references()->count();
    }

    // Scopes
    public function scopeSearch($query, $searchTerm)
    {
        return $query->where('name', 'like', "%{$searchTerm}%")
            ->orWhere('description', 'like', "%{$searchTerm}%")
            ->orWhere('country', 'like', "%{$searchTerm}%")
            ->orWhere('city', 'like', "%{$searchTerm}%");
    }

    public function scopeByCountry($query, $country)
    {
        return $query->where('country', $country);
    }

    public function scopeByCity($query, $city)
    {
        return $query->where('city', $city);
    }

    public function scopeWithReferencesCount($query)
    {
        return $query->withCount('references');
    }
}
