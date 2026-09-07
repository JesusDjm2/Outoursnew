@extends('layouts.app')
@section('title', 'Nuevo Proveedor')
@section('content')
<h1 class="text-xl font-bold text-gray-800 mb-5 dark:text-slate-100">Nuevo Proveedor</h1>
<div class="bg-white rounded-xl shadow p-5 md:p-6 max-w-2xl dark:bg-slate-900 dark:shadow-slate-950/50">
    <form method="POST" action="{{ route('proveedores.store') }}" enctype="multipart/form-data" class="space-y-3">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Nombre</label>
                <input type="text" name="nombre" value="{{ old('nombre') }}" required class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Tipo de proveedor</label>
                <select name="tipo_id" required class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
                    <option value="">Seleccionar tipo...</option>
                    @foreach($tiposProveedor as $tipoProveedor)
                    <option value="{{ $tipoProveedor->id }}" {{ (int) old('tipo_id') === $tipoProveedor->id ? 'selected' : '' }}>{{ $tipoProveedor->nombre }}</option>
                    @endforeach
                </select>
                @if($tiposProveedor->isEmpty())
                <p class="text-xs text-gray-400 mt-1 dark:text-slate-400">No hay tipos registrados. <a href="{{ route('tipos-proveedor.create') }}" class="text-amber-600 underline">Crear uno</a>.</p>
                @endif
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Teléfono</label>
                <input type="text" name="telefono" value="{{ old('telefono') }}" class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
            </div>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Dirección</label>
            <input type="text" name="direccion" value="{{ old('direccion') }}" class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Galería de fotos</label>
            <input type="file" name="galeria[]" multiple accept="image/*" class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
            <p class="text-xs text-gray-400 mt-1 dark:text-slate-400">Puedes seleccionar varias imágenes a la vez.</p>
        </div>
        <div class="flex gap-3 pt-1">
            <button type="submit" class="bg-amber-600 text-white px-5 py-2 text-sm font-medium rounded-lg hover:bg-amber-700 transition">Guardar</button>
            <a href="{{ route('proveedores.index') }}" class="bg-gray-300 text-gray-800 px-5 py-2 text-sm font-medium rounded-lg hover:bg-gray-400 transition dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600">Cancelar</a>
        </div>
    </form>
</div>
@endsection
