@extends('layouts.app')
@section('title', 'Proveedores')
@section('content')

@php
    $highlight = function (?string $text) use ($q) {
        if ($text === null || $text === '') {
            return $text;
        }
        $escaped = e($text);
        if (!$q) {
            return $escaped;
        }
        $needle = preg_quote(e($q), '/');
        return preg_replace(
            '/' . $needle . '/iu',
            '<mark class="rounded bg-amber-200 px-0.5 text-amber-900 dark:bg-amber-500/40 dark:text-amber-100">$0</mark>',
            $escaped
        );
    };
@endphp

<div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-4">
    <form method="GET" action="{{ route('proveedores.index') }}" class="flex flex-1 gap-2 max-w-lg">
        <div class="relative flex-1">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-slate-500"></i>
            <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por nombre, correo, teléfono o dirección..."
                   class="w-full pl-10 {{ $q ? 'pr-9' : 'pr-3' }} py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
            @if($q)
            <a href="{{ route('proveedores.index') }}" title="Limpiar búsqueda"
               class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:text-slate-500 dark:hover:text-slate-300">
                <i class="fas fa-xmark"></i>
            </a>
            @endif
        </div>
        <button type="submit" class="shrink-0 bg-amber-600 text-white px-4 py-1.5 rounded-lg hover:bg-amber-700 transition text-sm font-medium">
            <i class="fas fa-search mr-1"></i> Buscar
        </button>
    </form>
    <div class="flex gap-2 shrink-0 sm:ml-auto">
        <button type="button" id="btn-nuevo-tipo" class="bg-white border border-amber-300 text-amber-700 px-4 py-1.5 rounded-lg hover:bg-amber-50 transition text-sm font-medium dark:bg-slate-900 dark:border-amber-800 dark:text-amber-300 dark:hover:bg-amber-950/30">
            <i class="fas fa-tags mr-1"></i> Nuevo Tipo
        </button>
        <button type="button" id="btn-nuevo-proveedor" class="bg-amber-600 text-white px-4 py-1.5 rounded-lg hover:bg-amber-700 transition text-sm font-medium">
            <i class="fas fa-plus mr-1"></i> Nuevo Proveedor
        </button>
    </div>
</div>

