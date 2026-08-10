@extends('layouts.app')
@section('title', 'Proveedores')
@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-slate-100">Proveedores</h1>
        <a href="{{ route('tipos-proveedor.index') }}" class="text-sm text-amber-600 hover:underline dark:text-amber-400"><i class="fas fa-tags"></i> Gestionar tipos de proveedor</a>
    </div>
    <a href="{{ route('proveedores.create') }}" class="bg-amber-600 text-white px-4 py-2 rounded-lg hover:bg-amber-700 transition">
        <i class="fas fa-plus mr-1"></i> Nuevo Proveedor
    </a>
</div>

@if($proveedores->isEmpty())
    <div class="bg-white rounded-xl shadow p-10 text-center text-gray-500 dark:bg-slate-900 dark:shadow-slate-950/50 dark:text-slate-400">
        Aún no hay proveedores registrados.
    </div>
@else
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
    @foreach($proveedores as $proveedor)
    <div class="bg-white rounded-xl shadow overflow-hidden gsap-fade dark:bg-slate-900 dark:shadow-slate-950/50">
        <div class="h-36 bg-gray-100 flex items-center justify-center overflow-hidden dark:bg-slate-800">
            @if($proveedor->imagenes->isNotEmpty())
                <img src="{{ asset('storage/' . $proveedor->imagenes->first()->path) }}" class="w-full h-full object-cover">
            @else
                <i class="fas fa-industry text-4xl text-gray-300 dark:text-slate-600"></i>
            @endif
        </div>
        <div class="p-4">
            <div class="flex items-center justify-between gap-2">
                <h2 class="font-semibold text-gray-800 dark:text-slate-100">{{ $proveedor->nombre }}</h2>
                <span class="shrink-0 rounded-full bg-amber-50 text-amber-700 text-[11px] font-medium px-2.5 py-1 dark:bg-amber-900/40 dark:text-amber-300">
                    {{ $proveedor->tipo?->nombre ?? 'Sin tipo' }}
                </span>
            </div>
            <p class="text-sm text-gray-500 mt-1 dark:text-slate-400">
                @if($proveedor->email)<i class="fas fa-envelope w-4 text-gray-400 dark:text-slate-500"></i> {{ $proveedor->email }}<br>@endif
                @if($proveedor->telefono)<i class="fas fa-phone w-4 text-gray-400 dark:text-slate-500"></i> {{ $proveedor->telefono }}<br>@endif
                @if($proveedor->direccion)<i class="fas fa-map-marker-alt w-4 text-gray-400 dark:text-slate-500"></i> {{ $proveedor->direccion }}@endif
            </p>
            <p class="text-xs text-gray-400 mt-2 dark:text-slate-500">{{ $proveedor->imagenes->count() }} foto(s) en galería</p>
            <div class="flex gap-3 mt-4 text-sm">
                <a href="{{ route('proveedores.edit', $proveedor) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"><i class="fas fa-edit"></i> Editar</a>
                <form action="{{ route('proveedores.destroy', $proveedor) }}" method="POST" onsubmit="return confirm('¿Eliminar este proveedor?')">
                    @csrf @method('DELETE')
                    <button class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"><i class="fas fa-trash"></i> Eliminar</button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection
