<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hotel extends Model
{
    protected $fillable = [
        'destino_id',
        'codigo',
        'nombre',
        'direccion',
        'telefono',
        'email',
        'imagen_path',
    ];

    public function destino(): BelongsTo
    {
        return $this->belongsTo(Destino::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function hospedajes(): HasMany
    {
        return $this->hasMany(Hospedaje::class);
    }
}
