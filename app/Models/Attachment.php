<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attachment extends Model
{
    protected $fillable = [
        'user_id',
        'file_path',
        'file_name',
        'mime_type',
        'model_id',
        'model_type'
    ];

    // Relación inversa polimórfica (para saber si es de una Cita o un Ejercicio)
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    // Relación con el usuario
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
