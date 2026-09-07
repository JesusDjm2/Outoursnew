<?php

namespace App\Http\Controllers;

use App\Models\Destino;
use App\Models\Hotel;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HotelController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');

        $hotels = Hotel::withCount('rooms')
            ->with(['destino', 'rooms' => fn ($query) => $query->orderBy('nombre')])
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('nombre', 'like', "%{$q}%")
                        ->orWhere('codigo', 'like', "%{$q}%")
                        ->orWhereHas('destino', fn ($d) => $d->where('nombre', 'like', "%{$q}%"));
                });
            })
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();
        $destinos = Destino::orderBy('nombre')->get();
        return view('hotels.index', compact('hotels', 'destinos', 'q'));
    }

    public function create()
    {
        $destinos = Destino::orderBy('nombre')->get();
        return view('hotels.create', compact('destinos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->hotelRules());

        $data = $validated;
        unset($data['imagen']);

        if ($request->hasFile('imagen')) {
            $data['imagen_path'] = $request->file('imagen')->store('hotels', 'public');
        }

        Hotel::create($data);

        return redirect()->route('hotels.index')->with('success', 'Hotel creado.');
    }

    public function edit(Hotel $hotel)
    {
        $hotel->load('rooms');
        $destinos = Destino::orderBy('nombre')->get();
        return view('hotels.edit', compact('hotel', 'destinos'));
    }

    public function update(Request $request, Hotel $hotel)
    {
        $validated = $request->validate($this->hotelRules());

        $data = $validated;
        unset($data['imagen']);

        if ($request->hasFile('imagen')) {
            if ($hotel->imagen_path) Storage::disk('public')->delete($hotel->imagen_path);
            $data['imagen_path'] = $request->file('imagen')->store('hotels', 'public');
        }

        $hotel->update($data);

        return redirect()->route('hotels.index')->with('success', 'Hotel actualizado.');
    }

    public function destroy(Hotel $hotel)
    {
        try {
            $hotel->delete();
        } catch (QueryException $e) {
            return redirect()->route('hotels.index')
                ->with('error', 'No se puede eliminar: hay tours con hospedajes en este hotel.');
        }

        if ($hotel->imagen_path) Storage::disk('public')->delete($hotel->imagen_path);
        return redirect()->route('hotels.index')->with('success', 'Hotel eliminado.');
    }

    private function hotelRules(): array
    {
        return [
            'destino_id' => 'required|exists:destinos,id',
            'codigo' => 'nullable|string|max:20',
            'nombre' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:500',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ];
    }
}
