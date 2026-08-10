@extends('layouts.app')
@section('title', 'Tours')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-slate-100">Tours</h1>
    <a href="{{ route('tours.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
        <i class="fas fa-plus mr-1"></i> Nuevo Tour
    </a>
</div>
<div class="bg-white rounded-xl shadow overflow-hidden dark:bg-slate-900 dark:shadow-slate-950/50">
    <table class="w-full">
        <thead class="bg-gray-50 dark:bg-slate-800">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Código</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Nombre</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Inicio</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Fin</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
            @foreach($tours as $tour)
            <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/60">
                <td class="px-6 py-4 text-sm font-mono text-gray-600 dark:text-slate-400">{{ $tour->codigo }}</td>
                <td class="px-6 py-4 text-sm text-gray-800 font-medium dark:text-slate-100">{{ $tour->nombre }}</td>
                <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-400">{{ $tour->fecha_inicio ?? '—' }}</td>
                <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-400">{{ $tour->fecha_fin ?? '—' }}</td>
                <td class="px-6 py-4 text-right text-sm">
                    <a href="{{ route('tours.show', $tour) }}" class="text-emerald-600 hover:text-emerald-800 mr-3 dark:text-emerald-400 dark:hover:text-emerald-300" title="Ver"><i class="fas fa-eye"></i></a>
                    <a href="{{ route('tours.pdf', $tour) }}" target="_blank" class="text-rose-600 hover:text-rose-800 mr-3 dark:text-rose-400 dark:hover:text-rose-300" title="Ver / descargar PDF"><i class="fas fa-file-pdf"></i></a>
                    <a href="{{ route('itineraries.index', $tour) }}" class="text-purple-600 hover:text-purple-800 mr-3 dark:text-purple-400 dark:hover:text-purple-300" title="Itinerarios"><i class="fas fa-list"></i></a>
                    <a href="{{ route('tours.edit', $tour) }}" class="text-blue-600 hover:text-blue-800 mr-3 dark:text-blue-400 dark:hover:text-blue-300"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('tours.duplicate', $tour) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-amber-600 hover:text-amber-800 mr-3 dark:text-amber-400 dark:hover:text-amber-300" title="Duplicar cotización"><i class="fas fa-copy"></i></button>
                    </form>
                    <form action="{{ route('tours.destroy', $tour) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar este tour?')">
                        @csrf @method('DELETE')
                        <button class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
