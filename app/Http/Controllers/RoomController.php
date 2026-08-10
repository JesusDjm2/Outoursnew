<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Hotel $hotel)
    {
        $rooms = $hotel->rooms;
        return view('rooms.index', compact('hotel', 'rooms'));
    }

    public function create(Hotel $hotel)
    {
        return view('rooms.create', compact('hotel'));
    }

    public function store(Request $request, Hotel $hotel)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'cantidad_personas' => 'required|integer|min:1',
            'numero_habitacion' => 'required|string|max:20',
            'precio_regular' => 'required|numeric|min:0',
            'precio_promo' => 'nullable|numeric|min:0',
        ]);

        $validated['hotel_id'] = $hotel->id;
        Room::create($validated);

        return redirect()->route('rooms.index', $hotel)->with('success', 'Habitación creada.');
    }

    public function edit(Hotel $hotel, Room $room)
    {
        return view('rooms.edit', compact('hotel', 'room'));
    }

    public function update(Request $request, Hotel $hotel, Room $room)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'cantidad_personas' => 'required|integer|min:1',
            'numero_habitacion' => 'required|string|max:20',
            'precio_regular' => 'required|numeric|min:0',
            'precio_promo' => 'nullable|numeric|min:0',
        ]);

        $room->update($validated);

        return redirect()->route('rooms.index', $hotel)->with('success', 'Habitación actualizada.');
    }

    public function destroy(Hotel $hotel, Room $room)
    {
        $room->delete();
        return redirect()->route('rooms.index', $hotel)->with('success', 'Habitación eliminada.');
    }
}
