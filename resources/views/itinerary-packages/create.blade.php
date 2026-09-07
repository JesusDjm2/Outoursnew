@extends('layouts.app')
@section('title', 'Nuevo Paquete de Actividades')
@section('content')
@php $itemsSeleccionados = $itemsSeleccionados ?? collect(); @endphp
<h1 class="text-2xl font-bold text-gray-800 mb-6 dark:text-slate-100">Nuevo Paquete de Actividades</h1>
<div class="bg-white rounded-xl shadow p-6 md:p-8 dark:bg-slate-900 dark:shadow-slate-950/50">
    <form method="POST" action="{{ route('itinerary-packages.store') }}">
        @csrf
        <div class="flex flex-col sm:flex-row gap-4 mb-6">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">Nombre del paquete</label>
                <input type="text" name="nombre" value="{{ old('nombre') }}" required placeholder="Ej. Cusco Clásico 5 días" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
                @error('nombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="w-full sm:w-28">
                <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">Días</label>
                <input type="number" min="1" max="60" name="dias" value="{{ old('dias', 1) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
                @error('dias') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <h2 class="font-semibold text-gray-800 mb-3 dark:text-slate-100"><i class="fas fa-route text-purple-600 mr-1"></i> Actividades por día</h2>
        @error('itinerarios') <p class="text-red-500 text-xs mb-3">{{ $message }}</p> @enderror
        @include('itinerary-packages._package_picker', ['destinos' => $destinos])

        <div class="flex gap-3 mt-6">
            <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">Guardar</button>
            <a href="{{ route('itinerary-packages.index') }}" class="bg-gray-300 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-400 transition dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600">Cancelar</a>
        </div>
    </form>
</div>
@endsection
