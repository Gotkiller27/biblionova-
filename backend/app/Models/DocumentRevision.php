<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentRevision extends Model
{
    use HasFactory;

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
