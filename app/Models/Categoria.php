<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Categoria extends Model
{
    protected $fillable = [
        'destino_id',
        'nombre',
    ];

    public function destino(): BelongsTo
    {
        return $this->belongsTo(Destino::class);
    }
}
