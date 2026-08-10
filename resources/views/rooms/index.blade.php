@extends('layouts.app')
@section('title', 'Habitaciones - ' . $hotel->nombre)
@section('content')
<div class="flex justify-between items-center mb-2">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-slate-100">Habitaciones de: {{ $hotel->nombre }}</h1>
    <a href="{{ route('rooms.create', $hotel) }}" class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition">
        <i class="fas fa-plus mr-1"></i> Nueva Habitación
    </a>
</div>
<a href="{{ route('hotels.index') }}" class="text-blue-600 text-sm mb-4 inline-block dark:text-blue-400"><i class="fas fa-arrow-left"></i> Volver a Hoteles</a>
<div class="bg-white rounded-xl shadow overflow-hidden mt-2 dark:bg-slate-900 dark:shadow-slate-950/50">
    <table class="w-full">
        <thead class="bg-gray-50 dark:bg-slate-800">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Nombre</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">N° Habitación</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Capacidad</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Precio/noche</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
            @forelse($rooms as $room)
            <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/60">
                <td class="px-6 py-4 text-sm font-medium text-gray-800 dark:text-slate-100">{{ $room->nombre }}</td>
                <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-400">{{ $room->numero_habitacion }}</td>
                <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-400">{{ $room->cantidad_personas }} personas</td>
                <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-400">
                    {{ number_format($room->precio_regular ?? 0, 2) }}
                    @if($room->precio_promo)
                        <span class="text-emerald-600 dark:text-emerald-400">/ promo {{ number_format($room->precio_promo, 2) }}</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-right text-sm">
                    <a href="{{ route('rooms.edit', [$hotel, $room]) }}" class="text-blue-600 hover:text-blue-800 mr-3 dark:text-blue-400 dark:hover:text-blue-300"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('rooms.destroy', [$hotel, $room]) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar esta habitación?')">
                        @csrf @method('DELETE')
                        <button class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-6 py-10 text-center text-gray-500 dark:text-slate-400">Este hotel aún no tiene habitaciones.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
