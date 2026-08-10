@extends('layouts.app')
@section('title', 'Editar Itinerario')
@section('content')
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/quill/2.0.2/quill.snow.min.css">
@endpush

<h1 class="text-2xl font-bold text-gray-800 mb-6 dark:text-slate-100">Editar Itinerario</h1>
<div class="bg-white rounded-xl shadow p-6 md:p-8 max-w-3xl dark:bg-slate-900 dark:shadow-slate-950/50">
    <form method="POST" action="{{ route('itineraries.update', $itinerary) }}" id="itinerary-form">
        @csrf @method('PUT')
        @include('itineraries._category_picker', ['selectedDestinoId' => $itinerary->destino_id, 'selectedCategoriaId' => $itinerary->categoria_id])
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">Nombre</label>
                <input type="text" name="nombre" value="{{ old('nombre', $itinerary->nombre) }}" required class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">Costo Regular (por persona)</label>
                <input type="number" step="0.01" min="0" name="costo" value="{{ old('costo', $itinerary->costo) }}" class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">Costo Promo (por persona)</label>
                <input type="number" step="0.01" min="0" name="costo_promo" value="{{ old('costo_promo', $itinerary->costo_promo) }}" class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
            </div>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">Descripción</label>
            <div id="editor-descripcion" class="bg-white dark:bg-slate-800 rounded-b-lg">{!! old('descripcion', $itinerary->descripcion) !!}</div>
            <input type="hidden" name="descripcion" id="input-descripcion">
        </div>
        <div class="flex flex-col md:flex-row items-start gap-4 mb-6">
            <div class="flex-1 min-w-0">
                <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">Incluye</label>
                <div id="editor-incluye" class="bg-white dark:bg-slate-800 rounded-b-lg">{!! old('incluye', $itinerary->incluye) !!}</div>
                <input type="hidden" name="incluye" id="input-incluye">
            </div>
            <div class="flex-1 min-w-0">
                <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">No Incluye</label>
                <div id="editor-no_incluye" class="bg-white dark:bg-slate-800 rounded-b-lg">{!! old('no_incluye', $itinerary->no_incluye) !!}</div>
                <input type="hidden" name="no_incluye" id="input-no_incluye">
            </div>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">Actualizar</button>
            <a href="{{ route('itineraries.index') }}" class="bg-gray-300 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-400 transition dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600">Cancelar</a>
        </div>
    </form>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/quill/2.0.2/quill.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const fields = ['descripcion', 'incluye', 'no_incluye'];
    const editors = fields.map((field) => {
        const quill = new Quill('#editor-' + field, { theme: 'snow' });
        return { field, quill };
    });

    document.getElementById('itinerary-form').addEventListener('submit', () => {
        editors.forEach(({ field, quill }) => {
            const html = quill.getText().trim() === '' ? '' : quill.root.innerHTML;
            document.getElementById('input-' + field).value = html;
        });
    });
});
</script>
@endpush
@endsection
