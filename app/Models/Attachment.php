<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Attachment extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Relación inversa polimórfica
    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
