<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    protected $fillable = [
        'hotel_id',
        'nombre',
        'cantidad_personas',
        'numero_habitacion',
        'precio_regular',
        'precio_promo',
    ];

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function asignaciones(): HasMany
    {
        return $this->hasMany(HospedajePasajero::class);
    }

    public function hospedajes(): BelongsToMany
    {
        return $this->belongsToMany(Hospedaje::class, 'hospedaje_room');
    }
}
