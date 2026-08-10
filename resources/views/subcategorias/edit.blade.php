@extends('layouts.app')
@section('title', 'Editar Subcategoría')
@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6 dark:text-slate-100">Editar Subcategoría</h1>
<div class="bg-white rounded-xl shadow p-6 md:p-8 max-w-3xl dark:bg-slate-900 dark:shadow-slate-950/50">
    <form method="POST" action="{{ route('subcategorias.update', $subcategoria) }}">
        @csrf @method('PUT')
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">Destino</label>
            <select id="destino-select" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
                @foreach($categorias->pluck('destino')->unique('id') as $destino)
                    <option value="{{ $destino->id }}" {{ old('destino_id', $subcategoria->categoria->destino_id) == $destino->id ? 'selected' : '' }}>{{ $destino->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">Categoría</label>
            <select name="categoria_id" id="categoria-select" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
                @foreach($categorias as $categoria)
                    <option value="{{ $categoria->id }}" data-destino-id="{{ $categoria->destino_id }}" class="hidden" {{ old('categoria_id', $subcategoria->categoria_id) == $categoria->id ? 'selected' : '' }}>{{ $categoria->nombre }}</option>
                @endforeach
            </select>
            @error('categoria_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">Nombre</label>
            <input type="text" name="nombre" value="{{ old('nombre', $subcategoria->nombre) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
            @error('nombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="flex gap-3">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">Actualizar</button>
            <a href="{{ route('subcategorias.index') }}" class="bg-gray-300 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-400 transition dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600">Cancelar</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const destinoSelect = document.getElementById('destino-select');
    const categoriaSelect = document.getElementById('categoria-select');
    const options = Array.from(categoriaSelect.options);
    const initialSelected = categoriaSelect.value;

    function refresh() {
        const destinoId = destinoSelect.value;
        options.forEach(o => {
            const visible = destinoId && o.dataset.destinoId === destinoId;
            o.classList.toggle('hidden', !visible);
            o.disabled = !visible;
        });
    }

    destinoSelect.addEventListener('change', refresh);
    refresh();
    categoriaSelect.value = initialSelected;
});
</script>
@endpush
@endsection
