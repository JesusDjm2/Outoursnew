@php
    $selectedDestinoId = old('destino_id', $selectedDestinoId ?? null);
    $selectedCategoriaId = old('categoria_id', $selectedCategoriaId ?? null);
    $categoryTreeJson = $destinos->map(fn($destino) => [
        'id' => $destino->id,
        'nombre' => $destino->nombre,
        'categorias' => $destino->categorias->map(fn($categoria) => [
            'id' => $categoria->id,
            'nombre' => $categoria->nombre,
        ]),
    ]);
@endphp
@if($destinos->isEmpty())
    <p class="text-sm text-amber-600 dark:text-amber-400 mb-4">
        Aún no hay destinos/categorías registrados. <a href="{{ route('destinos.create') }}" target="_blank" class="underline">Crea uno primero</a> y luego recarga esta página.
    </p>
@else
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">Destino</label>
        <select name="destino_id" id="picker-destino" required class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
            <option value="">Selecciona...</option>
            @foreach($destinos as $destino)
                <option value="{{ $destino->id }}" {{ (string) $selectedDestinoId === (string) $destino->id ? 'selected' : '' }}>{{ $destino->nombre }}</option>
            @endforeach
        </select>
        @error('destino_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">Categoría <span class="text-gray-400 font-normal">(opcional)</span></label>
        <select name="categoria_id" id="picker-categoria" class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
            <option value="">Selecciona un destino primero...</option>
        </select>
        @error('categoria_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
</div>

<script>
window.categoryTree = window.categoryTree || @json($categoryTreeJson);
window.categoryPickerInitial = {{ Js::from(['destino_id' => $selectedDestinoId, 'categoria_id' => $selectedCategoriaId]) }};
document.addEventListener('DOMContentLoaded', () => {
    const destinoSelect = document.getElementById('picker-destino');
    const categoriaSelect = document.getElementById('picker-categoria');
    const tree = window.categoryTree;
    const initial = window.categoryPickerInitial || {};

    function fillCategorias(destinoId, preselectId) {
        categoriaSelect.innerHTML = '';
        const destino = tree.find(d => String(d.id) === String(destinoId));
        if (!destino) {
            categoriaSelect.appendChild(new Option('Selecciona un destino primero...', ''));
            return;
        }
        categoriaSelect.appendChild(new Option('Ninguna', ''));
        destino.categorias.forEach(c => categoriaSelect.appendChild(new Option(c.nombre, c.id)));
        if (preselectId) categoriaSelect.value = preselectId;
    }

    destinoSelect.addEventListener('change', () => fillCategorias(destinoSelect.value, null));

    fillCategorias(initial.destino_id, initial.categoria_id);
});
</script>
@endif
