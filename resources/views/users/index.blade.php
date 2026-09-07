@extends('layouts.app')
@section('title', 'Usuarios')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-slate-100">Usuarios</h1>
    <a href="{{ route('users.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
        <i class="fas fa-plus mr-1"></i> Nuevo Usuario
    </a>
</div>
<div class="bg-white rounded-xl shadow overflow-hidden dark:bg-slate-900 dark:shadow-slate-950/50">
    <table class="w-full">
        <thead class="bg-gray-50 dark:bg-slate-800">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Nombre</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Rol</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
            @foreach($users as $user)
            <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/60">
                <td class="px-6 py-4 text-sm text-gray-800 dark:text-slate-100">{{ $user->name }}</td>
                <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-400">{{ $user->email }}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                        {{ $user->getRoleNames()->first() }}
                    </span>
                </td>
                <td class="px-6 py-4 text-right text-sm">
                    <a href="{{ route('users.edit', $user) }}" class="text-blue-600 hover:text-blue-800 mr-3 dark:text-blue-400 dark:hover:text-blue-300"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" onsubmit="return swalConfirmSubmit(event, '¿Eliminar este usuario?')">
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
