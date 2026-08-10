<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Destino;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = Categoria::withCount('subcategorias')->with('destino')->orderBy('nombre')->get();
        $destinos = Destino::orderBy('nombre')->get();
        return view('categorias.index', compact('categorias', 'destinos'));
    }

    public function create()
    {
        $destinos = Destino::orderBy('nombre')->get();
        return view('categorias.create', compact('destinos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'destino_id' => 'required|exists:destinos,id',
            'nombre' => 'required|string|max:255',
        ]);
        $validated['nombre'] = trim($validated['nombre']);

        $exists = Categoria::where('destino_id', $validated['destino_id'])
            ->where('nombre', $validated['nombre'])
            ->exists();
        if ($exists) {
            return back()->withInput()->withErrors(['nombre' => 'Ya existe una categoría con ese nombre en este destino.']);
        }

        Categoria::create($validated);

        return redirect()->route('categorias.index')->with('success', 'Categoría creada.');
    }

    public function edit(Categoria $categoria)
    {
        $destinos = Destino::orderBy('nombre')->get();
        return view('categorias.edit', compact('categoria', 'destinos'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $validated = $request->validate([
            'destino_id' => 'required|exists:destinos,id',
            'nombre' => 'required|string|max:255',
        ]);
        $validated['nombre'] = trim($validated['nombre']);

        $exists = Categoria::where('destino_id', $validated['destino_id'])
            ->where('nombre', $validated['nombre'])
            ->where('id', '!=', $categoria->id)
            ->exists();
        if ($exists) {
            return back()->withInput()->withErrors(['nombre' => 'Ya existe una categoría con ese nombre en este destino.']);
        }

        $categoria->update($validated);

        return redirect()->route('categorias.index')->with('success', 'Categoría actualizada.');
    }

    public function destroy(Categoria $categoria)
    {
        try {
            $categoria->delete();
        } catch (QueryException $e) {
            return redirect()->route('categorias.index')
                ->with('error', 'No se puede eliminar: hay subcategorías usando esta categoría.');
        }

        return redirect()->route('categorias.index')->with('success', 'Categoría eliminada.');
    }
}
