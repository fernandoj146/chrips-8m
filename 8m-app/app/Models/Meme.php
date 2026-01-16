<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Meme extends Model
{
    protected $fillable = [
        'meme_url',
        'explicacion',
        'fecha_subida',
        'user_id',
    ];

    protected $casts = [
        'fecha_subida' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
