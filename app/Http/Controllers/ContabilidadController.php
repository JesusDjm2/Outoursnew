<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ContabilidadController extends Controller
{
    private const MESES = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
        7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
    ];

    public function index(Request $request)
    {
        $anio = (int) ($request->query('anio') ?: now()->year);
        $mes = $request->query('mes') ? (int) $request->query('mes') : null;
        $agenciaId = $request->query('agencia_id');

        $aniosDisponibles = Tour::query()
            ->selectRaw('YEAR(COALESCE(fecha_cotizacion, created_at)) as anio')
            ->distinct()
            ->orderByDesc('anio')
            ->pluck('anio');
        if ($aniosDisponibles->isEmpty()) {
            $aniosDisponibles = collect([now()->year]);
        }

        $query = Tour::with(['itineraries', 'hospedajes.rooms', 'agencia'])
            ->whereRaw('YEAR(COALESCE(fecha_cotizacion, created_at)) = ?', [$anio]);

        if ($mes) {
            $query->whereRaw('MONTH(COALESCE(fecha_cotizacion, created_at)) = ?', [$mes]);
        }

        if ($agenciaId) {
            $query->where('agencia_id', $agenciaId);
        }

        $tours = $query->get()->map(function (Tour $tour) {
            $fecha = $tour->fecha_cotizacion ? Carbon::parse($tour->fecha_cotizacion) : $tour->created_at;
            $resumen = $tour->calcularResumenFactura();

            return (object) [
                'tour' => $tour,
                'fecha' => $fecha,
                'moneda' => $tour->moneda ?: 'PEN',
                'pv_final' => $resumen['pv_final'],
                'monto_reserva' => $resumen['monto_reserva'],
            ];
        })->sortByDesc(fn ($item) => $item->fecha)->values();

        $porMes = [];
        $totalesPorMoneda = [];
        foreach ($tours as $item) {
            $key = $item->fecha->month . '|' . $item->moneda;
            if (!isset($porMes[$key])) {
                $porMes[$key] = [
                    'mes' => $item->fecha->month,
                    'mes_nombre' => self::MESES[$item->fecha->month],
                    'moneda' => $item->moneda,
                    'cantidad' => 0,
                    'total' => 0.0,
                    'reservas' => 0.0,
                ];
            }
            $porMes[$key]['cantidad']++;
            $porMes[$key]['total'] += $item->pv_final;
            $porMes[$key]['reservas'] += $item->monto_reserva;

            $totalesPorMoneda[$item->moneda] = ($totalesPorMoneda[$item->moneda] ?? 0) + $item->pv_final;
        }
        $porMes = collect($porMes)->sortBy('mes')->values();

        $agencias = User::whereHas('roles', fn ($q) => $q->where('name', 'Agencia'))->orderBy('name')->get(['id', 'name']);

        return view('contabilidad.index', [
            'anio' => $anio,
            'mes' => $mes,
            'agenciaId' => $agenciaId,
            'aniosDisponibles' => $aniosDisponibles,
            'meses' => self::MESES,
            'porMes' => $porMes,
            'totalesPorMoneda' => $totalesPorMoneda,
            'agencias' => $agencias,
            'tours' => $tours,
        ]);
    }
}
