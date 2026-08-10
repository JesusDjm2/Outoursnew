@extends('layouts.app')
@section('title', 'Categorías')
@section('content')
@include('categorias._tabs', ['active' => 'categorias'])
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-slate-100">Categorías</h1>
        <p class="text-sm text-gray-500 dark:text-slate-400">Segundo nivel de la jerarquía: Destino → Categoría → Subcategoría.</p>
    </div>
    @if($destinos->isEmpty())
        <a href="{{ route('destinos.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-1"></i> Crear un Destino primero
        </a>
    @else
        <a href="{{ route('categorias.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-1"></i> Nueva Categoría
        </a>
    @endif
</div>
<div class="bg-white rounded-xl shadow overflow-hidden dark:bg-slate-900 dark:shadow-slate-950/50">
    <table class="w-full">
        <thead class="bg-gray-50 dark:bg-slate-800">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Destino</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Categoría</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Subcategorías</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
            @forelse($categorias as $categoria)
            <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/60">
                <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-400">{{ $categoria->destino->nombre }}</td>
                <td class="px-6 py-4 text-sm font-medium text-gray-800 dark:text-slate-100">{{ $categoria->nombre }}</td>
                <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-400">{{ $categoria->subcategorias_count }}</td>
                <td class="px-6 py-4 text-right text-sm">
                    <a href="{{ route('categorias.edit', $categoria) }}" class="text-blue-600 hover:text-blue-800 mr-3 dark:text-blue-400 dark:hover:text-blue-300"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('categorias.destroy', $categoria) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar esta categoría?')">
                        @csrf @method('DELETE')
                        <button class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-6 py-10 text-center text-gray-500 dark:text-slate-400">Aún no hay categorías registradas.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
