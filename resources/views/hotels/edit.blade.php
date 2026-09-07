@extends('layouts.app')
@section('title', 'Editar Hotel')
@section('content')
<h1 class="text-xl font-bold text-gray-800 mb-5 dark:text-slate-100">Editar Hotel: {{ $hotel->nombre }}</h1>
<div class="bg-white rounded-xl shadow p-5 md:p-6 max-w-2xl dark:bg-slate-900 dark:shadow-slate-950/50">
    <form method="POST" action="{{ route('hotels.update', $hotel) }}" enctype="multipart/form-data" class="space-y-3">
        @csrf @method('PUT')
        @if($destinos->isEmpty())
            <p class="text-sm text-amber-600 dark:text-amber-400">
                Aún no hay destinos registrados. <a href="{{ route('destinos.create') }}" target="_blank" class="underline">Crea uno primero</a> y luego recarga esta página.
            </p>
        @else
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Destino</label>
            <select name="destino_id" required class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
                <option value="">Selecciona...</option>
                @foreach($destinos as $destino)
                    <option value="{{ $destino->id }}" {{ (string) old('destino_id', $hotel->destino_id) === (string) $destino->id ? 'selected' : '' }}>{{ $destino->nombre }}</option>
                @endforeach
            </select>
            @error('destino_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        @endif
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Código <span class="text-gray-400">(opcional)</span></label>
                <input type="text" name="codigo" value="{{ old('codigo', $hotel->codigo) }}" list="hoteles-codigos-datalist" maxlength="20" class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500 uppercase" placeholder="TE, TEA...">
                <datalist id="hoteles-codigos-datalist">
                    <option value="TE">
                    <option value="TEA">
                </datalist>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Nombre</label>
                <input type="text" name="nombre" value="{{ old('nombre', $hotel->nombre) }}" required class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Dirección</label>
                <input type="text" name="direccion" value="{{ old('direccion', $hotel->direccion) }}" class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Teléfono</label>
                <input type="text" name="telefono" value="{{ old('telefono', $hotel->telefono) }}" class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Email</label>
                <input type="email" name="email" value="{{ old('email', $hotel->email) }}" class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
            </div>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Imagen</label>
            <input type="file" name="imagen" accept="image/*" class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
            @if($hotel->imagen_path)
            <img src="{{ asset('storage/' . $hotel->imagen_path) }}" class="mt-2 h-16 rounded">
            @endif
        </div>
        <div class="flex gap-3 pt-1">
            <button type="submit" class="bg-indigo-600 text-white px-5 py-2 text-sm font-medium rounded-lg hover:bg-indigo-700 transition">Actualizar</button>
            <a href="{{ route('hotels.index') }}" class="bg-gray-300 text-gray-800 px-5 py-2 text-sm font-medium rounded-lg hover:bg-gray-400 transition dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600">Cancelar</a>
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow p-5 md:p-6 mt-5 max-w-2xl dark:bg-slate-900 dark:shadow-slate-950/50">
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-sm font-semibold text-gray-800 dark:text-slate-100"><i class="fas fa-door-open text-teal-600 mr-1"></i> Habitaciones ({{ $hotel->rooms->count() }})</h2>
        <a href="{{ route('rooms.create', $hotel) }}" class="text-teal-600 hover:text-teal-800 text-xs font-medium dark:text-teal-400 dark:hover:text-teal-300"><i class="fas fa-plus"></i> Agregar</a>
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
</div>
@endsection
