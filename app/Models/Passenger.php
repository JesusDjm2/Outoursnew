<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Passenger extends Model
{
    protected $fillable = [
        'tour_id',
        'nombre',
        'fecha_nacimiento',
        'edad',
        'correo',
        'imagen_path',
        'pdf_path',
    ];

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    public function asignaciones(): HasMany
    {
        return $this->hasMany(HospedajePasajero::class);
    }
}
