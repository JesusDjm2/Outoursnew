@extends('layouts.app')
@section('title', 'Reservas - ' . $tour->codigo)
@section('content')

<a href="{{ route('reservas.index') }}" class="text-blue-600 text-sm mb-1 inline-block dark:text-blue-400"><i class="fas fa-arrow-left"></i> Volver a Reservas</a>
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-slate-100">{{ $tour->nombre ?: $tour->codigo }}</h1>
    <p class="text-sm text-gray-500 mt-0.5 dark:text-slate-400">
        {{ $tour->codigo }} &middot; {{ $tour->nombre_pax ?: 'Sin pasajero asignado' }}
        <a href="{{ route('tours.show', $tour) }}" class="text-blue-600 hover:underline dark:text-blue-400 ml-2">Ver cotización completa</a>
    </p>
</div>

@php
    $estadoBadge = fn ($estado) => match ($estado) {
        'confirmada' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400',
        'cancelada' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/30 dark:text-rose-400',
        'reservado_pasajero' => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/30 dark:text-indigo-400',
        default => 'bg-amber-50 text-amber-700 dark:bg-amber-950/30 dark:text-amber-400',
    };
    $estadoLabel = fn ($estado) => match ($estado) {
        'confirmada' => 'Confirmada',
        'cancelada' => 'Cancelada',
        'reservado_pasajero' => 'Reservado por el pasajero',
        default => 'Pendiente',
    };
@endphp

