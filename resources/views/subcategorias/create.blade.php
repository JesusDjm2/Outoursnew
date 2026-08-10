@extends('layouts.app')
@section('title', 'Nueva Subcategoría')
@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6 dark:text-slate-100">Nueva Subcategoría</h1>
<div class="bg-white rounded-xl shadow p-6 md:p-8 max-w-3xl dark:bg-slate-900 dark:shadow-slate-950/50">
    <form method="POST" action="{{ route('subcategorias.store') }}">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">Destino</label>
            <select id="destino-select" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
                <option value="">Selecciona un destino...</option>
                @foreach($categorias->pluck('destino')->unique('id') as $destino)
                    <option value="{{ $destino->id }}">{{ $destino->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">Categoría</label>
            <select name="categoria_id" id="categoria-select" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
                <option value="">Selecciona un destino primero...</option>
                @foreach($categorias as $categoria)
                    <option value="{{ $categoria->id }}" data-destino-id="{{ $categoria->destino_id }}" class="hidden" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>{{ $categoria->nombre }}</option>
                @endforeach
            </select>
            @error('categoria_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">Nombre</label>
            <input type="text" name="nombre" value="{{ old('nombre') }}" required placeholder="Ej. City tour full day" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
            @error('nombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="flex gap-3">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">Guardar</button>
            <a href="{{ route('subcategorias.index') }}" class="bg-gray-300 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-400 transition dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600">Cancelar</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const destinoSelect = document.getElementById('destino-select');
    const categoriaSelect = document.getElementById('categoria-select');
    const options = Array.from(categoriaSelect.options).filter(o => o.value !== '');

    function refresh() {
        const destinoId = destinoSelect.value;
        categoriaSelect.querySelectorAll('option[value=""]').forEach(o => o.remove());
        options.forEach(o => {
            const visible = destinoId && o.dataset.destinoId === destinoId;
            o.classList.toggle('hidden', !visible);
            o.disabled = !visible;
        });
        const visibleOptions = options.filter(o => !o.disabled);
        if (!visibleOptions.some(o => o.selected)) {
            const placeholder = new Option(destinoId ? 'Selecciona una categoría...' : 'Selecciona un destino primero...', '', true, true);
            categoriaSelect.prepend(placeholder);
        }
    }

    destinoSelect.addEventListener('change', refresh);
    refresh();
});
</script>
@endpush
@endsection
