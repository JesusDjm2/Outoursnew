@extends('layouts.app')
@section('title', 'Nuevo Tipo de Proveedor')
@section('content')
<h1 class="text-xl font-bold text-gray-800 mb-5 dark:text-slate-100">Nuevo Tipo de Proveedor</h1>
<div class="bg-white rounded-xl shadow p-5 md:p-6 max-w-md dark:bg-slate-900 dark:shadow-slate-950/50">
    <form method="POST" action="{{ route('tipos-proveedor.store') }}" class="space-y-3">
        @csrf
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Nombre</label>
            <input type="text" name="nombre" value="{{ old('nombre') }}" required class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
            @error('nombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="flex gap-3 pt-1">
            <button type="submit" class="bg-amber-600 text-white px-5 py-2 text-sm font-medium rounded-lg hover:bg-amber-700 transition">Guardar</button>
            <a href="{{ route('proveedores.index') }}" class="bg-gray-300 text-gray-800 px-5 py-2 text-sm font-medium rounded-lg hover:bg-gray-400 transition dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600">Cancelar</a>
        </div>
    </form>
</div>
@endsection
