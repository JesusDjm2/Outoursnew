<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Itinerary extends Model
{
    protected $fillable = [
        'destino_id',
        'categoria_id',
        'nombre',
        'codigo',
        'costo',
        'costo_promo',
        'costo_nino',
        'costo_promo_nino',
        'descripcion',
        'incluye',
        'no_incluye',
    ];

    public function tours(): BelongsToMany
    {
        return $this->belongsToMany(Tour::class);
    }

    public function destino(): BelongsTo
    {
        return $this->belongsTo(Destino::class);
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }
}
