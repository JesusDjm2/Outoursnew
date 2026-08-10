@extends('layouts.app')
@section('title', 'Subcategorías')
@section('content')
@include('categorias._tabs', ['active' => 'subcategorias'])
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-slate-100">Subcategorías</h1>
        <p class="text-sm text-gray-500 dark:text-slate-400">Tercer nivel de la jerarquía: Destino → Categoría → Subcategoría. Los itinerarios se clasifican aquí.</p>
    </div>
    @if($categorias->isEmpty())
        <a href="{{ route('categorias.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-1"></i> Crear una Categoría primero
        </a>
    @else
        <a href="{{ route('subcategorias.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-1"></i> Nueva Subcategoría
        </a>
    @endif
</div>
<div class="bg-white rounded-xl shadow overflow-hidden dark:bg-slate-900 dark:shadow-slate-950/50">
    <table class="w-full">
        <thead class="bg-gray-50 dark:bg-slate-800">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Destino</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Categoría</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Subcategoría</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Itinerarios</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
            @forelse($subcategorias as $subcategoria)
            <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/60">
                <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-400">{{ $subcategoria->categoria->destino->nombre }}</td>
                <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-400">{{ $subcategoria->categoria->nombre }}</td>
                <td class="px-6 py-4 text-sm font-medium text-gray-800 dark:text-slate-100">{{ $subcategoria->nombre }}</td>
                <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-400">{{ $subcategoria->itineraries_count }}</td>
                <td class="px-6 py-4 text-right text-sm">
                    <a href="{{ route('subcategorias.edit', $subcategoria) }}" class="text-blue-600 hover:text-blue-800 mr-3 dark:text-blue-400 dark:hover:text-blue-300"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('subcategorias.destroy', $subcategoria) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar esta subcategoría?')">
                        @csrf @method('DELETE')
                        <button class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-10 text-center text-gray-500 dark:text-slate-400">Aún no hay subcategorías registradas.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
