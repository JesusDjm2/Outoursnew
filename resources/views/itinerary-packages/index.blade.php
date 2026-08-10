@extends('layouts.app')
@section('title', 'Paquetes de Itinerarios')
@section('content')
<div class="flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-slate-100">Paquetes de Itinerarios</h1>
    <a href="{{ route('itinerary-packages.create') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition text-center">
        <i class="fas fa-plus mr-1"></i> Nuevo Paquete
    </a>
</div>
<p class="text-sm text-gray-500 dark:text-slate-400 mb-4">Plantillas de varios días armadas con itinerarios del catálogo. Puedes aplicarlas al crear o editar una cotización para llenar el itinerario en un solo paso.</p>

<form method="GET" action="{{ route('itinerary-packages.index') }}" class="mb-4">
    <div class="relative max-w-md">
        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-slate-500"></i>
        <input type="text" name="q" value="{{ $q }}" placeholder="Buscar paquete por nombre..."
               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
    </div>
</form>

<div class="bg-white rounded-xl shadow overflow-hidden dark:bg-slate-900 dark:shadow-slate-950/50">
    <table class="w-full">
        <thead class="bg-gray-50 dark:bg-slate-800">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Nombre</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Días</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Itinerarios</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
            @forelse($packages as $package)
            <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/60">
                <td class="px-6 py-4 text-sm font-medium text-gray-800 dark:text-slate-100">{{ $package->nombre }}</td>
                <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-400">{{ $package->dias }}</td>
                <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-400">{{ $package->itineraries_count }}</td>
                <td class="px-6 py-4 text-right text-sm">
                    <a href="{{ route('itinerary-packages.edit', $package) }}" class="text-blue-600 hover:text-blue-800 mr-3 dark:text-blue-400 dark:hover:text-blue-300"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('itinerary-packages.destroy', $package) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar este paquete de itinerarios?')">
                        @csrf @method('DELETE')
                        <button class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-6 py-10 text-center text-gray-500 dark:text-slate-400">
                    {{ $q ? 'No se encontraron paquetes para "' . $q . '".' : 'Aún no hay paquetes de itinerarios registrados.' }}
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($packages->hasPages())
<div class="mt-4">
    {{ $packages->links() }}
</div>
@endif
@endsection