<div class="bg-white rounded-xl shadow overflow-hidden dark:bg-slate-900 dark:shadow-slate-950/50 mb-6">
    <div class="px-5 py-3.5 border-b border-gray-100 dark:border-slate-800">
        <h2 class="font-semibold text-gray-800 dark:text-slate-100"><i class="fas fa-hotel text-teal-600 mr-2"></i>Hospedajes</h2>
    </div>
    <div class="divide-y divide-gray-100 dark:divide-slate-800">
        @forelse($tour->hospedajes as $hospedaje)
        <div class="flex flex-wrap items-start justify-between gap-3 px-5 py-4">
            <div class="min-w-0">
                <p class="font-medium text-gray-800 dark:text-slate-100">{{ $hospedaje->hotel->nombre ?? 'Hotel por definir' }}</p>
                <p class="text-xs text-gray-500 dark:text-slate-400">
                    {{ $hospedaje->fecha_ingreso ? \Illuminate\Support\Carbon::parse($hospedaje->fecha_ingreso)->format('d/m/Y') : '—' }}
                    &rarr;
                    {{ $hospedaje->fecha_salida ? \Illuminate\Support\Carbon::parse($hospedaje->fecha_salida)->format('d/m/Y') : '—' }}
                </p>
                @if($hospedaje->hotel)
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2 text-xs">
                    @if($hospedaje->hotel->telefono)
                    <a href="tel:{{ $hospedaje->hotel->telefono }}" class="flex items-center gap-1.5 text-gray-600 hover:text-blue-600 dark:text-slate-400 dark:hover:text-blue-400">
                        <i class="fas fa-phone w-3.5"></i> {{ $hospedaje->hotel->telefono }}
                    </a>
                    @endif
                    @if($hospedaje->hotel->email)
                    <a href="mailto:{{ $hospedaje->hotel->email }}" class="flex items-center gap-1.5 text-gray-600 hover:text-blue-600 dark:text-slate-400 dark:hover:text-blue-400">
                        <i class="fas fa-envelope w-3.5"></i> {{ $hospedaje->hotel->email }}
                    </a>
                    @endif
                    @if($hospedaje->hotel->direccion)
                    <span class="flex items-center gap-1.5 text-gray-500 dark:text-slate-500">
                        <i class="fas fa-location-dot w-3.5"></i> {{ $hospedaje->hotel->direccion }}
                    </span>
                    @endif
                    @if(!$hospedaje->hotel->telefono && !$hospedaje->hotel->email && !$hospedaje->hotel->direccion)
                    <span class="text-gray-400 dark:text-slate-500">Sin datos de contacto registrados para este hotel.</span>
                    @endif
                </div>
                @endif
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $estadoBadge($hospedaje->estado_reserva) }}">
                    {{ $estadoLabel($hospedaje->estado_reserva) }}
                </span>
                <form action="{{ route('reservas.hospedaje.update', $hospedaje) }}" method="POST" class="flex items-center gap-2">
                    @csrf @method('PUT')
                    <select name="estado_reserva" onchange="this.form.submit()"
                        class="text-sm border border-gray-300 rounded-lg px-2.5 py-1.5 focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
                        <option value="pendiente" {{ $hospedaje->estado_reserva === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="confirmada" {{ $hospedaje->estado_reserva === 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                        <option value="cancelada" {{ $hospedaje->estado_reserva === 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                        <option value="reservado_pasajero" {{ $hospedaje->estado_reserva === 'reservado_pasajero' ? 'selected' : '' }}>Reservado por el pasajero</option>
                    </select>
                </form>
            </div>
        </div>
        @empty
        <p class="px-5 py-8 text-center text-gray-500 dark:text-slate-400">Este tour no tiene hospedajes.</p>
        @endforelse
    </div>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden dark:bg-slate-900 dark:shadow-slate-950/50">
    <div class="px-5 py-3.5 border-b border-gray-100 dark:border-slate-800">
        <h2 class="font-semibold text-gray-800 dark:text-slate-100"><i class="fas fa-truck-fast text-teal-600 mr-2"></i>Proveedores</h2>
    </div>
    <div class="divide-y divide-gray-100 dark:divide-slate-800">
        @forelse($tour->proveedores as $proveedor)
        <div class="flex flex-wrap items-start justify-between gap-3 px-5 py-4">
            <div class="min-w-0">
                <p class="font-medium text-gray-800 dark:text-slate-100">{{ $proveedor->nombre }}</p>
                <p class="text-xs text-gray-500 dark:text-slate-400">{{ $proveedor->tipo->nombre ?? 'Sin tipo' }}</p>
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2 text-xs">
                    @if($proveedor->telefono)
                    <a href="tel:{{ $proveedor->telefono }}" class="flex items-center gap-1.5 text-gray-600 hover:text-blue-600 dark:text-slate-400 dark:hover:text-blue-400">
                        <i class="fas fa-phone w-3.5"></i> {{ $proveedor->telefono }}
                    </a>
                    @endif
                    @if($proveedor->email)
                    <a href="mailto:{{ $proveedor->email }}" class="flex items-center gap-1.5 text-gray-600 hover:text-blue-600 dark:text-slate-400 dark:hover:text-blue-400">
                        <i class="fas fa-envelope w-3.5"></i> {{ $proveedor->email }}
                    </a>
                    @endif
                    @if($proveedor->direccion)
                    <span class="flex items-center gap-1.5 text-gray-500 dark:text-slate-500">
                        <i class="fas fa-location-dot w-3.5"></i> {{ $proveedor->direccion }}
                    </span>
                    @endif
                    @if(!$proveedor->telefono && !$proveedor->email && !$proveedor->direccion)
                    <span class="text-gray-400 dark:text-slate-500">Sin datos de contacto registrados para este proveedor.</span>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $estadoBadge($proveedor->pivot->estado_reserva) }}">
                    {{ $estadoLabel($proveedor->pivot->estado_reserva) }}
                </span>
                <form action="{{ route('reservas.proveedor.update', [$tour, $proveedor]) }}" method="POST" class="flex items-center gap-2">
                    @csrf @method('PUT')
                    <select name="estado_reserva" onchange="this.form.submit()"
                        class="text-sm border border-gray-300 rounded-lg px-2.5 py-1.5 focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
                        <option value="pendiente" {{ $proveedor->pivot->estado_reserva === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="confirmada" {{ $proveedor->pivot->estado_reserva === 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                        <option value="cancelada" {{ $proveedor->pivot->estado_reserva === 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                        <option value="reservado_pasajero" {{ $proveedor->pivot->estado_reserva === 'reservado_pasajero' ? 'selected' : '' }}>Reservado por el pasajero</option>
                    </select>
                </form>
            </div>
        </div>
        @empty
        <p class="px-5 py-8 text-center text-gray-500 dark:text-slate-400">Este tour no tiene proveedores asignados.</p>
        @endforelse
    </div>
</div>
@endsection
