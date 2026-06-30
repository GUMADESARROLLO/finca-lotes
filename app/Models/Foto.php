<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Foto extends Model
{
    protected $fillable = [
        'fotoable_type',
        'fotoable_id',
        'ruta',
        'mime',
        'size',
        'taken_at',
        'lat',
        'lng',
        'uploaded_by',
    ];

    public function fotoable(): MorphTo
    {
        return $this->morphTo();
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
