@extends('layouts.app')
@section('title', 'Editar Categoría')
@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-1 dark:text-slate-100">Editar Categoría</h1>
<a href="{{ route('destinos.index') }}" class="text-blue-600 text-sm mb-5 inline-block dark:text-blue-400"><i class="fas fa-arrow-left"></i> Volver a Destinos</a>
<div class="bg-white rounded-xl shadow p-6 md:p-8 dark:bg-slate-900 dark:shadow-slate-950/50">
    <form method="POST" action="{{ route('categorias.update', $categoria) }}">
        @csrf @method('PUT')
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">Destino</label>
            <select name="destino_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
                @foreach($destinos as $destino)
                    <option value="{{ $destino->id }}" {{ old('destino_id', $categoria->destino_id) == $destino->id ? 'selected' : '' }}>{{ $destino->nombre }}</option>
                @endforeach
            </select>
            @error('destino_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">Nombre</label>
            <input type="text" name="nombre" value="{{ old('nombre', $categoria->nombre) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
            @error('nombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="flex gap-3">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">Actualizar</button>
            <a href="{{ route('categorias.index') }}" class="bg-gray-300 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-400 transition dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600">Cancelar</a>
        </div>
    </form>
</div>
@endsection
