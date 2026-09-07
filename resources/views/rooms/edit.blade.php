@extends('layouts.app')
@section('title', 'Editar Habitación')
@section('content')
<h1 class="text-xl font-bold text-gray-800 mb-1 dark:text-slate-100">Editar Habitación</h1>
<p class="text-sm text-gray-500 mb-1 dark:text-slate-400">Hotel: {{ $hotel->nombre }}</p>
<a href="{{ route('hotels.index') }}" class="text-blue-600 text-sm mb-5 inline-block dark:text-blue-400"><i class="fas fa-arrow-left"></i> Volver a Hoteles</a>
<div class="bg-white rounded-xl shadow p-5 md:p-6 max-w-2xl dark:bg-slate-900 dark:shadow-slate-950/50">
    <form method="POST" action="{{ route('rooms.update', [$hotel, $room]) }}" class="space-y-3">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Nombre</label>
                <input type="text" name="nombre" value="{{ old('nombre', $room->nombre) }}" required class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Cantidad de Personas</label>
                <input type="number" name="cantidad_personas" value="{{ old('cantidad_personas', $room->cantidad_personas) }}" min="1" required class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">N° de Habitación</label>
                <input type="text" name="numero_habitacion" value="{{ old('numero_habitacion', $room->numero_habitacion) }}" required class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Precio Regular (por noche)</label>
                <input type="number" step="0.01" min="0" name="precio_regular" value="{{ old('precio_regular', $room->precio_regular) }}" required class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Precio Promo (por noche)</label>
                <input type="number" step="0.01" min="0" name="precio_promo" value="{{ old('precio_promo', $room->precio_promo) }}" class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
            </div>
        </div>
        <div class="flex gap-3 pt-1">
            <button type="submit" class="bg-teal-600 text-white px-5 py-2 text-sm font-medium rounded-lg hover:bg-teal-700 transition">Actualizar</button>
            <a href="{{ route('hotels.index') }}" class="bg-gray-300 text-gray-800 px-5 py-2 text-sm font-medium rounded-lg hover:bg-gray-400 transition dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600">Cancelar</a>
        </div>
    </form>
</div>
@endsection
