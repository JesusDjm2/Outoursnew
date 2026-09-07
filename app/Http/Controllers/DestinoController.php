<?php

namespace App\Http\Controllers;

use App\Models\Destino;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class DestinoController extends Controller
{
    public function index()
    {
        $destinos = Destino::withCount('categorias')
            ->with(['categorias' => fn ($query) => $query->orderBy('nombre')])
            ->orderBy('nombre')
            ->paginate(15);
        return view('destinos.index', compact('destinos'));
    }

    public function create()
    {
        return view('destinos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:destinos,nombre',
        ]);

        Destino::create($validated);

        return redirect()->route('destinos.index')->with('success', 'Destino creado.');
    }

    public function edit(Destino $destino)
    {
        return view('destinos.edit', compact('destino'));
    }

    public function update(Request $request, Destino $destino)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:destinos,nombre,' . $destino->id,
        ]);

        $destino->update($validated);

        return redirect()->route('destinos.index')->with('success', 'Destino actualizado.');
    }

    public function destroy(Destino $destino)
    {
        try {
            $destino->delete();
        } catch (QueryException $e) {
            return redirect()->route('destinos.index')
                ->with('error', 'No se puede eliminar: hay categorías usando este destino.');
        }

        return redirect()->route('destinos.index')->with('success', 'Destino eliminado.');
    }
}
