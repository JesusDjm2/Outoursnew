@extends('layouts.app')
@section('title', 'Editar Hotel')
@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6 dark:text-slate-100">Editar Hotel: {{ $hotel->nombre }}</h1>
<div class="bg-white rounded-xl shadow p-6 md:p-8 max-w-3xl dark:bg-slate-900 dark:shadow-slate-950/50">
    <form method="POST" action="{{ route('hotels.update', $hotel) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">Nombre</label>
                <input type="text" name="nombre" value="{{ old('nombre', $hotel->nombre) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">Dirección</label>
                <input type="text" name="direccion" value="{{ old('direccion', $hotel->direccion) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">Teléfono</label>
                <input type="text" name="telefono" value="{{ old('telefono', $hotel->telefono) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">Email</label>
                <input type="email" name="email" value="{{ old('email', $hotel->email) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
            </div>
        </div>
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">Imagen</label>
            <input type="file" name="imagen" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
            @if($hotel->imagen_path)
            <img src="{{ asset('storage/' . $hotel->imagen_path) }}" class="mt-2 h-20 rounded">
            @endif
        </div>
        <div class="flex gap-3">
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">Actualizar</button>
            <a href="{{ route('hotels.index') }}" class="bg-gray-300 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-400 transition dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600">Cancelar</a>
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow p-6 md:p-8 max-w-3xl mt-6 dark:bg-slate-900 dark:shadow-slate-950/50">
    <div class="flex items-center justify-between mb-3">
        <h2 class="font-semibold text-gray-800 dark:text-slate-100"><i class="fas fa-door-open text-teal-600 mr-1"></i> Habitaciones ({{ $hotel->rooms->count() }})</h2>
        <a href="{{ route('rooms.create', $hotel) }}" class="text-teal-600 hover:text-teal-800 text-sm dark:text-teal-400 dark:hover:text-teal-300"><i class="fas fa-plus"></i> Agregar</a>
    </div>
    @if($hotel->rooms->isEmpty())
        <p class="text-sm text-gray-500 dark:text-slate-400">Este hotel aún no tiene habitaciones registradas.</p>
    @else
        <ul class="grid grid-cols-1 md:grid-cols-2 gap-x-6 divide-y divide-gray-100 md:divide-y-0 dark:divide-slate-800">
            @foreach($hotel->rooms as $room)
            <li class="py-2 flex items-center justify-between text-sm border-b border-gray-100 md:border-none dark:border-slate-800">
                <span class="text-gray-700 dark:text-slate-300">{{ $room->nombre }} · #{{ $room->numero_habitacion }} · {{ $room->cantidad_personas }} pax</span>
                <a href="{{ route('rooms.edit', [$hotel, $room]) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"><i class="fas fa-edit"></i></a>
            </li>
            @endforeach
        </ul>
    @endif
    <a href="{{ route('rooms.index', $hotel) }}" class="text-sm text-teal-600 hover:underline mt-3 inline-block dark:text-teal-400">Ver todas las habitaciones</a>
</div>
@endsection
