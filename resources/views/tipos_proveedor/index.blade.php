@extends('layouts.app')
@section('title', 'Tipos de Proveedor')
@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-slate-100">Tipos de Proveedor</h1>
        <a href="{{ route('proveedores.index') }}" class="text-sm text-amber-600 hover:underline dark:text-amber-400"><i class="fas fa-arrow-left"></i> Volver a Proveedores</a>
    </div>
    <a href="{{ route('tipos-proveedor.create') }}" class="bg-amber-600 text-white px-4 py-2 rounded-lg hover:bg-amber-700 transition">
        <i class="fas fa-plus mr-1"></i> Nuevo Tipo
    </a>
</div>
<div class="bg-white rounded-xl shadow overflow-hidden dark:bg-slate-900 dark:shadow-slate-950/50">
    <table class="w-full">
        <thead class="bg-gray-50 dark:bg-slate-800">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Nombre</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Proveedores</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
            @forelse($tipos as $tipo)
            <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/60">
                <td class="px-6 py-4 text-sm font-medium text-gray-800 dark:text-slate-100">{{ $tipo->nombre }}</td>
                <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-400">{{ $tipo->proveedores_count }}</td>
                <td class="px-6 py-4 text-right text-sm">
                    <a href="{{ route('tipos-proveedor.edit', $tipo) }}" class="text-blue-600 hover:text-blue-800 mr-3 dark:text-blue-400 dark:hover:text-blue-300"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('tipos-proveedor.destroy', $tipo) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar este tipo de proveedor?')">
                        @csrf @method('DELETE')
                        <button class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="px-6 py-10 text-center text-gray-500 dark:text-slate-400">Aún no hay tipos de proveedor registrados.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