<div class="space-y-3">
    @forelse($tiposProveedor as $tipo)
    <details {{ $q ? 'open' : '' }} class="group bg-white rounded-xl shadow overflow-hidden dark:bg-slate-900 dark:shadow-slate-950/50">
        <summary class="list-none cursor-pointer flex items-center justify-between gap-3 px-5 py-3.5 hover:bg-gray-50 dark:hover:bg-slate-800/60 [&::-webkit-details-marker]:hidden">
            <div class="flex items-center gap-3 min-w-0">
                <i class="fas fa-chevron-right text-gray-400 text-xs transition-transform duration-200 group-open:rotate-90 dark:text-slate-500 shrink-0"></i>
                <span class="font-semibold text-gray-800 dark:text-slate-100 truncate">{{ $tipo->nombre }}</span>
                <span class="shrink-0 rounded-full bg-amber-50 text-amber-700 text-xs font-medium px-2.5 py-1 dark:bg-amber-900/30 dark:text-amber-300">
                    {{ $tipo->proveedores_count }} {{ $tipo->proveedores_count == 1 ? 'proveedor' : 'proveedores' }}
                </span>
            </div>
            <div class="flex items-center gap-3 shrink-0" onclick="event.stopPropagation();">
                <button type="button" class="js-edit-tipo text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                    data-id="{{ $tipo->id }}" data-nombre="{{ $tipo->nombre }}" title="Editar tipo">
                    <i class="fas fa-edit"></i>
                </button>
                <form action="{{ route('tipos-proveedor.destroy', $tipo) }}" method="POST" onsubmit="return swalConfirmSubmit(event, '¿Eliminar este tipo de proveedor?')">
                    @csrf @method('DELETE')
                    <button class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300" title="Eliminar tipo"><i class="fas fa-trash"></i></button>
                </form>
            </div>
        </summary>
        <div class="border-t border-gray-100 px-5 py-4 dark:border-slate-800">
            @if($tipo->proveedores->isEmpty())
                <p class="text-sm text-gray-400 mb-3 dark:text-slate-500">Sin proveedores todavía.</p>
            @else
                <ul class="divide-y divide-gray-100 mb-3 dark:divide-slate-800">
                    @foreach($tipo->proveedores as $proveedor)
                    <li class="flex items-center gap-3 py-2.5 px-2 -mx-2 rounded-lg transition hover:bg-gray-50 dark:hover:bg-slate-800/60">
                        <div class="h-9 w-9 rounded-lg overflow-hidden bg-gray-100 shrink-0 flex items-center justify-center dark:bg-slate-800">
                            @if($proveedor->imagenes->isNotEmpty())
                                <img src="{{ asset('storage/' . $proveedor->imagenes->first()->path) }}" class="h-full w-full object-cover">
                            @else
                                <i class="fas fa-industry text-gray-300 text-sm dark:text-slate-600"></i>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-medium text-gray-800 truncate dark:text-slate-100">{!! $highlight($proveedor->nombre) !!}</p>
                                @if($q)
                                <span class="shrink-0 rounded-full bg-amber-50 text-amber-700 text-[10px] font-medium px-2 py-0.5 dark:bg-amber-900/40 dark:text-amber-300">{{ $tipo->nombre }}</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-400 truncate dark:text-slate-500">
                                {!! $highlight($proveedor->email) !!}{{ $proveedor->email && $proveedor->telefono ? ' · ' : '' }}{!! $highlight($proveedor->telefono) !!}
                            </p>
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            <button type="button" class="js-edit-proveedor text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                data-id="{{ $proveedor->id }}" data-tipo-id="{{ $proveedor->tipo_id }}" data-nombre="{{ $proveedor->nombre }}"
                                data-email="{{ $proveedor->email }}" data-telefono="{{ $proveedor->telefono }}" data-direccion="{{ $proveedor->direccion }}"
                                data-imagenes='@json($proveedor->imagenes->map(fn ($img) => ["id" => $img->id, "url" => asset("storage/" . $img->path)]))'
                                title="Editar proveedor">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('proveedores.destroy', $proveedor) }}" method="POST" onsubmit="return swalConfirmSubmit(event, '¿Eliminar este proveedor?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300" title="Eliminar proveedor"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </li>
                    @endforeach
                </ul>
            @endif
            <button type="button" class="js-add-proveedor inline-flex items-center gap-1.5 text-sm font-medium text-amber-600 hover:text-amber-800 dark:text-amber-400 dark:hover:text-amber-300"
                data-tipo-id="{{ $tipo->id }}">
                <i class="fas fa-plus"></i> Agregar proveedor
            </button>
        </div>
    </details>
    @empty
        @if(!$q || $proveedoresSinTipo->isEmpty())
        <div class="bg-white rounded-xl shadow px-6 py-10 text-center text-gray-500 dark:bg-slate-900 dark:shadow-slate-950/50 dark:text-slate-400">
            {{ $q ? 'No se encontraron proveedores para "' . $q . '".' : 'Aún no hay tipos de proveedor registrados.' }}
        </div>
        @endif
    @endforelse

    @if($proveedoresSinTipo->isNotEmpty())
    <details {{ $q ? 'open' : '' }} class="group bg-white rounded-xl shadow overflow-hidden dark:bg-slate-900 dark:shadow-slate-950/50">
        <summary class="list-none cursor-pointer flex items-center gap-3 px-5 py-3.5 hover:bg-gray-50 dark:hover:bg-slate-800/60 [&::-webkit-details-marker]:hidden">
            <i class="fas fa-chevron-right text-gray-400 text-xs transition-transform duration-200 group-open:rotate-90 dark:text-slate-500 shrink-0"></i>
            <span class="font-semibold text-gray-800 dark:text-slate-100">Sin tipo</span>
            <span class="shrink-0 rounded-full bg-gray-100 text-gray-600 text-xs font-medium px-2.5 py-1 dark:bg-slate-800 dark:text-slate-300">
                {{ $proveedoresSinTipo->count() }}
            </span>
        </summary>
        <div class="border-t border-gray-100 px-5 py-4 dark:border-slate-800">
            <ul class="divide-y divide-gray-100 dark:divide-slate-800">
                @foreach($proveedoresSinTipo as $proveedor)
                <li class="flex items-center gap-3 py-2.5 px-2 -mx-2 rounded-lg transition hover:bg-gray-50 dark:hover:bg-slate-800/60">
                    <div class="h-9 w-9 rounded-lg overflow-hidden bg-gray-100 shrink-0 flex items-center justify-center dark:bg-slate-800">
                        @if($proveedor->imagenes->isNotEmpty())
                            <img src="{{ asset('storage/' . $proveedor->imagenes->first()->path) }}" class="h-full w-full object-cover">
                        @else
                            <i class="fas fa-industry text-gray-300 text-sm dark:text-slate-600"></i>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-medium text-gray-800 truncate dark:text-slate-100">{!! $highlight($proveedor->nombre) !!}</p>
                            @if($q)
                            <span class="shrink-0 rounded-full bg-gray-100 text-gray-600 text-[10px] font-medium px-2 py-0.5 dark:bg-slate-800 dark:text-slate-300">Sin tipo</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-400 truncate dark:text-slate-500">
                            {!! $highlight($proveedor->email) !!}{{ $proveedor->email && $proveedor->telefono ? ' · ' : '' }}{!! $highlight($proveedor->telefono) !!}
                        </p>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <button type="button" class="js-edit-proveedor text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                            data-id="{{ $proveedor->id }}" data-tipo-id="{{ $proveedor->tipo_id }}" data-nombre="{{ $proveedor->nombre }}"
                            data-email="{{ $proveedor->email }}" data-telefono="{{ $proveedor->telefono }}" data-direccion="{{ $proveedor->direccion }}"
                            data-imagenes='@json($proveedor->imagenes->map(fn ($img) => ["id" => $img->id, "url" => asset("storage/" . $img->path)]))'
                            title="Editar proveedor">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('proveedores.destroy', $proveedor) }}" method="POST" onsubmit="return swalConfirmSubmit(event, '¿Eliminar este proveedor?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300" title="Eliminar proveedor"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
    </details>
    @endif
</div>

<div class="mt-4">
    {{ $tiposProveedor->links() }}
</div>

{{-- Modal: Tipo de Proveedor (crear/editar) --}}
<div id="tipo-modal" class="fixed inset-0 z-50 hidden">
    <div id="tipo-modal-backdrop" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
    <div class="relative flex min-h-full items-center justify-center p-4 pointer-events-none">
        <div role="dialog" aria-modal="true" aria-labelledby="tipo-modal-title"
             class="pointer-events-auto relative w-full max-w-md bg-white rounded-2xl shadow-2xl dark:bg-slate-900">
            <div class="flex items-start justify-between gap-4 p-5 border-b border-gray-100 dark:border-slate-800">
                <h2 id="tipo-modal-title" class="text-lg font-bold text-gray-800 dark:text-slate-100">Nuevo Tipo de Proveedor</h2>
                <button type="button" id="tipo-modal-close" aria-label="Cerrar" class="text-gray-400 hover:text-gray-600 dark:text-slate-500 dark:hover:text-slate-300">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>
            <form id="tipo-form" method="POST" action="{{ route('tipos-proveedor.store') }}" class="p-5 space-y-4">
                @csrf
                <input type="hidden" name="_method" id="tipo-method" value="PUT" disabled>
                <input type="hidden" name="_editing_id" id="tipo-editing-id" value="">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Nombre</label>
                    <input type="text" name="nombre" id="tipo-nombre" required
                        class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="bg-amber-600 text-white px-5 py-2 text-sm font-medium rounded-lg hover:bg-amber-700 transition">Guardar</button>
                    <button type="button" id="tipo-modal-cancel" class="bg-gray-300 text-gray-800 px-5 py-2 text-sm font-medium rounded-lg hover:bg-gray-400 transition dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Proveedor (crear/editar) --}}
<div id="proveedor-modal" class="fixed inset-0 z-50 hidden">
    <div id="proveedor-modal-backdrop" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
    <div class="relative flex min-h-full items-center justify-center p-4 pointer-events-none">
        <div role="dialog" aria-modal="true" aria-labelledby="proveedor-modal-title"
             class="pointer-events-auto relative w-full max-w-lg max-h-[90vh] overflow-y-auto bg-white rounded-2xl shadow-2xl dark:bg-slate-900">
            <div class="flex items-start justify-between gap-4 p-5 border-b border-gray-100 dark:border-slate-800">
                <h2 id="proveedor-modal-title" class="text-lg font-bold text-gray-800 dark:text-slate-100">Nuevo Proveedor</h2>
                <button type="button" id="proveedor-modal-close" aria-label="Cerrar" class="text-gray-400 hover:text-gray-600 dark:text-slate-500 dark:hover:text-slate-300">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>
            <form id="proveedor-form" method="POST" action="{{ route('proveedores.store') }}" enctype="multipart/form-data" class="p-5 space-y-3">
                @csrf
                <input type="hidden" name="_method" id="proveedor-method" value="PUT" disabled>
                <input type="hidden" name="_editing_id" id="proveedor-editing-id" value="">

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Nombre</label>
                        <input type="text" name="nombre" id="proveedor-nombre" required
                            class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Tipo</label>
                        <select name="tipo_id" id="proveedor-tipo" required
                            class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
                            <option value="">Seleccionar tipo...</option>
                            @foreach($tiposProveedor as $tipoProveedor)
                                <option value="{{ $tipoProveedor->id }}">{{ $tipoProveedor->nombre }}</option>
                            @endforeach
                        </select>
                        @if($tiposProveedor->isEmpty())
                            <p class="text-[11px] text-gray-400 mt-1 dark:text-slate-400">No hay tipos registrados. Crea uno primero.</p>
                        @endif
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Email</label>
                        <input type="email" name="email" id="proveedor-email"
                            class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Teléfono</label>
                        <input type="text" name="telefono" id="proveedor-telefono"
                            class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Dirección</label>
                    <input type="text" name="direccion" id="proveedor-direccion"
                        class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
                </div>

                <div id="proveedor-galeria-actual" class="hidden">
                    <label class="block text-xs font-medium text-gray-600 mb-2 dark:text-slate-400">Galería actual</label>
                    <div id="proveedor-galeria-grid" class="grid grid-cols-5 gap-2 mb-1"></div>
                    <p class="text-[11px] text-gray-400 dark:text-slate-400">Marca una foto para eliminarla al guardar.</p>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400" id="proveedor-galeria-label">Galería de fotos</label>
                    <input type="file" name="galeria[]" multiple accept="image/*"
                        class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="bg-amber-600 text-white px-5 py-2 text-sm font-medium rounded-lg hover:bg-amber-700 transition">Guardar</button>
                    <button type="button" id="proveedor-modal-cancel" class="bg-gray-300 text-gray-800 px-5 py-2 text-sm font-medium rounded-lg hover:bg-gray-400 transition dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const tipoUpdateUrlTemplate = "{{ route('tipos-proveedor.update', ['tipo' => '__ID__']) }}";
    const proveedorUpdateUrlTemplate = "{{ route('proveedores.update', ['proveedor' => '__ID__']) }}";

    // --- Modal Tipo ---
    const tipoModal = document.getElementById('tipo-modal');
    const tipoForm = document.getElementById('tipo-form');
    const tipoStoreUrl = tipoForm.action;
    const tipoTitle = document.getElementById('tipo-modal-title');
    const tipoNombre = document.getElementById('tipo-nombre');
    const tipoMethod = document.getElementById('tipo-method');
    const tipoEditingId = document.getElementById('tipo-editing-id');

    function openTipoModal() {
        tipoModal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
    function closeTipoModal() {
        tipoModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
    function tipoCreateMode() {
        tipoTitle.textContent = 'Nuevo Tipo de Proveedor';
        tipoForm.action = tipoStoreUrl;
        tipoMethod.disabled = true;
        tipoEditingId.value = '';
        tipoNombre.value = '';
    }
    function tipoEditMode(id, nombre) {
        tipoTitle.textContent = 'Editar Tipo de Proveedor';
        tipoForm.action = tipoUpdateUrlTemplate.replace('__ID__', id);
        tipoMethod.disabled = false;
        tipoEditingId.value = id;
        tipoNombre.value = nombre;
    }

    document.getElementById('btn-nuevo-tipo')?.addEventListener('click', () => {
        tipoCreateMode();
        openTipoModal();
    });
    document.querySelectorAll('.js-edit-tipo').forEach(btn => {
        btn.addEventListener('click', () => {
            tipoEditMode(btn.dataset.id, btn.dataset.nombre);
            openTipoModal();
        });
    });
    document.getElementById('tipo-modal-close')?.addEventListener('click', closeTipoModal);
    document.getElementById('tipo-modal-cancel')?.addEventListener('click', closeTipoModal);
    document.getElementById('tipo-modal-backdrop')?.addEventListener('click', closeTipoModal);

    // --- Modal Proveedor ---
    const proveedorModal = document.getElementById('proveedor-modal');
    const proveedorForm = document.getElementById('proveedor-form');
    const proveedorStoreUrl = proveedorForm.action;
    const proveedorTitle = document.getElementById('proveedor-modal-title');
    const proveedorNombre = document.getElementById('proveedor-nombre');
    const proveedorTipo = document.getElementById('proveedor-tipo');
    const proveedorEmail = document.getElementById('proveedor-email');
    const proveedorTelefono = document.getElementById('proveedor-telefono');
    const proveedorDireccion = document.getElementById('proveedor-direccion');
    const proveedorMethod = document.getElementById('proveedor-method');
    const proveedorEditingId = document.getElementById('proveedor-editing-id');
    const galeriaActual = document.getElementById('proveedor-galeria-actual');
    const galeriaGrid = document.getElementById('proveedor-galeria-grid');
    const galeriaLabel = document.getElementById('proveedor-galeria-label');

    function openProveedorModal() {
        proveedorModal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
    function closeProveedorModal() {
        proveedorModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
    function renderGaleria(imagenes) {
        galeriaGrid.innerHTML = '';
        if (!imagenes || imagenes.length === 0) {
            galeriaActual.classList.add('hidden');
            galeriaLabel.textContent = 'Galería de fotos';
            return;
        }
        galeriaActual.classList.remove('hidden');
        galeriaLabel.textContent = 'Agregar más fotos';
        imagenes.forEach(img => {
            const label = document.createElement('label');
            label.className = 'relative block cursor-pointer group';
            label.innerHTML = `
                <img src="${img.url}" class="h-14 w-full object-cover rounded-lg border border-gray-200 dark:border-slate-700">
                <span class="absolute inset-0 bg-red-600/70 opacity-0 group-has-[:checked]:opacity-100 flex items-center justify-center rounded-lg transition">
                    <i class="fas fa-trash text-white text-xs"></i>
                </span>
                <input type="checkbox" name="eliminar_imagenes[]" value="${img.id}" class="absolute top-1 right-1">
            `;
            galeriaGrid.appendChild(label);
        });
    }
    function proveedorCreateMode(tipoId) {
        proveedorTitle.textContent = 'Nuevo Proveedor';
        proveedorForm.action = proveedorStoreUrl;
        proveedorMethod.disabled = true;
        proveedorEditingId.value = '';
        proveedorNombre.value = '';
        proveedorTipo.value = tipoId || '';
        proveedorEmail.value = '';
        proveedorTelefono.value = '';
        proveedorDireccion.value = '';
        renderGaleria([]);
    }
    function proveedorEditMode(data) {
        proveedorTitle.textContent = 'Editar Proveedor';
        proveedorForm.action = proveedorUpdateUrlTemplate.replace('__ID__', data.id);
        proveedorMethod.disabled = false;
        proveedorEditingId.value = data.id;
        proveedorNombre.value = data.nombre || '';
        proveedorTipo.value = data.tipoId || '';
        proveedorEmail.value = data.email || '';
        proveedorTelefono.value = data.telefono || '';
        proveedorDireccion.value = data.direccion || '';
        renderGaleria(data.imagenes || []);
    }

    document.getElementById('btn-nuevo-proveedor')?.addEventListener('click', () => {
        proveedorCreateMode(null);
        openProveedorModal();
    });
    document.querySelectorAll('.js-add-proveedor').forEach(btn => {
        btn.addEventListener('click', () => {
            proveedorCreateMode(btn.dataset.tipoId);
            openProveedorModal();
        });
    });
    document.querySelectorAll('.js-edit-proveedor').forEach(btn => {
        btn.addEventListener('click', () => {
            let imagenes = [];
            try { imagenes = JSON.parse(btn.dataset.imagenes || '[]'); } catch (e) { imagenes = []; }
            proveedorEditMode({
                id: btn.dataset.id,
                tipoId: btn.dataset.tipoId,
                nombre: btn.dataset.nombre,
                email: btn.dataset.email,
                telefono: btn.dataset.telefono,
                direccion: btn.dataset.direccion,
                imagenes: imagenes,
            });
            openProveedorModal();
        });
    });
    document.getElementById('proveedor-modal-close')?.addEventListener('click', closeProveedorModal);
    document.getElementById('proveedor-modal-cancel')?.addEventListener('click', closeProveedorModal);
    document.getElementById('proveedor-modal-backdrop')?.addEventListener('click', closeProveedorModal);

    document.addEventListener('keydown', (e) => {
        if (e.key !== 'Escape') return;
        if (!tipoModal.classList.contains('hidden')) closeTipoModal();
        if (!proveedorModal.classList.contains('hidden')) closeProveedorModal();
    });

    // Si la validación falló, reabrimos el modal correspondiente con lo que el usuario había escrito.
    @if ($errors->any())
        @if (old('tipo_id') !== null || old('email') !== null || old('direccion') !== null)
            @if (old('_editing_id'))
                proveedorEditMode({
                    id: '{{ old('_editing_id') }}',
                    tipoId: '{{ old('tipo_id') }}',
                    nombre: @json(old('nombre')),
                    email: @json(old('email')),
                    telefono: @json(old('telefono')),
                    direccion: @json(old('direccion')),
                    imagenes: [],
                });
            @else
                proveedorCreateMode('{{ old('tipo_id') }}');
                proveedorNombre.value = @json(old('nombre'));
                proveedorEmail.value = @json(old('email'));
                proveedorTelefono.value = @json(old('telefono'));
                proveedorDireccion.value = @json(old('direccion'));
            @endif
            openProveedorModal();
        @elseif (old('nombre') !== null)
            @if (old('_editing_id'))
                tipoEditMode('{{ old('_editing_id') }}', @json(old('nombre')));
            @else
                tipoCreateMode();
                tipoNombre.value = @json(old('nombre'));
            @endif
            openTipoModal();
        @endif
    @endif
});
</script>
@endsection
