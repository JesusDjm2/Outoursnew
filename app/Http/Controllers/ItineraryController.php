<?php

namespace App\Http\Controllers;

use App\Models\Destino;
use App\Models\Itinerary;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ItineraryController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');

        $itineraries = Itinerary::query()
            ->with('destino', 'categoria')
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('nombre', 'like', "%{$q}%")
                        ->orWhere('codigo', 'like', "%{$q}%")
                        ->orWhereHas('destino', fn ($d) => $d->where('nombre', 'like', "%{$q}%"))
                        ->orWhereHas('categoria', fn ($c) => $c->where('nombre', 'like', "%{$q}%"));
                });
            })
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view('itineraries.index', compact('itineraries', 'q'));
    }

    public function create()
    {
        $destinos = Destino::with('categorias')->orderBy('nombre')->get();
        return view('itineraries.create', compact('destinos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->itineraryRules($request));

        Itinerary::create($validated);

        return redirect()->route('itineraries.index')->with('success', 'Itinerario creado.');
    }

    public function edit(Itinerary $itinerary)
    {
        $destinos = Destino::with('categorias')->orderBy('nombre')->get();
        return view('itineraries.edit', compact('itinerary', 'destinos'));
    }

    public function update(Request $request, Itinerary $itinerary)
    {
        $validated = $request->validate($this->itineraryRules($request));

        $itinerary->update($validated);

        return redirect()->route('itineraries.index')->with('success', 'Itinerario actualizado.');
    }

    public function destroy(Itinerary $itinerary)
    {
        $itinerary->delete();

        return redirect()->route('itineraries.index')->with('success', 'Itinerario eliminado.');
    }

    public function search(Request $request)
    {
        $q = $request->query('q');
        $categoriaId = $request->query('categoria_id');
        $destinoId = $request->query('destino_id');

        $itineraries = Itinerary::query()
            ->when($q, fn($query) => $query->where('nombre', 'like', "%{$q}%"))
            ->when($categoriaId, fn($query) => $query->where('categoria_id', $categoriaId))
            ->when($destinoId, fn($query) => $query->where('destino_id', $destinoId))
            ->orderBy('nombre')
            ->limit(50)
            ->get(['id', 'nombre', 'codigo', 'costo', 'costo_promo', 'costo_nino', 'costo_promo_nino']);

        return response()->json($itineraries);
    }

    private function itineraryRules(Request $request): array
    {
        return [
            'destino_id' => 'required|exists:destinos,id',
            'categoria_id' => [
                'nullable',
                Rule::exists('categorias', 'id')->where('destino_id', $request->input('destino_id')),
            ],
            'nombre' => 'required|string|max:255',
            'codigo' => 'nullable|string|max:20',
            'costo' => 'nullable|numeric|min:0',
            'costo_promo' => 'nullable|numeric|min:0',
            'costo_nino' => 'nullable|numeric|min:0',
            'costo_promo_nino' => 'nullable|numeric|min:0',
            'descripcion' => 'nullable|string',
            'incluye' => 'nullable|string',
            'no_incluye' => 'nullable|string',
        ];
    }
}
