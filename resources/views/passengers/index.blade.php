@extends('layouts.app')
@section('title', 'Pasajeros')
@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-slate-100">Pasajeros</h1>
    <p class="text-sm text-gray-500 mt-1 dark:text-slate-400">Los pasajeros se crean y editan desde el formulario de cada Tour. Aquí solo puedes consultarlos.</p>
</div>
<div class="bg-white rounded-xl shadow overflow-hidden dark:bg-slate-900 dark:shadow-slate-950/50">
    <table class="w-full">
        <thead class="bg-gray-50 dark:bg-slate-800">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Nombre</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Edad</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Correo</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Tour</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Habitaciones</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">PDF</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
            @forelse($passengers as $passenger)
            <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/60">
                <td class="px-6 py-4 text-sm text-gray-800 dark:text-slate-100">{{ $passenger->nombre }}</td>
                <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-400">{{ $passenger->edad }}</td>
                <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-400">{{ $passenger->correo }}</td>
                <td class="px-6 py-4 text-sm">
                    @if($passenger->tour)
                        <a href="{{ route('tours.show', $passenger->tour) }}" class="text-cyan-600 hover:underline dark:text-cyan-400">{{ $passenger->tour->nombre }}</a>
                    @else
                        <span class="text-gray-400 dark:text-slate-500">Sin tour asignado</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-400">
                    @forelse($passenger->asignaciones->whereNotNull('room_id') as $asignacion)
                        <span class="inline-block rounded-full bg-indigo-50 text-indigo-700 text-[11px] px-2 py-1 mb-1 dark:bg-indigo-900/40 dark:text-indigo-300">
                            {{ $asignacion->room->hotel->nombre }} · {{ $asignacion->room->nombre }} #{{ $asignacion->room->numero_habitacion }}
                        </span><br>
                    @empty
                        <span class="text-gray-400 dark:text-slate-500">Sin asignar</span>
                    @endforelse
                </td>
                <td class="px-6 py-4 text-sm">
                    @if($passenger->pdf_path)
                    <a href="{{ asset('storage/' . $passenger->pdf_path) }}" target="_blank" class="text-blue-600 hover:underline dark:text-blue-400"><i class="fas fa-file-pdf"></i> Ver</a>
                    @else
                    <span class="text-gray-400 dark:text-slate-500">-</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-10 text-center text-gray-500 dark:text-slate-400">Aún no hay pasajeros registrados.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
