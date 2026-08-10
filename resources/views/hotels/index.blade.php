@extends('layouts.app')
@section('title', 'Hoteles')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-slate-100">Hoteles</h1>
    <a href="{{ route('hotels.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
        <i class="fas fa-plus mr-1"></i> Nuevo Hotel
    </a>
</div>
<div class="bg-white rounded-xl shadow overflow-hidden dark:bg-slate-900 dark:shadow-slate-950/50">
    <table class="w-full">
        <thead class="bg-gray-50 dark:bg-slate-800">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Nombre</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Dirección</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Teléfono</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Hab.</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
            @forelse($hotels as $hotel)
            <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/60">
                <td class="px-6 py-4 text-sm font-medium text-gray-800 dark:text-slate-100">{{ $hotel->nombre }}</td>
                <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-400">{{ Str::limit($hotel->direccion, 30) }}</td>
                <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-400">{{ $hotel->telefono }}</td>
                <td class="px-6 py-4 text-sm">
                    <a href="{{ route('rooms.index', $hotel) }}" class="text-teal-600 hover:underline dark:text-teal-400">{{ $hotel->rooms_count }} hab.</a>
                </td>
                <td class="px-6 py-4 text-right text-sm">
                    <a href="{{ route('rooms.index', $hotel) }}" class="text-teal-600 hover:text-teal-800 mr-3 dark:text-teal-400 dark:hover:text-teal-300" title="Habitaciones"><i class="fas fa-door-open"></i></a>
                    <a href="{{ route('hotels.edit', $hotel) }}" class="text-blue-600 hover:text-blue-800 mr-3 dark:text-blue-400 dark:hover:text-blue-300"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('hotels.destroy', $hotel) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar este hotel?')">
                        @csrf @method('DELETE')
                        <button class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-10 text-center text-gray-500 dark:text-slate-400">Aún no hay hoteles registrados.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
