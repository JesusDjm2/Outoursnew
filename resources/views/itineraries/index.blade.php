@extends('layouts.app')
@section('title', 'Itinerarios')
@section('content')
<div class="flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-slate-100">Itinerarios</h1>
    <a href="{{ route('itineraries.create') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition text-center">
        <i class="fas fa-plus mr-1"></i> Nuevo Itinerario
    </a>
</div>

<form method="GET" action="{{ route('itineraries.index') }}" class="mb-4">
    <div class="relative max-w-md">
        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-slate-500"></i>
        <input type="text" name="q" value="{{ $q }}" placeholder="Buscar itinerario por nombre..."
               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
    </div>
</form>

<div class="bg-white rounded-xl shadow overflow-hidden dark:bg-slate-900 dark:shadow-slate-950/50">
    <table class="w-full">
        <thead class="bg-gray-50 dark:bg-slate-800">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Nombre</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Categoría</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Incluye</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
            @forelse($itineraries as $itinerary)
            <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/60">
                <td class="px-6 py-4 text-sm font-medium text-gray-800 dark:text-slate-100">{{ $itinerary->nombre }}</td>
                <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-400">
                    @if($itinerary->destino)
                        {{ implode(' → ', array_filter([
                            $itinerary->destino->nombre,
                            $itinerary->categoria?->nombre,
                        ])) }}
                    @else
                        <span class="text-amber-500">Sin destino</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-400">{{ Str::limit(strip_tags($itinerary->incluye ?? ''), 50) ?: '—' }}</td>
                <td class="px-6 py-4 text-right text-sm">
                    <a href="{{ route('itineraries.edit', $itinerary) }}" class="text-blue-600 hover:text-blue-800 mr-3 dark:text-blue-400 dark:hover:text-blue-300"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('itineraries.destroy', $itinerary) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar este itinerario? Se quitará de todos los tours que lo usen.')">
                        @csrf @method('DELETE')
                        <button class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-6 py-10 text-center text-gray-500 dark:text-slate-400">
                    {{ $q ? 'No se encontraron itinerarios para "' . $q . '".' : 'Aún no hay itinerarios registrados.' }}
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($itineraries->hasPages())
<div class="mt-4">
    {{ $itineraries->links() }}
</div>
@endif
@endsection
