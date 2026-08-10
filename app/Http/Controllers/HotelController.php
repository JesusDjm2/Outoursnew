<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HotelController extends Controller
{
    public function index()
    {
        $hotels = Hotel::withCount('rooms')->orderBy('nombre')->get();
        return view('hotels.index', compact('hotels'));
    }

    public function create()
    {
        return view('hotels.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:500',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

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
        return view('hotels.edit', compact('hotel'));
    }

    public function update(Request $request, Hotel $hotel)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:500',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

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
}
