@extends('layouts.app')
@section('title', 'Nuevo Tour')
@section('content')
@php
    $tour = null;
    $oldPasajeros = old('pasajeros');
    $oldHospedajes = old('hospedajes');
    $itinerariosSeleccionados = $itinerariosSeleccionados ?? collect();
    $destinos = $destinos ?? collect();
    $roomsByHotel = $hoteles->mapWithKeys(fn($hotel) => [
        $hotel->id => $hotel->rooms->map(fn($room) => [
            'id' => (string) $room->id,
            'nombre' => $room->nombre,
            'numero_habitacion' => $room->numero_habitacion,
            'precio_regular' => $room->precio_regular,
            'precio_promo' => $room->precio_promo,
        ])->values(),
    ]);
    $roomPrices = $hoteles->flatMap->rooms->mapWithKeys(fn($room) => [
        (string) $room->id => ['precio_regular' => $room->precio_regular, 'precio_promo' => $room->precio_promo],
    ]);
    $hotelCatalogo = $hoteles->map(fn($hotel) => ['id' => $hotel->id, 'nombre' => $hotel->nombre, 'destino_id' => $hotel->destino_id])->values();
@endphp
<script>window.roomsByHotel = @json($roomsByHotel);</script>
<script>window.roomPrices = @json($roomPrices);</script>
<script>window.hotelCatalogo = @json($hotelCatalogo);</script>
<h1 class="text-2xl font-bold text-gray-800 mb-6 dark:text-slate-100">Nuevo Tour</h1>

