<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tour extends Model
{
    protected $fillable = [
        'nombre',
        'codigo',
        'imagen_path',
        'pdf_path',
        'fecha_inicio',
        'fecha_fin',
        'idioma',
        'moneda',
        'precio_adicional',
        'descuento_especial',
        'notas_adicionales',
        'reserva_pct',
        'agencia_id',
        'agente',
        'nombre_pax',
        'edad_pax',
        'contacto_pax',
        'canal',
        'fecha_cotizacion',
        'pax_adultos',
        'pax_ninos',
        'pais',
        'codigo_pais',
        'departamento_estado',
        'fecha_llegada',
        'hora_llegada',
        'fecha_salida_viaje',
        'hora_salida_viaje',
    ];

    public function agencia(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agencia_id');
    }

    public function itineraries(): BelongsToMany
    {
        return $this->belongsToMany(Itinerary::class)
            ->withPivot(['fecha', 'cantidad_pax', 'orden'])
            ->orderBy('itinerary_tour.orden');
    }

    public function proveedores(): BelongsToMany
    {
        return $this->belongsToMany(Proveedor::class);
    }

    public function hospedajes(): HasMany
    {
        return $this->hasMany(Hospedaje::class);
    }

    public function passengers(): HasMany
    {
        return $this->hasMany(Passenger::class);
    }

    public function calcularResumenFactura(): array
    {
        $pvRegular = 0.0;
        $pvPromo = 0.0;

        foreach ($this->itineraries as $itinerario) {
            $cantidad = (float) ($itinerario->pivot->cantidad_pax ?? 0);
            $pvRegular += (float) ($itinerario->costo ?? 0) * $cantidad;
            $pvPromo += (float) ($itinerario->costo_promo ?? $itinerario->costo ?? 0) * $cantidad;
        }

        foreach ($this->hospedajes as $hospedaje) {
            if (!$hospedaje->fecha_ingreso || !$hospedaje->fecha_salida) {
                continue;
            }
            $noches = max(1, \Carbon\Carbon::parse($hospedaje->fecha_ingreso)->diffInDays(\Carbon\Carbon::parse($hospedaje->fecha_salida)));
            foreach ($hospedaje->rooms as $room) {
                $pvRegular += (float) ($room->precio_regular ?? 0) * $noches;
                $pvPromo += (float) ($room->precio_promo ?? $room->precio_regular ?? 0) * $noches;
            }
        }

        $totalDescuento = $pvRegular - $pvPromo;
        $pvFinal = $pvPromo + (float) ($this->precio_adicional ?? 0) - (float) ($this->descuento_especial ?? 0);
        $montoReserva = $pvFinal * ((float) ($this->reserva_pct ?? 0) / 100);

        return [
            'pv_regular' => round($pvRegular, 2),
            'pv_promo' => round($pvPromo, 2),
            'total_descuento' => round($totalDescuento, 2),
            'pv_final' => round($pvFinal, 2),
            'monto_reserva' => round($montoReserva, 2),
        ];
    }
}
