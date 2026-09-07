@extends('layouts.app')
@section('title', 'Nuevo Tour')
@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-2 dark:text-slate-100">Nuevo Tour</h1>
<p class="text-sm text-gray-500 mb-6 dark:text-slate-400">¿Cómo quieres armar esta cotización?</p>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
    <a href="{{ route('tours.create') }}"
       class="block bg-white rounded-xl shadow p-6 border-2 border-transparent hover:border-blue-500 transition dark:bg-slate-900 dark:shadow-slate-950/50">
        <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mb-4 dark:bg-blue-900/40 dark:text-blue-400">
            <i class="fas fa-file-circle-plus text-xl"></i>
        </div>
        <h2 class="font-semibold text-gray-800 mb-1 dark:text-slate-100">Armar desde cero</h2>
        <p class="text-sm text-gray-500 dark:text-slate-400">Empieza con un formulario en blanco y agrega actividades, hospedajes y pasajeros manualmente.</p>
    </a>

    <div class="bg-white rounded-xl shadow p-6 dark:bg-slate-900 dark:shadow-slate-950/50">
        <div class="w-12 h-12 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center mb-4 dark:bg-purple-900/40 dark:text-purple-400">
            <i class="fas fa-layer-group text-xl"></i>
        </div>
        <h2 class="font-semibold text-gray-800 mb-1 dark:text-slate-100">Usar un paquete predefinido</h2>

        @if($paquetes->isEmpty())
            <p class="text-sm text-gray-500 dark:text-slate-400">Aún no hay paquetes creados. <a href="{{ route('itinerary-packages.create') }}" class="text-purple-600 underline dark:text-purple-400">Crear uno</a>.</p>
        @else
            <p class="text-sm text-gray-500 mb-4 dark:text-slate-400">Elige un paquete para empezar el formulario con sus actividades ya cargadas.</p>
            <div class="space-y-2 max-h-72 overflow-y-auto pr-1">
                @foreach($paquetes as $paquete)
                    <a href="{{ route('tours.create', ['paquete' => $paquete->id]) }}"
                       class="flex items-center justify-between gap-3 border border-gray-200 rounded-lg p-3 hover:border-purple-500 hover:bg-purple-50 transition dark:border-slate-700 dark:hover:bg-purple-950/20">
                        <div>
                            <p class="text-sm font-medium text-gray-800 dark:text-slate-100">{{ $paquete->nombre }}</p>
                            @if($paquete->descripcion)
                                <p class="text-xs text-gray-400 dark:text-slate-500">{{ Str::limit($paquete->descripcion, 80) }}</p>
                            @endif
                        </div>
                        <span class="shrink-0 text-xs font-medium text-purple-600 bg-purple-100 rounded-full px-2 py-1 dark:bg-purple-900/40 dark:text-purple-300">
                            {{ $paquete->dias }} {{ $paquete->dias == 1 ? 'día' : 'días' }}
                        </span>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