<form method="POST" action="{{ route('tours.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
    @csrf

    <div class="space-y-6 lg:sticky lg:top-20 lg:col-span-3">
        <div class="bg-white rounded-xl shadow p-5 dark:bg-slate-900 dark:shadow-slate-950/50">
            <h2 class="font-semibold text-gray-800 mb-4 dark:text-slate-100"><i class="fas fa-user-tag text-blue-600 mr-1"></i> Datos Pax</h2>
            @include('tours._pax_fields')
        </div>

        <div class="bg-white rounded-xl shadow p-5 dark:bg-slate-900 dark:shadow-slate-950/50">
            <h2 class="font-semibold text-gray-800 mb-4 dark:text-slate-100"><i class="fas fa-globe text-blue-600 mr-1"></i> Idioma y moneda</h2>
            @include('tours._idioma_moneda_fields')
        </div>
    </div>

    <div class="space-y-6 lg:col-span-9">
        <div class="bg-white rounded-xl shadow p-6 dark:bg-slate-900 dark:shadow-slate-950/50">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-gray-800 dark:text-slate-100"><i class="fas fa-route text-purple-600 mr-1"></i> Datos del Tour</h2>
                <a href="{{ route('itineraries.create') }}" target="_blank" class="text-purple-600 text-sm hover:underline dark:text-purple-400">
                    <i class="fas fa-plus"></i> Crear actividad
                </a>
            </div>
            @include('tours._itinerary_picker', ['mostrarSelectorPaquetes' => true])
        </div>

        <div class="bg-white rounded-xl shadow p-6 dark:bg-slate-900 dark:shadow-slate-950/50">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-gray-800 dark:text-slate-100"><i class="fas fa-hotel text-indigo-600 mr-1"></i> Hospedajes</h2>
                <button type="button" id="add-hospedaje-btn" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 transition">
                    <i class="fas fa-plus"></i> Agregar hospedaje
                </button>
            </div>
            @if($hoteles->isEmpty())
                <p class="text-sm text-gray-500 dark:text-slate-400">Aún no hay hoteles registrados. <a href="{{ route('hotels.create') }}" class="text-indigo-600 underline dark:text-indigo-400">Crear uno</a>.</p>
            @else
                <div id="hotel-picker-panel" class="hidden fixed z-50 w-72 max-h-80 overflow-y-auto bg-white border border-gray-200 rounded-lg shadow-xl dark:bg-slate-800 dark:border-slate-700">
                    <div class="p-2 border-b border-gray-100 dark:border-slate-700 sticky top-0 bg-white dark:bg-slate-800">
                        <input type="text" id="hotel-picker-buscar" class="w-full px-2 py-1 text-xs border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-cyan-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" placeholder="Buscar hotel...">
                    </div>
                    <div id="hotel-picker-arbol" class="text-sm py-1"></div>
                </div>
                <div id="hospedajes-container" data-next-index="{{ $oldHospedajes ? count($oldHospedajes) : 0 }}">
                    @if($oldHospedajes)
                        @foreach($oldHospedajes as $i => $hospedajeOld)
                            @include('tours._hospedaje_fields', ['i' => $i, 'hospedaje' => null])
                        @endforeach
                    @endif
                </div>
                <p class="text-xs text-gray-400 dark:text-slate-500">Cada hospedaje es una estadía en un hotel durante un rango de fechas del tour.</p>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow p-6 dark:bg-slate-900 dark:shadow-slate-950/50">
            <h2 class="font-semibold text-gray-800 mb-4 dark:text-slate-100"><i class="fas fa-truck-fast text-amber-600 mr-1"></i> Proveedores</h2>
            @if($proveedores->isEmpty())
                <p class="text-sm text-gray-500 dark:text-slate-400">Aún no hay proveedores registrados. <a href="{{ route('proveedores.create') }}" class="text-amber-600 underline dark:text-amber-400">Crear uno</a>.</p>
            @else
                @php $selectedProveedores = old('proveedores', []); @endphp
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($proveedores as $proveedor)
                    @php
                        $proveedorChecked = in_array($proveedor->id, $selectedProveedores);
                        $fechasProveedor = old('proveedores_fechas.' . $proveedor->id, []);
                    @endphp
                    <label class="flex items-start gap-3 border border-gray-200 rounded-lg p-3 cursor-pointer hover:bg-amber-50 dark:border-slate-700 dark:bg-slate-800/50 dark:hover:bg-slate-800">
                        <input type="checkbox" name="proveedores[]" value="{{ $proveedor->id }}"
                               {{ $proveedorChecked ? 'checked' : '' }}
                               class="proveedor-checkbox mt-0.5 rounded text-amber-600 focus:ring-amber-500">
                        <span class="flex-1 min-w-0">
                            <span class="block text-sm text-gray-700 dark:text-slate-300"><span class="proveedor-nombre">{{ $proveedor->nombre }}</span> <span class="text-gray-400 dark:text-slate-500">({{ $proveedor->tipo?->nombre ?? 'Sin tipo' }})</span></span>
                            <div class="proveedor-fechas-wrap mt-1 flex flex-wrap items-center gap-1 {{ $proveedorChecked ? '' : 'hidden' }}" data-proveedor-id="{{ $proveedor->id }}">
                                <div class="proveedor-fechas-chips flex flex-wrap gap-1">
                                    @foreach($fechasProveedor as $fecha)
                                    <span class="proveedor-fecha-chip inline-flex items-center gap-1 rounded-full px-1.5 py-0.5 text-[10px] bg-amber-50 text-amber-700 dark:bg-amber-950/30 dark:text-amber-300">
                                        {{ $fecha }}
                                        <button type="button" class="proveedor-fecha-remove hover:text-red-600" title="Quitar fecha">&times;</button>
                                        <input type="hidden" name="proveedores_fechas[{{ $proveedor->id }}][]" value="{{ $fecha }}">
                                    </span>
                                    @endforeach
                                </div>
                                <input type="date" class="proveedor-fecha-add px-1.5 py-0.5 text-[11px] border border-gray-300 rounded dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100" title="Agregar fecha">
                            </div>
                        </span>
                    </label>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow p-6 dark:bg-slate-900 dark:shadow-slate-950/50">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-gray-800 dark:text-slate-100"><i class="fas fa-user-friends text-green-600 mr-1"></i> Pasajeros</h2>
                <button type="button" id="add-passenger-btn" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 transition">
                    <i class="fas fa-plus"></i> Agregar pasajero
                </button>
            </div>

            <div id="pasajeros-container" data-next-index="{{ $oldPasajeros ? count($oldPasajeros) : 0 }}">
                @if($oldPasajeros)
                    @foreach($oldPasajeros as $i => $pasajeroOld)
                        @include('tours._passenger_fields', ['i' => $i, 'passenger' => null])
                    @endforeach
                @endif
            </div>
            <p class="text-xs text-gray-400 dark:text-slate-500">Puedes agregar pasajeros ahora o más adelante editando el tour. Las habitaciones se asignan después de guardar, desde "Asignar Habitaciones".</p>
        </div>

        @include('tours._resumen_factura')
    </div>
</form>

<template id="passenger-template">@include('tours._passenger_fields', ['i' => '__I__', 'passenger' => null])</template>
<template id="hospedaje-template">@include('tours._hospedaje_fields', ['i' => '__I__', 'hospedaje' => null])</template>

@include('tours._form_scripts')
@endsection
