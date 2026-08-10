<?php

namespace App\Http\Controllers;

use App\Models\Itinerary;
use App\Models\ItineraryPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ItineraryPackageController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');

        $packages = ItineraryPackage::query()
            ->withCount('itineraries')
            ->when($q, fn($query) => $query->where('nombre', 'like', "%{$q}%"))
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view('itinerary-packages.index', compact('packages', 'q'));
    }

    public function create()
    {
        $itemsSeleccionados = $this->buildItemsSeleccionadosFromOld();
        return view('itinerary-packages.create', compact('itemsSeleccionados'));
    }

    public function store(Request $request)
    {
        $validated = $this->validatePackage($request);

        $package = ItineraryPackage::create([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'] ?? null,
            'dias' => $validated['dias'],
        ]);

        $this->syncItems($package, $validated);

        return redirect()->route('itinerary-packages.index')->with('success', 'Paquete de itinerarios creado.');
    }

    public function edit(ItineraryPackage $itineraryPackage)
    {
        $itineraryPackage->load('itineraries');
        $itemsSeleccionados = old('itinerarios') !== null
            ? $this->buildItemsSeleccionadosFromOld()
            : $itineraryPackage->itineraries;

        return view('itinerary-packages.edit', ['package' => $itineraryPackage, 'itemsSeleccionados' => $itemsSeleccionados]);
    }

    public function update(Request $request, ItineraryPackage $itineraryPackage)
    {
        $validated = $this->validatePackage($request);

        $itineraryPackage->update([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'] ?? null,
            'dias' => $validated['dias'],
        ]);

        $this->syncItems($itineraryPackage, $validated);

        return redirect()->route('itinerary-packages.index')->with('success', 'Paquete de itinerarios actualizado.');
    }

    public function destroy(ItineraryPackage $itineraryPackage)
    {
        $itineraryPackage->delete();

        return redirect()->route('itinerary-packages.index')->with('success', 'Paquete de itinerarios eliminado.');
    }

    public function items(ItineraryPackage $itineraryPackage)
    {
        $items = $itineraryPackage->itineraries->map(fn($itinerario) => [
            'id' => $itinerario->id,
            'nombre' => $itinerario->nombre,
            'costo' => $itinerario->costo,
            'costo_promo' => $itinerario->costo_promo,
            'dia' => $itinerario->pivot->dia,
            'cantidad_pax_defecto' => $itinerario->pivot->cantidad_pax_defecto,
        ])->values();

        return response()->json($items);
    }

    private function validatePackage(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'dias' => 'required|integer|min:1|max:60',

            'itinerarios' => 'nullable|array',
            'itinerarios.*' => 'nullable|integer|exists:itineraries,id',
            'itinerarios_dia' => 'nullable|array',
            'itinerarios_dia.*' => 'nullable|integer|min:1',
            'itinerarios_cantidad' => 'nullable|array',
            'itinerarios_cantidad.*' => 'nullable|integer|min:1',
        ]);

        $validator->after(function ($validator) use ($request) {
            $itinerarios = $request->input('itinerarios', []);
            $dias = $request->input('itinerarios_dia', []);
            $cantidades = $request->input('itinerarios_cantidad', []);
            if (count($itinerarios) !== count($dias) || count($itinerarios) !== count($cantidades)) {
                $validator->errors()->add('itinerarios', 'El día y la cantidad de pax deben indicarse para cada itinerario agregado.');
            }

            $diasMax = (int) $request->input('dias', 1);
            foreach ($dias as $i => $dia) {
                if ($dia && (int) $dia > $diasMax) {
                    $validator->errors()->add("itinerarios_dia.$i", "El día no puede superar la cantidad total de días del paquete ($diasMax).");
                }
            }
        });

        return $validator->validate();
    }

    private function syncItems(ItineraryPackage $package, array $validated): void
    {
        $ids = $validated['itinerarios'] ?? [];
        $dias = $validated['itinerarios_dia'] ?? [];
        $cantidades = $validated['itinerarios_cantidad'] ?? [];

        $pivotData = [];
        foreach ($ids as $i => $itinerarioId) {
            $pivotData[$itinerarioId] = [
                'dia' => $dias[$i] ?? 1,
                'cantidad_pax_defecto' => $cantidades[$i] ?? null,
                'orden' => $i,
            ];
        }

        $package->itineraries()->sync($pivotData);
    }

    private function buildItemsSeleccionadosFromOld()
    {
        $ids = old('itinerarios', []);
        $dias = old('itinerarios_dia', []);
        $cantidades = old('itinerarios_cantidad', []);
        $itinerariosById = Itinerary::whereIn('id', $ids)->get()->keyBy('id');

        return collect($ids)->map(function ($id, $i) use ($itinerariosById, $dias, $cantidades) {
            $itinerario = $itinerariosById->get($id);
            if (!$itinerario) {
                return null;
            }
            $itinerario->pivot = (object) [
                'dia' => $dias[$i] ?? null,
                'cantidad_pax_defecto' => $cantidades[$i] ?? null,
            ];
            return $itinerario;
        })->filter()->values();
    }
}
