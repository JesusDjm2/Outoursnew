<?php

namespace App\Http\Controllers;

use App\Models\Passenger;

class PassengerController extends Controller
{
    public function index()
    {
        $passengers = Passenger::with(['tour', 'asignaciones.room.hotel', 'asignaciones.hospedaje'])->latest()->paginate(20);
        return view('passengers.index', compact('passengers'));
    }
}
