@extends('layouts.app')
@section('title', 'Registro de Cotizaciones')
@section('content')
@include('partials._cotizador_tabs', ['cotizadorActive' => 'registros'])
<div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-4">
    <form method="GET" action="{{ route('tours.index') }}" class="flex flex-1 gap-2 max-w-lg">
        <div class="relative flex-1">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-slate-500"></i>
            <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por código, nombre, pax o agente..."
                   class="w-full pl-10 {{ $q ? 'pr-9' : 'pr-3' }} py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
            @if($q)
            <a href="{{ route('tours.index') }}" title="Limpiar búsqueda"
               class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:text-slate-500 dark:hover:text-slate-300">
                <i class="fas fa-xmark"></i>
            </a>
            @endif
        </div>
        <button type="submit" class="shrink-0 bg-blue-600 text-white px-4 py-1.5 rounded-lg hover:bg-blue-700 transition text-sm font-medium">
            <i class="fas fa-search mr-1"></i> Buscar
        </button>
    </form>
    <button type="button" id="btn-nuevo-tour" class="shrink-0 sm:ml-auto bg-blue-600 text-white px-4 py-1.5 rounded-lg hover:bg-blue-700 transition text-sm font-medium">
        <i class="fas fa-plus mr-1"></i> Nuevo Tour
    </button>
</div>

<div id="nuevo-tour-modal" class="fixed inset-0 z-50 hidden">
    <div id="nuevo-tour-backdrop" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
    <div class="relative flex min-h-full items-center justify-center p-4 pointer-events-none">
        <div role="dialog" aria-modal="true" aria-labelledby="nuevo-tour-modal-title"
             class="pointer-events-auto relative w-full max-w-lg max-h-[90vh] overflow-y-auto bg-white rounded-2xl shadow-2xl dark:bg-slate-900">
            <div class="flex items-start justify-between gap-4 p-5 border-b border-gray-100 dark:border-slate-800">
                <div>
                    <h2 id="nuevo-tour-modal-title" class="text-lg font-bold text-gray-800 dark:text-slate-100">Nueva cotización</h2>
                    <p class="text-sm text-gray-500 mt-0.5 dark:text-slate-400">¿Cómo quieres armar esta cotización?</p>
                </div>
                <button type="button" id="nuevo-tour-close" aria-label="Cerrar"
                        class="shrink-0 -m-1 p-1 text-gray-400 hover:text-gray-600 dark:text-slate-500 dark:hover:text-slate-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="p-5">
                <div class="rounded-xl border-2 border-purple-500 bg-purple-50/60 p-4 dark:border-purple-500/70 dark:bg-purple-950/20">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 shrink-0 rounded-full bg-purple-600 text-white flex items-center justify-center">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-semibold text-gray-800 dark:text-slate-100">Usar un paquete predefinido</h3>
                            <p class="text-xs text-gray-500 dark:text-slate-400">Empieza con las actividades ya cargadas.</p>
                        </div>
                    </div>

                    @if($paquetes->isEmpty())
                        <p class="text-sm text-gray-500 dark:text-slate-400">Aún no hay paquetes creados. <a href="{{ route('itinerary-packages.create') }}" class="text-purple-600 underline dark:text-purple-400">Crear uno</a>.</p>
                    @else
                        <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                            @foreach($paquetes as $paquete)
                                <a href="{{ route('tours.create', ['paquete' => $paquete->id]) }}"
                                   class="flex items-center justify-between gap-3 bg-white border border-gray-200 rounded-lg p-3 hover:border-purple-500 hover:bg-purple-50 transition dark:bg-slate-900 dark:border-slate-700 dark:hover:bg-purple-950/30">
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-800 truncate dark:text-slate-100">{{ $paquete->nombre }}</p>
                                        @if($paquete->descripcion)
                                            <p class="text-xs text-gray-400 truncate dark:text-slate-500">{{ Str::limit($paquete->descripcion, 60) }}</p>
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

                <div class="mt-4 text-center">
                    <a href="{{ route('tours.create') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 hover:underline dark:text-slate-400 dark:hover:text-slate-200">
                        <i class="fas fa-file-circle-plus"></i> o arma la cotización desde cero
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="bg-white rounded-xl shadow overflow-hidden dark:bg-slate-900 dark:shadow-slate-950/50">
    <table class="w-full">
        <thead class="bg-gray-50 dark:bg-slate-800">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Código</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Pax</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Agente</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Inicio</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Fin</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
            @foreach($tours as $tour)
            <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/60">
                <td class="px-6 py-4 text-sm font-mono text-gray-600 dark:text-slate-400">{{ $tour->codigo }}</td>
                <td class="px-6 py-4 text-sm text-gray-800 font-medium dark:text-slate-100">{{ $tour->nombre_pax ?: '—' }}</td>
                <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-400">{{ $tour->agente ?: '—' }}</td>
                <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-400">{{ $tour->fecha_inicio ?? '—' }}</td>
                <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-400">{{ $tour->fecha_fin ?? '—' }}</td>
                <td class="px-6 py-4 text-right text-sm">
                    <a href="{{ route('tours.show', $tour) }}" class="text-emerald-600 hover:text-emerald-800 mr-3 dark:text-emerald-400 dark:hover:text-emerald-300" title="Ver"><i class="fas fa-eye"></i></a>
                    <a href="{{ route('tours.pdf', $tour) }}" target="_blank" class="text-rose-600 hover:text-rose-800 mr-3 dark:text-rose-400 dark:hover:text-rose-300" title="Ver / descargar PDF de cotización"><i class="fas fa-file-pdf"></i></a>
                    <a href="{{ route('tours.itinerario-pdf', $tour) }}" target="_blank" class="text-purple-600 hover:text-purple-800 mr-3 dark:text-purple-400 dark:hover:text-purple-300" title="Descargar itinerario de viaje"><i class="fas fa-book-open"></i></a>
                    <a href="{{ route('tours.edit', $tour) }}" class="text-blue-600 hover:text-blue-800 mr-3 dark:text-blue-400 dark:hover:text-blue-300"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('tours.duplicate', $tour) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-amber-600 hover:text-amber-800 mr-3 dark:text-amber-400 dark:hover:text-amber-300" title="Duplicar cotización"><i class="fas fa-copy"></i></button>
                    </form>
                    <form action="{{ route('tours.destroy', $tour) }}" method="POST" class="inline" onsubmit="return swalConfirmSubmit(event, '¿Eliminar este tour?')">
                        @csrf @method('DELETE')
                        <button class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
            @if($tours->isEmpty())
            <tr>
                <td colspan="6" class="px-6 py-10 text-center text-gray-500 dark:text-slate-400">
                    {{ $q ? 'No se encontraron cotizaciones para "' . $q . '".' : 'Aún no hay cotizaciones registradas.' }}
                </td>
            </tr>
            @endif
        </tbody>
    </table>
</div>

@if($tours->hasPages())
<div class="mt-4">
    {{ $tours->links() }}
</div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const openBtn = document.getElementById('btn-nuevo-tour');
    const modal = document.getElementById('nuevo-tour-modal');
    const backdrop = document.getElementById('nuevo-tour-backdrop');
    const closeBtn = document.getElementById('nuevo-tour-close');
    if (!openBtn || !modal) return;

    function openModal() {
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    openBtn.addEventListener('click', openModal);
    closeBtn?.addEventListener('click', closeModal);
    backdrop?.addEventListener('click', closeModal);
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
    });
});
</script>
@endpush
@endsection
