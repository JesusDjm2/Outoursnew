@extends('layouts.app')
@section('title', 'Editar Tour')
@section('content')
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tom-select/2.3.1/css/tom-select.min.css">
@endpush
@php
    $itinerariosSeleccionados = $itinerariosSeleccionados ?? collect();
    $destinos = $destinos ?? collect();
    $oldPasajeros = old('pasajeros');
    $tourPassengers = $tour->passengers;
    $oldHospedajes = old('hospedajes');
    $tourHospedajes = $tour->hospedajes;
    $roomsByHotel = $hoteles->mapWithKeys(fn($hotel) => [
        $hotel->id => $hotel->rooms->map(fn($room) => [
            'id' => (string) $room->id,
            'label' => $room->nombre . ' · #' . $room->numero_habitacion,
        ])->values(),
    ]);
    $roomPrices = $hoteles->flatMap->rooms->mapWithKeys(fn($room) => [
        (string) $room->id => ['precio_regular' => $room->precio_regular, 'precio_promo' => $room->precio_promo],
    ]);
@endphp
<script>window.roomsByHotel = @json($roomsByHotel);</script>
<script>window.roomPrices = @json($roomPrices);</script>
<h1 class="text-2xl font-bold text-gray-800 mb-6 dark:text-slate-100">Editar Tour</h1>

<form method="POST" action="{{ route('tours.update', $tour) }}" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
    @csrf @method('PUT')

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
                <h2 class="font-semibold text-gray-800 dark:text-slate-100">
                    <i class="fas fa-route text-purple-600 mr-1"></i> Datos del Tour
                    <span class="text-xs font-normal text-gray-400 dark:text-slate-500">· Código {{ $tour->codigo }}</span>
                </h2>
                <a href="{{ route('itineraries.create') }}" target="_blank" class="text-purple-600 text-sm hover:underline dark:text-purple-400">
                    <i class="fas fa-plus"></i> Crear itinerario
                </a>
            </div>
            @include('tours._itinerary_picker')
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
                <div id="hospedajes-container" data-next-index="{{ $oldHospedajes ? count($oldHospedajes) : $tourHospedajes->count() }}">
                    @if($oldHospedajes)
                        @foreach($oldHospedajes as $i => $hospedajeOld)
                            @include('tours._hospedaje_fields', ['i' => $i, 'hospedaje' => $tourHospedajes->get($i)])
                        @endforeach
                    @else
                        @foreach($tourHospedajes as $i => $hospedaje)
                            @include('tours._hospedaje_fields', ['i' => $i, 'hospedaje' => $hospedaje])
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
                @php $selectedProveedores = old('proveedores', $tour->proveedores->pluck('id')->all()); @endphp
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($proveedores as $proveedor)
                    <label class="flex items-center gap-3 border border-gray-200 rounded-lg p-3 cursor-pointer hover:bg-amber-50 dark:border-slate-700 dark:bg-slate-800/50 dark:hover:bg-slate-800">
                        <input type="checkbox" name="proveedores[]" value="{{ $proveedor->id }}"
                               {{ in_array($proveedor->id, $selectedProveedores) ? 'checked' : '' }}
                               class="rounded text-amber-600 focus:ring-amber-500">
                        <span class="text-sm text-gray-700 dark:text-slate-300">{{ $proveedor->nombre }} <span class="text-gray-400 dark:text-slate-500">({{ $proveedor->tipo?->nombre ?? 'Sin tipo' }})</span></span>
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

            <div id="pasajeros-container" data-next-index="{{ $oldPasajeros ? count($oldPasajeros) : $tourPassengers->count() }}">
                @if($oldPasajeros)
                    @foreach($oldPasajeros as $i => $pasajeroOld)
                        @include('tours._passenger_fields', ['i' => $i, 'passenger' => $tourPassengers->get($i)])
                    @endforeach
                @else
                    @foreach($tourPassengers as $i => $passenger)
                        @include('tours._passenger_fields', ['i' => $i, 'passenger' => $passenger])
                    @endforeach
                @endif
            </div>
            <p class="text-xs text-gray-400 dark:text-slate-500">Puedes agregar, editar o quitar pasajeros de este tour. Las habitaciones se asignan desde "Asignar Habitaciones" en la vista del tour.</p>
        </div>

        @include('tours._resumen_factura')
    </div>
</form>

<template id="passenger-template">@include('tours._passenger_fields', ['i' => '__I__', 'passenger' => null])</template>
<template id="hospedaje-template">@include('tours._hospedaje_fields', ['i' => '__I__', 'hospedaje' => null])</template>

@include('tours._form_scripts')
@endsection
