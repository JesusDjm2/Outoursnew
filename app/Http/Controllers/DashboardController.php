<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Itinerary;
use App\Models\Passenger;
use App\Models\Room;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $isAgencia = $user->hasRole('Agencia');

        $tourQuery = Tour::query();
        $passengerQuery = Passenger::with('tour');
        if ($isAgencia) {
            $tourQuery->where('agencia_id', $user->id);
            $passengerQuery->whereHas('tour', fn($q) => $q->where('agencia_id', $user->id));
        }

        $stats = [
            'tours' => (clone $tourQuery)->count(),
            'itinerarios' => Itinerary::count(),
            'hoteles' => Hotel::count(),
            'habitaciones' => Room::count(),
            'pasajeros' => (clone $passengerQuery)->count(),
            'usuarios' => $isAgencia ? null : User::count(),
        ];

        $recentTours = (clone $tourQuery)->latest()->take(4)->get();
        $recentPassengers = $passengerQuery->latest()->take(4)->get();

        return view('dashboard', compact('stats', 'recentTours', 'recentPassengers', 'user'));
    }
}
