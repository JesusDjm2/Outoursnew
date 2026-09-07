<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use App\Models\ProveedorImagen;
use App\Models\TipoProveedor;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');

        $proveedorFilter = function ($query) use ($q) {
            $query->where('nombre', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%")
                ->orWhere('telefono', 'like', "%{$q}%")
                ->orWhere('direccion', 'like', "%{$q}%");
        };

        $tiposProveedor = TipoProveedor::withCount('proveedores')
            ->with(['proveedores' => function ($query) use ($q, $proveedorFilter) {
                $query->with('imagenes')->orderBy('nombre');
                if ($q) {
                    $query->where($proveedorFilter);
                }
            }])
            ->when($q, fn ($query) => $query->whereHas('proveedores', $proveedorFilter))
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        $proveedoresSinTipo = Proveedor::whereNull('tipo_id')
            ->with('imagenes')
            ->when($q, fn ($query) => $query->where($proveedorFilter))
            ->orderBy('nombre')
            ->get();

        return view('proveedores.index', compact('tiposProveedor', 'proveedoresSinTipo', 'q'));
    }

    public function create()
    {
        $tiposProveedor = TipoProveedor::orderBy('nombre')->get();
        return view('proveedores.create', compact('tiposProveedor'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo_id' => 'required|exists:tipo_proveedores,id',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'galeria' => 'nullable|array',
            'galeria.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $data = $validated;
        unset($data['galeria']);

        $proveedor = Proveedor::create($data);

        if ($request->hasFile('galeria')) {
            foreach ($request->file('galeria') as $orden => $file) {
                $path = $file->store("proveedores/{$proveedor->id}", 'public');
                ProveedorImagen::create([
                    'proveedor_id' => $proveedor->id,
                    'path' => $path,
                    'orden' => $orden,
                ]);
            }
        }

        return redirect()->route('proveedores.index')->with('success', 'Proveedor creado exitosamente.');
    }

    public function edit(Proveedor $proveedor)
    {
        $proveedor->load('imagenes');
        $tiposProveedor = TipoProveedor::orderBy('nombre')->get();
        return view('proveedores.edit', compact('proveedor', 'tiposProveedor'));
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo_id' => 'required|exists:tipo_proveedores,id',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'galeria' => 'nullable|array',
            'galeria.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'eliminar_imagenes' => 'nullable|array',
            'eliminar_imagenes.*' => 'integer|exists:proveedor_imagenes,id',
        ]);

        $data = $validated;
        unset($data['galeria'], $data['eliminar_imagenes']);

        $proveedor->update($data);

        if ($request->filled('eliminar_imagenes')) {
            $imagenes = $proveedor->imagenes()->whereIn('id', $request->input('eliminar_imagenes'))->get();
            foreach ($imagenes as $imagen) {
                Storage::disk('public')->delete($imagen->path);
                $imagen->delete();
            }
        }

        if ($request->hasFile('galeria')) {
            $siguienteOrden = (int) $proveedor->imagenes()->max('orden') + 1;
            foreach ($request->file('galeria') as $i => $file) {
                $path = $file->store("proveedores/{$proveedor->id}", 'public');
                ProveedorImagen::create([
                    'proveedor_id' => $proveedor->id,
                    'path' => $path,
                    'orden' => $siguienteOrden + $i,
                ]);
            }
        }

        return redirect()->route('proveedores.index')->with('success', 'Proveedor actualizado.');
    }

    public function destroy(Proveedor $proveedor)
    {
        try {
            $proveedor->delete();
        } catch (QueryException $e) {
            return redirect()->route('proveedores.index')
                ->with('error', 'No se puede eliminar: hay tours usando este proveedor.');
        }

        foreach ($proveedor->imagenes as $imagen) {
            Storage::disk('public')->delete($imagen->path);
        }
        Storage::disk('public')->deleteDirectory("proveedores/{$proveedor->id}");

        return redirect()->route('proveedores.index')->with('success', 'Proveedor eliminado.');
    }
}
