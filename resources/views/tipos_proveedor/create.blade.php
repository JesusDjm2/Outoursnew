@extends('layouts.app')
@section('title', 'Nuevo Tipo de Proveedor')
@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6 dark:text-slate-100">Nuevo Tipo de Proveedor</h1>
<div class="bg-white rounded-xl shadow p-6 md:p-8 max-w-3xl dark:bg-slate-900 dark:shadow-slate-950/50">
    <form method="POST" action="{{ route('tipos-proveedor.store') }}">
        @csrf
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">Nombre</label>
            <input type="text" name="nombre" value="{{ old('nombre') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
            @error('nombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="flex gap-3">
            <button type="submit" class="bg-amber-600 text-white px-6 py-2 rounded-lg hover:bg-amber-700 transition">Guardar</button>
            <a href="{{ route('tipos-proveedor.index') }}" class="bg-gray-300 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-400 transition dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600">Cancelar</a>
        </div>
    </form>
</div>
@endsection
