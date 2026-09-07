<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hospedaje extends Model
{
    protected $table = 'hospedajes';

    protected $fillable = [
        'tour_id',
        'hotel_id',
        'fecha_ingreso',
        'fecha_salida',
        'estado_reserva',
    ];

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function asignaciones(): HasMany
    {
        return $this->hasMany(HospedajePasajero::class);
    }

    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(Room::class, 'hospedaje_room');
    }
}
