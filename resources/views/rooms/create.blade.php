@extends('layouts.app')
@section('title', 'Nueva Habitación')
@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-2 dark:text-slate-100">Nueva Habitación</h1>
<p class="text-gray-500 mb-6 dark:text-slate-400">Hotel: {{ $hotel->nombre }}</p>
<div class="bg-white rounded-xl shadow p-6 md:p-8 max-w-3xl dark:bg-slate-900 dark:shadow-slate-950/50">
    <form method="POST" action="{{ route('rooms.store', $hotel) }}">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">Nombre</label>
                <input type="text" name="nombre" value="{{ old('nombre') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">Cantidad de Personas</label>
                <input type="number" name="cantidad_personas" value="{{ old('cantidad_personas', 1) }}" min="1" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">N° de Habitación</label>
                <input type="text" name="numero_habitacion" value="{{ old('numero_habitacion') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">Precio Regular (por noche)</label>
                <input type="number" step="0.01" min="0" name="precio_regular" value="{{ old('precio_regular') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">Precio Promo (por noche)</label>
                <input type="number" step="0.01" min="0" name="precio_promo" value="{{ old('precio_promo') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
            </div>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="bg-teal-600 text-white px-6 py-2 rounded-lg hover:bg-teal-700 transition">Guardar</button>
            <a href="{{ route('rooms.index', $hotel) }}" class="bg-gray-300 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-400 transition dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600">Cancelar</a>
        </div>
    </form>
</div>
@endsection
