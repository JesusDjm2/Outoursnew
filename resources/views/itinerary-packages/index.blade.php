@extends('layouts.app')
@section('title', 'Paquetes de Actividades')
@section('content')
@include('partials._cotizador_tabs', ['cotizadorActive' => 'paquetes'])

<div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-4">
    <form method="GET" action="{{ route('itinerary-packages.index') }}" class="flex flex-1 gap-2 max-w-lg">
        <div class="relative flex-1">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-slate-500"></i>
            <input type="text" name="q" value="{{ $q }}" placeholder="Buscar paquete por nombre..."
                   class="w-full pl-10 {{ $q ? 'pr-9' : 'pr-3' }} py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
            @if($q)
            <a href="{{ route('itinerary-packages.index') }}" title="Limpiar búsqueda"
               class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:text-slate-500 dark:hover:text-slate-300">
                <i class="fas fa-xmark"></i>
            </a>
            @endif
        </div>
        <button type="submit" class="shrink-0 bg-purple-600 text-white px-4 py-1.5 rounded-lg hover:bg-purple-700 transition text-sm font-medium">
            <i class="fas fa-search mr-1"></i> Buscar
        </button>
    </form>
    <a href="{{ route('itinerary-packages.create') }}" class="shrink-0 sm:ml-auto bg-purple-600 text-white px-4 py-1.5 rounded-lg hover:bg-purple-700 transition text-sm font-medium text-center">
        <i class="fas fa-plus mr-1"></i> Nuevo Paquete
    </a>
</div>

<div class="space-y-3">
    @forelse($packages as $package)
    <details class="group bg-white rounded-xl shadow overflow-hidden dark:bg-slate-900 dark:shadow-slate-950/50">
        <summary class="list-none cursor-pointer flex items-center justify-between gap-3 px-5 py-3.5 hover:bg-gray-50 dark:hover:bg-slate-800/60 [&::-webkit-details-marker]:hidden">
            <div class="flex items-center gap-3 min-w-0">
                <i class="fas fa-chevron-right text-gray-400 text-xs transition-transform duration-200 group-open:rotate-90 dark:text-slate-500 shrink-0"></i>
                <span class="font-semibold text-gray-800 dark:text-slate-100 truncate">{{ $package->nombre }}</span>
                <span class="shrink-0 rounded-full bg-purple-50 text-purple-700 text-xs font-medium px-2.5 py-1 dark:bg-purple-900/30 dark:text-purple-300">
                    {{ $package->dias }} {{ $package->dias == 1 ? 'día' : 'días' }}
                </span>
                <span class="shrink-0 rounded-full bg-gray-100 text-gray-600 text-xs font-medium px-2.5 py-1 dark:bg-slate-800 dark:text-slate-300">
                    {{ $package->itineraries_count }} {{ $package->itineraries_count == 1 ? 'actividad' : 'actividades' }}
                </span>
            </div>
            <div class="flex items-center gap-3 shrink-0" onclick="event.stopPropagation();">
                <a href="{{ route('itinerary-packages.edit', $package) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300" title="Editar paquete"><i class="fas fa-edit"></i></a>
                <form action="{{ route('itinerary-packages.destroy', $package) }}" method="POST" onsubmit="return swalConfirmSubmit(event, '¿Eliminar este paquete de itinerarios?')">
                    @csrf @method('DELETE')
                    <button class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300" title="Eliminar paquete"><i class="fas fa-trash"></i></button>
                </form>
            </div>
        </summary>
        <div class="border-t border-gray-100 px-5 py-4 dark:border-slate-800">
            @if($package->itineraries->isEmpty())
                <p class="text-sm text-gray-400 dark:text-slate-500">Este paquete aún no tiene actividades.</p>
            @else
                <div class="space-y-4">
                    @foreach($package->itineraries->groupBy('pivot.dia') as $dia => $itinerariosDelDia)
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-purple-600 mb-2 dark:text-purple-400">Día {{ $dia }}</p>
                        <ul class="divide-y divide-gray-100 dark:divide-slate-800">
                            @foreach($itinerariosDelDia as $itinerario)
                            <li class="flex items-center justify-between gap-3 py-2.5 px-2 -mx-2 rounded-lg transition hover:bg-gray-50 dark:hover:bg-slate-800/60">
                                <div class="min-w-0 flex flex-wrap items-center gap-x-2 gap-y-1 sm:flex-nowrap sm:flex-1">
                                    @if($itinerario->destino)
                                        <span class="shrink-0 inline-flex items-center gap-1 rounded-full bg-indigo-50 text-indigo-700 text-[11px] font-medium px-2 py-0.5 dark:bg-indigo-900/30 dark:text-indigo-300">
                                            <i class="fas fa-map-marker-alt text-[10px]"></i>
                                            {{ implode(' → ', array_filter([$itinerario->destino->nombre, $itinerario->categoria?->nombre])) }}
                                        </span>
                                    @else
                                        <span class="shrink-0 inline-flex items-center gap-1 rounded-full bg-amber-50 text-amber-600 text-[11px] font-medium px-2 py-0.5 dark:bg-amber-900/30 dark:text-amber-400">
                                            <i class="fas fa-triangle-exclamation text-[10px]"></i> Sin destino
                                        </span>
                                    @endif
                                    <p class="text-sm font-medium text-gray-800 truncate dark:text-slate-100 min-w-0">{{ $itinerario->nombre }}</p>
                                    @if($itinerario->pivot->cantidad_pax_defecto)
                                        <span class="shrink-0 text-xs text-gray-400 dark:text-slate-500"
                                            title="Cantidad de pax que se precarga para esta actividad al aplicar el paquete a una cotización. Se puede editar después, por eso puede diferir del total de pax de la cotización.">
                                            <i class="fas fa-user-group text-[10px]"></i> {{ $itinerario->pivot->cantidad_pax_defecto }} pax por defecto
                                        </span>
                                    @endif
                                </div>
                                <div class="shrink-0 text-right text-sm sm:flex sm:items-center sm:gap-2">
                                    <span class="font-medium text-gray-700 dark:text-slate-200">{{ $itinerario->costo !== null ? number_format($itinerario->costo, 2) : '—' }}</span>
                                    @if($itinerario->costo_promo !== null)
                                        <span class="block text-xs text-emerald-600 dark:text-emerald-400 sm:inline">promo {{ number_format($itinerario->costo_promo, 2) }}</span>
                                    @endif
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </details>
    @empty
    <div class="bg-white rounded-xl shadow px-6 py-10 text-center text-gray-500 dark:bg-slate-900 dark:shadow-slate-950/50 dark:text-slate-400">
        {{ $q ? 'No se encontraron paquetes para "' . $q . '".' : 'Aún no hay paquetes de itinerarios registrados.' }}
    </div>
    @endforelse
</div>

@if($packages->hasPages())
<div class="mt-4">
    {{ $packages->links() }}
</div>
@endif
@endsection
