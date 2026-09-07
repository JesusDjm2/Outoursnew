@extends('layouts.app')
@section('title', 'Destinos y Categorías')
@section('content')
@include('partials._cotizador_tabs', ['cotizadorActive' => 'destinos'])
@include('categorias._tabs', ['active' => 'destinos'])

<div class="flex justify-end mb-4">
    <button type="button" id="btn-nuevo-destino" class="bg-blue-600 text-white px-4 py-1.5 rounded-lg hover:bg-blue-700 transition text-sm font-medium">
        <i class="fas fa-plus mr-1"></i> Nuevo Destino
    </button>
</div>

<div class="space-y-3">
    @forelse($destinos as $destino)
    <details class="group bg-white rounded-xl shadow overflow-hidden dark:bg-slate-900 dark:shadow-slate-950/50">
        <summary class="list-none cursor-pointer flex items-center justify-between gap-3 px-5 py-4 hover:bg-gray-50 dark:hover:bg-slate-800/60 [&::-webkit-details-marker]:hidden">
            <div class="flex items-center gap-3 min-w-0">
                <i class="fas fa-chevron-right text-gray-400 text-xs transition-transform duration-200 group-open:rotate-90 dark:text-slate-500"></i>
                <span class="font-semibold text-gray-800 dark:text-slate-100 truncate">{{ $destino->nombre }}</span>
                <span class="shrink-0 rounded-full bg-blue-50 text-blue-700 text-xs font-medium px-2.5 py-1 dark:bg-blue-900/30 dark:text-blue-300">
                    {{ $destino->categorias_count }} {{ $destino->categorias_count == 1 ? 'categoría' : 'categorías' }}
                </span>
            </div>
            <div class="flex items-center gap-3 shrink-0" onclick="event.stopPropagation();">
                <button type="button" class="js-edit-destino text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                    data-id="{{ $destino->id }}" data-nombre="{{ $destino->nombre }}" title="Editar destino">
                    <i class="fas fa-edit"></i>
                </button>
                <form action="{{ route('destinos.destroy', $destino) }}" method="POST" onsubmit="return swalConfirmSubmit(event, '¿Eliminar este destino?')">
                    @csrf @method('DELETE')
                    <button class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300" title="Eliminar destino"><i class="fas fa-trash"></i></button>
                </form>
            </div>
        </summary>
        <div class="border-t border-gray-100 px-5 py-4 dark:border-slate-800">
            @if($destino->categorias->isEmpty())
                <p class="text-sm text-gray-400 mb-3 dark:text-slate-500">Sin categorías todavía.</p>
            @else
                <ul class="divide-y divide-gray-100 mb-3 dark:divide-slate-800">
                    @foreach($destino->categorias as $categoria)
                    <li class="flex items-center justify-between py-2.5 px-2 -mx-2 rounded-lg transition hover:bg-gray-50 dark:hover:bg-slate-800/60">
                        <span class="text-sm text-gray-700 dark:text-slate-300">{{ $categoria->nombre }}</span>
                        <div class="flex items-center gap-3">
                            <button type="button" class="js-edit-categoria text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                data-id="{{ $categoria->id }}" data-nombre="{{ $categoria->nombre }}" data-destino-id="{{ $categoria->destino_id }}" title="Editar categoría">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('categorias.destroy', $categoria) }}" method="POST" onsubmit="return swalConfirmSubmit(event, '¿Eliminar esta categoría?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300" title="Eliminar categoría"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </li>
                    @endforeach
                </ul>
            @endif
            <button type="button" class="js-add-categoria inline-flex items-center gap-1.5 text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                data-destino-id="{{ $destino->id }}">
                <i class="fas fa-plus"></i> Agregar categoría
            </button>
        </div>
    </details>
    @empty
    <div class="bg-white rounded-xl shadow px-6 py-10 text-center text-gray-500 dark:bg-slate-900 dark:shadow-slate-950/50 dark:text-slate-400">
        Aún no hay destinos registrados.
    </div>
    @endforelse
</div>

<div class="mt-4">
    {{ $destinos->links() }}
</div>

