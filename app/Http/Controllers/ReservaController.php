<?php

namespace App\Http\Controllers;

use App\Models\Hospedaje;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ReservaController extends Controller
{
    private const ESTADOS = ['pendiente', 'confirmada', 'cancelada', 'reservado_pasajero'];

    public function index(Request $request)
    {
        $q = $request->query('q');
        $estado = $request->query('estado');

        $query = Tour::query()
            ->with(['hospedajes.hotel', 'proveedores.tipo'])
            ->where(function ($outer) {
                $outer->whereHas('hospedajes')->orWhereHas('proveedores');
            })
            ->latest();

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('codigo', 'like', "%{$q}%")
                    ->orWhere('nombre', 'like', "%{$q}%")
                    ->orWhere('nombre_pax', 'like', "%{$q}%");
            });
        }

        if ($estado && in_array($estado, self::ESTADOS, true)) {
            $query->where(function ($sub) use ($estado) {
                $sub->whereHas('hospedajes', fn ($h) => $h->where('estado_reserva', $estado))
                    ->orWhereHas('proveedores', fn ($p) => $p->wherePivot('estado_reserva', $estado));
            });
        }

        $tours = $query->paginate(15)->withQueryString();

        $resumen = [
            'pendiente' => Hospedaje::where('estado_reserva', 'pendiente')->count()
                + DB::table('proveedor_tour')->where('estado_reserva', 'pendiente')->count(),
            'confirmada' => Hospedaje::where('estado_reserva', 'confirmada')->count()
                + DB::table('proveedor_tour')->where('estado_reserva', 'confirmada')->count(),
            'cancelada' => Hospedaje::where('estado_reserva', 'cancelada')->count()
                + DB::table('proveedor_tour')->where('estado_reserva', 'cancelada')->count(),
        ];

        return view('reservas.index', compact('tours', 'q', 'estado', 'resumen'));
    }

    public function show(Tour $tour)
    {
        $tour->load(['hospedajes.hotel', 'proveedores.tipo']);
        return view('reservas.show', compact('tour'));
    }

    public function updateHospedaje(Request $request, Hospedaje $hospedaje)
    {
        $validated = $request->validate([
            'estado_reserva' => ['required', Rule::in(self::ESTADOS)],
        ]);

        $hospedaje->update($validated);

        return back()->with('success', 'Estado de la reserva de hospedaje actualizado.');
    }

    public function updateProveedor(Request $request, Tour $tour, $proveedor)
    {
        $validated = $request->validate([
            'estado_reserva' => ['required', Rule::in(self::ESTADOS)],
        ]);

        $tour->proveedores()->updateExistingPivot($proveedor, $validated);

        return back()->with('success', 'Estado de la reserva del proveedor actualizado.');
    }
}
