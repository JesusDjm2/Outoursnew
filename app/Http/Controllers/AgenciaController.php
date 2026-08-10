<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use App\Models\User;
use Illuminate\Http\Request;

class AgenciaController extends Controller
{
    public function index(Request $request)
    {
        $recentTours = Tour::with('agencia')->latest()->take(10)->get();

        $q = $request->query('q');
        $ruc = $request->query('ruc');
        $telefono = $request->query('telefono');

        $agencias = User::role('Agencia')
            ->withCount('tours')
            ->when($q, fn($query) => $query->where('name', 'like', "%{$q}%"))
            ->when($ruc, fn($query) => $query->where('ruc', 'like', "%{$ruc}%"))
            ->when($telefono, fn($query) => $query->where('telefono', 'like', "%{$telefono}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('agencias.index', compact('recentTours', 'agencias', 'q', 'ruc', 'telefono'));
    }
}