{{-- Modal: Destino (crear/editar) --}}
<div id="destino-modal" class="fixed inset-0 z-50 hidden">
    <div id="destino-modal-backdrop" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
    <div class="relative flex min-h-full items-center justify-center p-4 pointer-events-none">
        <div role="dialog" aria-modal="true" aria-labelledby="destino-modal-title"
             class="pointer-events-auto relative w-full max-w-md bg-white rounded-2xl shadow-2xl dark:bg-slate-900">
            <div class="flex items-start justify-between gap-4 p-5 border-b border-gray-100 dark:border-slate-800">
                <h2 id="destino-modal-title" class="text-lg font-bold text-gray-800 dark:text-slate-100">Nuevo Destino</h2>
                <button type="button" id="destino-modal-close" aria-label="Cerrar" class="text-gray-400 hover:text-gray-600 dark:text-slate-500 dark:hover:text-slate-300">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>
            <form id="destino-form" method="POST" action="{{ route('destinos.store') }}" class="p-5 space-y-4">
                @csrf
                <input type="hidden" name="_method" id="destino-method" value="PUT" disabled>
                <input type="hidden" name="_editing_id" id="destino-editing-id" value="">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">Nombre</label>
                    <input type="text" name="nombre" id="destino-nombre" required placeholder="Ej. Cusco"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">Guardar</button>
                    <button type="button" id="destino-modal-cancel" class="bg-gray-300 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-400 transition dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Categoría (crear/editar) --}}
