<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ItineraryPackage extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'dias',
    ];

    public function itineraries(): BelongsToMany
    {
        return $this->belongsToMany(Itinerary::class, 'itinerary_package_items')
            ->withPivot(['dia', 'cantidad_pax_defecto', 'orden'])
            ->orderBy('itinerary_package_items.dia')
            ->orderBy('itinerary_package_items.orden');
    }
}
