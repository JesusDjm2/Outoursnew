<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Subcategoria;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class SubcategoriaController extends Controller
{
    public function index()
    {
        $subcategorias = Subcategoria::withCount('itineraries')->with('categoria.destino')->orderBy('nombre')->get();
        $categorias = Categoria::with('destino')->orderBy('nombre')->get();
        return view('subcategorias.index', compact('subcategorias', 'categorias'));
    }

    public function create()
    {
        $categorias = Categoria::with('destino')->orderBy('nombre')->get();
        return view('subcategorias.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nombre' => 'required|string|max:255',
        ]);
        $validated['nombre'] = trim($validated['nombre']);

        $exists = Subcategoria::where('categoria_id', $validated['categoria_id'])
            ->where('nombre', $validated['nombre'])
            ->exists();
        if ($exists) {
            return back()->withInput()->withErrors(['nombre' => 'Ya existe una subcategoría con ese nombre en esta categoría.']);
        }

        Subcategoria::create($validated);

        return redirect()->route('subcategorias.index')->with('success', 'Subcategoría creada.');
    }

    public function edit(Subcategoria $subcategoria)
    {
        $categorias = Categoria::with('destino')->orderBy('nombre')->get();
        return view('subcategorias.edit', compact('subcategoria', 'categorias'));
    }

    public function update(Request $request, Subcategoria $subcategoria)
    {
        $validated = $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nombre' => 'required|string|max:255',
        ]);
        $validated['nombre'] = trim($validated['nombre']);

        $exists = Subcategoria::where('categoria_id', $validated['categoria_id'])
            ->where('nombre', $validated['nombre'])
            ->where('id', '!=', $subcategoria->id)
            ->exists();
        if ($exists) {
            return back()->withInput()->withErrors(['nombre' => 'Ya existe una subcategoría con ese nombre en esta categoría.']);
        }

        $subcategoria->update($validated);

        return redirect()->route('subcategorias.index')->with('success', 'Subcategoría actualizada.');
    }

    public function destroy(Subcategoria $subcategoria)
    {
        try {
            $subcategoria->delete();
        } catch (QueryException $e) {
            return redirect()->route('subcategorias.index')
                ->with('error', 'No se puede eliminar: hay itinerarios usando esta subcategoría.');
        }

        return redirect()->route('subcategorias.index')->with('success', 'Subcategoría eliminada.');
    }
}