<div id="categoria-modal" class="fixed inset-0 z-50 hidden">
    <div id="categoria-modal-backdrop" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
    <div class="relative flex min-h-full items-center justify-center p-4 pointer-events-none">
        <div role="dialog" aria-modal="true" aria-labelledby="categoria-modal-title"
             class="pointer-events-auto relative w-full max-w-md bg-white rounded-2xl shadow-2xl dark:bg-slate-900">
            <div class="flex items-start justify-between gap-4 p-5 border-b border-gray-100 dark:border-slate-800">
                <h2 id="categoria-modal-title" class="text-lg font-bold text-gray-800 dark:text-slate-100">Nueva Categoría</h2>
                <button type="button" id="categoria-modal-close" aria-label="Cerrar" class="text-gray-400 hover:text-gray-600 dark:text-slate-500 dark:hover:text-slate-300">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>
            <form id="categoria-form" method="POST" action="{{ route('categorias.store') }}" class="p-5 space-y-4">
                @csrf
                <input type="hidden" name="_method" id="categoria-method" value="PUT" disabled>
                <input type="hidden" name="_editing_id" id="categoria-editing-id" value="">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">Destino</label>
                    <select name="destino_id" id="categoria-destino" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
                        <option value="">Selecciona un destino...</option>
                        @foreach($destinos as $destino)
                            <option value="{{ $destino->id }}">{{ $destino->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">Nombre</label>
                    <input type="text" name="nombre" id="categoria-nombre" required placeholder="Ej. City Tour"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">Guardar</button>
                    <button type="button" id="categoria-modal-cancel" class="bg-gray-300 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-400 transition dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const destinoUpdateUrlTemplate = "{{ route('destinos.update', ['destino' => '__ID__']) }}";
    const categoriaUpdateUrlTemplate = "{{ route('categorias.update', ['categoria' => '__ID__']) }}";

    // --- Modal Destino ---
    const destinoModal = document.getElementById('destino-modal');
    const destinoForm = document.getElementById('destino-form');
    const destinoStoreUrl = destinoForm.action;
    const destinoTitle = document.getElementById('destino-modal-title');
    const destinoNombre = document.getElementById('destino-nombre');
    const destinoMethod = document.getElementById('destino-method');
    const destinoEditingId = document.getElementById('destino-editing-id');

    function openDestinoModal() {
        destinoModal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
    function closeDestinoModal() {
        destinoModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
    function destinoCreateMode() {
        destinoTitle.textContent = 'Nuevo Destino';
        destinoForm.action = destinoStoreUrl;
        destinoMethod.disabled = true;
        destinoEditingId.value = '';
    }
    function destinoEditMode(id, nombre) {
        destinoTitle.textContent = 'Editar Destino';
        destinoForm.action = destinoUpdateUrlTemplate.replace('__ID__', id);
        destinoMethod.disabled = false;
        destinoEditingId.value = id;
        destinoNombre.value = nombre;
    }

    document.getElementById('btn-nuevo-destino')?.addEventListener('click', () => {
        destinoCreateMode();
        destinoNombre.value = '';
        openDestinoModal();
    });
    document.querySelectorAll('.js-edit-destino').forEach(btn => {
        btn.addEventListener('click', () => {
            destinoEditMode(btn.dataset.id, btn.dataset.nombre);
            openDestinoModal();
        });
    });
    document.getElementById('destino-modal-close')?.addEventListener('click', closeDestinoModal);
    document.getElementById('destino-modal-cancel')?.addEventListener('click', closeDestinoModal);
    document.getElementById('destino-modal-backdrop')?.addEventListener('click', closeDestinoModal);

    // --- Modal Categoría ---
    const categoriaModal = document.getElementById('categoria-modal');
    const categoriaForm = document.getElementById('categoria-form');
    const categoriaStoreUrl = categoriaForm.action;
    const categoriaTitle = document.getElementById('categoria-modal-title');
    const categoriaNombre = document.getElementById('categoria-nombre');
    const categoriaDestino = document.getElementById('categoria-destino');
    const categoriaMethod = document.getElementById('categoria-method');
    const categoriaEditingId = document.getElementById('categoria-editing-id');

    function openCategoriaModal() {
        categoriaModal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
    function closeCategoriaModal() {
        categoriaModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
    function categoriaCreateMode(destinoId) {
        categoriaTitle.textContent = 'Nueva Categoría';
        categoriaForm.action = categoriaStoreUrl;
        categoriaMethod.disabled = true;
        categoriaEditingId.value = '';
        categoriaNombre.value = '';
        categoriaDestino.value = destinoId || '';
    }
    function categoriaEditMode(id, nombre, destinoId) {
        categoriaTitle.textContent = 'Editar Categoría';
        categoriaForm.action = categoriaUpdateUrlTemplate.replace('__ID__', id);
        categoriaMethod.disabled = false;
        categoriaEditingId.value = id;
        categoriaNombre.value = nombre;
        categoriaDestino.value = destinoId;
    }

    document.querySelectorAll('.js-add-categoria').forEach(btn => {
        btn.addEventListener('click', () => {
            categoriaCreateMode(btn.dataset.destinoId);
            openCategoriaModal();
        });
    });
    document.querySelectorAll('.js-edit-categoria').forEach(btn => {
        btn.addEventListener('click', () => {
            categoriaEditMode(btn.dataset.id, btn.dataset.nombre, btn.dataset.destinoId);
            openCategoriaModal();
        });
    });
    document.getElementById('categoria-modal-close')?.addEventListener('click', closeCategoriaModal);
    document.getElementById('categoria-modal-cancel')?.addEventListener('click', closeCategoriaModal);
    document.getElementById('categoria-modal-backdrop')?.addEventListener('click', closeCategoriaModal);

    document.addEventListener('keydown', (e) => {
        if (e.key !== 'Escape') return;
        if (!destinoModal.classList.contains('hidden')) closeDestinoModal();
        if (!categoriaModal.classList.contains('hidden')) closeCategoriaModal();
    });

    // Si la validación falló, reabrimos el modal correspondiente con lo que el usuario había escrito.
    @if ($errors->any())
        @if (old('destino_id') !== null)
            @if (old('_editing_id'))
                categoriaEditMode('{{ old('_editing_id') }}', @json(old('nombre')), '{{ old('destino_id') }}');
            @else
                categoriaCreateMode('{{ old('destino_id') }}');
                categoriaNombre.value = @json(old('nombre'));
            @endif
            openCategoriaModal();
        @elseif (old('nombre') !== null)
            @if (old('_editing_id'))
                destinoEditMode('{{ old('_editing_id') }}', @json(old('nombre')));
            @else
                destinoCreateMode();
                destinoNombre.value = @json(old('nombre'));
            @endif
            openDestinoModal();
        @endif
    @endif
});
</script>
@endsection
