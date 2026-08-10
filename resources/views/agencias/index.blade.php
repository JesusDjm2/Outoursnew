@extends('layouts.app')
@section('title', 'Agencias')
@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6 dark:text-slate-100">Agencias</h1>

<div class="bg-white rounded-xl shadow p-6 mb-6 dark:bg-slate-900 dark:shadow-slate-950/50">
    <h2 class="font-semibold text-gray-800 mb-4 dark:text-slate-100"><i class="fas fa-clock-rotate-left text-cyan-600 mr-1"></i> Actividad reciente</h2>
    <div class="space-y-2">
        @forelse($recentTours as $tour)
        <div class="flex items-center justify-between rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-800">
            <div>
                <p class="font-medium text-gray-800 dark:text-slate-100">{{ $tour->nombre }} <span class="text-gray-400 font-normal dark:text-slate-500">({{ $tour->codigo }})</span></p>
                <p class="text-sm text-gray-500 dark:text-slate-400">
                    Creado por
                    <span class="font-medium">{{ $tour->agencia && $tour->agencia->hasRole('Agencia') ? $tour->agencia->name : 'Administrador' }}</span>
                </p>
            </div>
            <span class="text-xs text-gray-400 dark:text-slate-500">{{ $tour->created_at->diffForHumans() }}</span>
        </div>
        @empty
        <p class="text-sm text-gray-500 dark:text-slate-400">Aún no hay tours registrados.</p>
        @endforelse
    </div>
</div>

<div class="bg-white rounded-xl shadow p-6 dark:bg-slate-900 dark:shadow-slate-950/50">
    <h2 class="font-semibold text-gray-800 mb-4 dark:text-slate-100"><i class="fas fa-building text-cyan-600 mr-1"></i> Listado de agencias</h2>

    <form method="GET" action="{{ route('agencias.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
        <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por nombre..."
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
        <input type="text" name="ruc" value="{{ $ruc }}" placeholder="Buscar por RUC..."
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
        <input type="text" name="telefono" value="{{ $telefono }}" placeholder="Buscar por teléfono..."
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
        <button type="submit" class="sm:col-span-3 justify-self-start bg-cyan-600 text-white px-4 py-2 rounded-lg hover:bg-cyan-700 transition text-sm">
            <i class="fas fa-filter mr-1"></i> Filtrar
        </button>
    </form>

    <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-slate-800">
        <table class="w-full">
            <thead class="bg-gray-50 dark:bg-slate-800">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">RUC</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Teléfono</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Tours</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
                @forelse($agencias as $agencia)
                <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/60">
                    <td class="px-6 py-4 text-sm font-medium text-gray-800 dark:text-slate-100">{{ $agencia->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-400">{{ $agencia->ruc ?? '—' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-400">{{ $agencia->telefono ?? '—' }}</td>
                    <td class="px-6 py-4 text-right text-sm text-gray-600 dark:text-slate-400">{{ $agencia->tours_count }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center text-gray-500 dark:text-slate-400">No se encontraron agencias.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($agencias->hasPages())
    <div class="mt-4">
        {{ $agencias->links() }}
    </div>
    @endif
</div>
@endsection
