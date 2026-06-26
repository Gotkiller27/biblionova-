<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferenceKeyword extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'reference_id',
        'keyword',
    ];

    public function reference()
    {
        return $this->belongsTo(Reference::class);
    }
}
