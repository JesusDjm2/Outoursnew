<?php

namespace App\Http\Controllers;

use App\Models\TipoProveedor;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class TipoProveedorController extends Controller
{
    public function index()
    {
        $tipos = TipoProveedor::withCount('proveedores')->orderBy('nombre')->get();
        return view('tipos_proveedor.index', compact('tipos'));
    }

    public function create()
    {
        return view('tipos_proveedor.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:tipo_proveedores,nombre',
        ]);

        TipoProveedor::create($validated);

        return redirect()->route('tipos-proveedor.index')->with('success', 'Tipo de proveedor creado.');
    }

    public function edit(TipoProveedor $tipo)
    {
        return view('tipos_proveedor.edit', compact('tipo'));
    }

    public function update(Request $request, TipoProveedor $tipo)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:tipo_proveedores,nombre,' . $tipo->id,
        ]);

        $tipo->update($validated);

        return redirect()->route('tipos-proveedor.index')->with('success', 'Tipo de proveedor actualizado.');
    }

    public function destroy(TipoProveedor $tipo)
    {
        try {
            $tipo->delete();
        } catch (QueryException $e) {
            return redirect()->route('tipos-proveedor.index')
                ->with('error', 'No se puede eliminar: hay proveedores usando este tipo.');
        }

        return redirect()->route('tipos-proveedor.index')->with('success', 'Tipo de proveedor eliminado.');
    }
}
