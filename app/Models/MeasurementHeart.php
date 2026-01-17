<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeasurementHeart extends Model
{
    use HasFactory;

    protected $guarded = []; // Permite asignación masiva (útil para el MVP)

    protected $casts = [
        'date' => 'datetime', // Laravel convertirá esto a objeto Carbon automáticamente
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
