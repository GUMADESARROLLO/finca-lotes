<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoteUser extends Model
{
    protected $table = 'lote_user';

    protected $fillable = [
        'lote_id',
        'user_id',
        'rol_en_lote',
    ];

    public function lote(): BelongsTo
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
