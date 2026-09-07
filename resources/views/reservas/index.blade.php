@extends('layouts.app')
@section('title', 'Reservas')
@section('content')

<div class="flex flex-wrap items-center justify-between gap-3 mb-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-slate-100">Reservas</h1>
        <p class="text-sm text-gray-500 mt-0.5 dark:text-slate-400">Estado de las reservas de hospedaje y proveedores por cotización.</p>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
    <a href="{{ route('reservas.index', ['estado' => 'pendiente'] + ($q ? ['q' => $q] : [])) }}"
       class="rounded-xl border p-4 transition {{ $estado === 'pendiente' ? 'border-amber-400 bg-amber-50 dark:bg-amber-950/30 dark:border-amber-600' : 'border-gray-200 bg-white hover:bg-gray-50 dark:border-slate-800 dark:bg-slate-900 dark:hover:bg-slate-800/60' }}">
        <p class="text-xs font-medium uppercase tracking-wide text-amber-600 dark:text-amber-400">Pendientes</p>
        <p class="text-2xl font-bold text-gray-800 mt-1 dark:text-slate-100">{{ $resumen['pendiente'] }}</p>
    </a>
    <a href="{{ route('reservas.index', ['estado' => 'confirmada'] + ($q ? ['q' => $q] : [])) }}"
       class="rounded-xl border p-4 transition {{ $estado === 'confirmada' ? 'border-emerald-400 bg-emerald-50 dark:bg-emerald-950/30 dark:border-emerald-600' : 'border-gray-200 bg-white hover:bg-gray-50 dark:border-slate-800 dark:bg-slate-900 dark:hover:bg-slate-800/60' }}">
        <p class="text-xs font-medium uppercase tracking-wide text-emerald-600 dark:text-emerald-400">Confirmadas</p>
        <p class="text-2xl font-bold text-gray-800 mt-1 dark:text-slate-100">{{ $resumen['confirmada'] }}</p>
    </a>
    <a href="{{ route('reservas.index', ['estado' => 'cancelada'] + ($q ? ['q' => $q] : [])) }}"
       class="rounded-xl border p-4 transition {{ $estado === 'cancelada' ? 'border-rose-400 bg-rose-50 dark:bg-rose-950/30 dark:border-rose-600' : 'border-gray-200 bg-white hover:bg-gray-50 dark:border-slate-800 dark:bg-slate-900 dark:hover:bg-slate-800/60' }}">
        <p class="text-xs font-medium uppercase tracking-wide text-rose-600 dark:text-rose-400">Canceladas</p>
        <p class="text-2xl font-bold text-gray-800 mt-1 dark:text-slate-100">{{ $resumen['cancelada'] }}</p>
    </a>
</div>

<div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-4">
    <form method="GET" action="{{ route('reservas.index') }}" class="flex flex-1 gap-2 max-w-lg">
        @if($estado)<input type="hidden" name="estado" value="{{ $estado }}">@endif
        <div class="relative flex-1">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-slate-500"></i>
            <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por código, nombre o pasajero..."
                   class="w-full pl-10 {{ $q ? 'pr-9' : 'pr-3' }} py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
            @if($q)
            <a href="{{ route('reservas.index', $estado ? ['estado' => $estado] : []) }}" title="Limpiar búsqueda"
               class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:text-slate-500 dark:hover:text-slate-300">
                <i class="fas fa-xmark"></i>
            </a>
            @endif
        </div>
        <button type="submit" class="shrink-0 bg-blue-600 text-white px-4 py-1.5 rounded-lg hover:bg-blue-700 transition text-sm font-medium">
            <i class="fas fa-search mr-1"></i> Buscar
        </button>
    </form>
    @if($estado)
    <a href="{{ route('reservas.index', $q ? ['q' => $q] : []) }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-slate-400 dark:hover:text-slate-200">
        <i class="fas fa-filter-circle-xmark mr-1"></i> Quitar filtro de estado
    </a>
    @endif
</div>

<div class="bg-white rounded-xl shadow overflow-hidden dark:bg-slate-900 dark:shadow-slate-950/50">
    <table class="w-full">
        <thead class="bg-gray-50 dark:bg-slate-800">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Código</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Pasajero</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Hospedajes</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Proveedores</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
            @forelse($tours as $tour)
            <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/60">
                <td class="px-6 py-4 text-sm font-mono text-gray-600 dark:text-slate-400">{{ $tour->codigo }}</td>
                <td class="px-6 py-4 text-sm text-gray-800 font-medium dark:text-slate-100">{{ $tour->nombre_pax ?: '—' }}</td>
                <td class="px-6 py-4 text-sm">
                    @php
                        $estadoPillClass = fn ($estado) => match ($estado) {
                            'confirmada' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400',
                            'cancelada' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/30 dark:text-rose-400',
                            'reservado_pasajero' => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/30 dark:text-indigo-400',
                            default => 'bg-amber-50 text-amber-700 dark:bg-amber-950/30 dark:text-amber-400',
                        };
                    @endphp
                    @forelse($tour->hospedajes as $hospedaje)
                        <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium mr-1 mb-1 {{ $estadoPillClass($hospedaje->estado_reserva) }}">
                            {{ $hospedaje->hotel->nombre ?? 'Hotel' }}
                        </span>
                    @empty
                        <span class="text-gray-400 dark:text-slate-500">—</span>
                    @endforelse
                </td>
                <td class="px-6 py-4 text-sm">
                    @forelse($tour->proveedores as $proveedor)
                        <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium mr-1 mb-1 {{ $estadoPillClass($proveedor->pivot->estado_reserva) }}">
                            {{ $proveedor->nombre }}
                        </span>
                    @empty
                        <span class="text-gray-400 dark:text-slate-500">—</span>
                    @endforelse
                </td>
                <td class="px-6 py-4 text-right text-sm">
                    <a href="{{ route('reservas.show', $tour) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300" title="Gestionar reservas">
                        <i class="fas fa-clipboard-check"></i> Gestionar
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-10 text-center text-gray-500 dark:text-slate-400">
                    No hay cotizaciones con hospedajes o proveedores para mostrar.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($tours->hasPages())
<div class="mt-4">
    {{ $tours->links() }}
</div>
@endif
@endsection
