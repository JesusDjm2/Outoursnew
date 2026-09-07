@extends('layouts.app')
@section('title', 'Editar Proveedor')
@section('content')
<h1 class="text-xl font-bold text-gray-800 mb-5 dark:text-slate-100">Editar Proveedor</h1>
<div class="bg-white rounded-xl shadow p-5 md:p-6 max-w-2xl dark:bg-slate-900 dark:shadow-slate-950/50">
    <form method="POST" action="{{ route('proveedores.update', $proveedor) }}" enctype="multipart/form-data" class="space-y-3">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Nombre</label>
                <input type="text" name="nombre" value="{{ old('nombre', $proveedor->nombre) }}" required class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Tipo de proveedor</label>
                <select name="tipo_id" required class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
                    <option value="">Seleccionar tipo...</option>
                    @foreach($tiposProveedor as $tipoProveedor)
                    <option value="{{ $tipoProveedor->id }}" {{ (int) old('tipo_id', $proveedor->tipo_id) === $tipoProveedor->id ? 'selected' : '' }}>{{ $tipoProveedor->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Email</label>
                <input type="email" name="email" value="{{ old('email', $proveedor->email) }}" class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Teléfono</label>
                <input type="text" name="telefono" value="{{ old('telefono', $proveedor->telefono) }}" class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
            </div>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Dirección</label>
            <input type="text" name="direccion" value="{{ old('direccion', $proveedor->direccion) }}" class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
        </div>

        @if($proveedor->imagenes->isNotEmpty())
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-2 dark:text-slate-400">Galería actual</label>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                @foreach($proveedor->imagenes as $imagen)
                <label class="relative block cursor-pointer group">
                    <img src="{{ asset('storage/' . $imagen->path) }}" class="h-16 w-full object-cover rounded-lg border border-gray-200 dark:border-slate-700">
                    <span class="absolute inset-0 bg-red-600/70 opacity-0 group-has-[:checked]:opacity-100 flex items-center justify-center rounded-lg transition">
                        <i class="fas fa-trash text-white text-xs"></i>
                    </span>
                    <input type="checkbox" name="eliminar_imagenes[]" value="{{ $imagen->id }}" class="absolute top-1 right-1">
                </label>
                @endforeach
            </div>
            <p class="text-xs text-gray-400 mt-1 dark:text-slate-400">Marca la casilla de una foto para eliminarla al guardar.</p>
        </div>
        @endif

        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Agregar más fotos</label>
            <input type="file" name="galeria[]" multiple accept="image/*" class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
        </div>

        <div class="flex gap-3 pt-1">
            <button type="submit" class="bg-amber-600 text-white px-5 py-2 text-sm font-medium rounded-lg hover:bg-amber-700 transition">Actualizar</button>
            <a href="{{ route('proveedores.index') }}" class="bg-gray-300 text-gray-800 px-5 py-2 text-sm font-medium rounded-lg hover:bg-gray-400 transition dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600">Cancelar</a>
        </div>
    </form>
</div>
@endsection
