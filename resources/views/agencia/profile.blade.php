@extends('layouts.app')
@section('title', 'Mi Agencia')
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/quill/2.0.2/quill.snow.min.css">
@endpush
@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6 dark:text-slate-100">Mi Agencia</h1>
<div class="bg-white rounded-xl shadow p-6 md:p-8 w-full dark:bg-slate-900 dark:shadow-slate-950/50">
    <form method="POST" action="{{ route('agencia.profile.update') }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">Nombre de la agencia</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">RUC</label>
                <input type="text" name="ruc" value="{{ old('ruc', $user->ruc) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
                @error('ruc') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">Teléfono fijo</label>
                <input type="text" name="telefono" value="{{ old('telefono', $user->telefono) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
                @error('telefono') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">Celulares</label>
                <input type="text" name="celulares" value="{{ old('celulares', $user->celulares) }}" placeholder="(+51) 970-824-536 / (+51) 935-095-895" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
                @error('celulares') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">WhatsApp</label>
                <input type="text" name="whatsapp" value="{{ old('whatsapp', $user->whatsapp) }}" placeholder="(+51) 935-095-895" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
                @error('whatsapp') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">Dirección</label>
            <input type="text" name="direccion" value="{{ old('direccion', $user->direccion) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
            @error('direccion') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">Logo</label>
            <input type="file" name="logo" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
            @error('logo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @if($user->logo_path)
            <img src="{{ asset('storage/' . $user->logo_path) }}" alt="Logo de {{ $user->name }}" class="mt-2 h-20 rounded bg-white p-1">
            @endif
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2 dark:text-slate-300">Paleta de colores (opcional, hasta 3)</label>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach(range(0, 2) as $i)
                <div class="flex items-center gap-3">
                    <input type="color" name="colores[{{ $i }}]"
                           value="{{ old("colores.$i", $user->colores[$i] ?? ['#de0007', '#991519', '#de0007'][$i]) }}"
                           class="h-10 w-14 rounded border border-gray-300 dark:border-slate-700">
                    <span class="text-sm text-gray-600 dark:text-slate-400">
                        {{ ['Color primario', 'Color secundario', 'Color de acento'][$i] }}
                    </span>
                </div>
                @endforeach
            </div>
            <p class="text-xs text-gray-400 mt-2 dark:text-slate-500">Es opcional: puedes definir de 1 a 3 colores. Si no eliges ninguno, se usan los colores del tema principal (rojo).</p>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2 dark:text-slate-300">Términos y condiciones</label>
            <p class="text-xs text-gray-400 mb-3 dark:text-slate-500">Se imprimen como segunda página en el PDF de la cotización, en el idioma que elegiste para ese tour. Si dejas un idioma vacío, esa página simplemente se omite.</p>

            @php $terminosIdiomas = ['es' => 'Español', 'en' => 'Inglés', 'pt' => 'Portugués']; @endphp
            <div class="flex gap-2 mb-3" role="tablist">
                @foreach($terminosIdiomas as $codigo => $label)
                <button type="button" data-terminos-tab="{{ $codigo }}"
                        class="terminos-tab-btn px-4 py-1.5 rounded-lg text-sm border {{ $loop->first ? 'bg-blue-600 text-white border-blue-600' : 'bg-gray-100 text-gray-600 border-gray-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700' }}">
                    {{ $label }}
                </button>
                @endforeach
            </div>

            @foreach($terminosIdiomas as $codigo => $label)
            <div data-terminos-panel="{{ $codigo }}" class="{{ $loop->first ? '' : 'hidden' }}">
                <div id="editor-terminos-{{ $codigo }}" class="bg-white dark:bg-slate-800 rounded-b-lg">{!! old("terminos_condiciones_$codigo", $user->{"terminos_condiciones_$codigo"}) !!}</div>
                <input type="hidden" name="terminos_condiciones_{{ $codigo }}" id="input-terminos-{{ $codigo }}" value="{{ old("terminos_condiciones_$codigo", $user->{"terminos_condiciones_$codigo"}) }}">
                @error("terminos_condiciones_$codigo") <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            @endforeach
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">Guardar</button>
            <a href="{{ route('dashboard') }}" class="bg-gray-300 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-400 transition dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600">Cancelar</a>
        </div>
    </form>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/quill/2.0.2/quill.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const tabButtons = document.querySelectorAll('.terminos-tab-btn');
    const panels = document.querySelectorAll('[data-terminos-panel]');
    const quillInstances = {};

    function initEditor(codigo) {
        if (quillInstances[codigo] || !window.Quill) return quillInstances[codigo] || null;
        try {
            quillInstances[codigo] = new Quill(`#editor-terminos-${codigo}`, {
                theme: 'snow',
                modules: {
                    toolbar: [['bold', 'italic', 'underline'], [{ list: 'ordered' }, { list: 'bullet' }], ['link'], ['clean']],
                },
            });
        } catch (err) {
            console.error('No se pudo inicializar el editor de términos (' + codigo + ')', err);
        }
        return quillInstances[codigo] || null;
    }

    // 1) Primero dejamos funcionando la navegación entre pestañas y el guardado,
    //    sin depender de que Quill haya cargado o inicializado correctamente.
    tabButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            const target = btn.dataset.terminosTab;

            tabButtons.forEach((b) => {
                const active = b === btn;
                b.classList.toggle('bg-blue-600', active);
                b.classList.toggle('text-white', active);
                b.classList.toggle('border-blue-600', active);
                b.classList.toggle('bg-gray-100', !active);
                b.classList.toggle('text-gray-600', !active);
                b.classList.toggle('border-gray-200', !active);
                b.classList.toggle('dark:bg-slate-800', !active);
                b.classList.toggle('dark:text-slate-300', !active);
                b.classList.toggle('dark:border-slate-700', !active);
            });

            panels.forEach((panel) => {
                panel.classList.toggle('hidden', panel.dataset.terminosPanel !== target);
            });

            // El panel recién quedó visible: esperamos al siguiente frame para que el
            // navegador termine de aplicarlo antes de que Quill mida el contenedor.
            requestAnimationFrame(() => initEditor(target));
        });
    });

    const agenciaForm = tabButtons[0]?.closest('form');
    agenciaForm?.addEventListener('submit', () => {
        Object.entries(quillInstances).forEach(([codigo, quill]) => {
            if (!quill) return;
            const input = document.getElementById(`input-terminos-${codigo}`);
            input.value = quill.getText().trim() === '' ? '' : quill.root.innerHTML;
        });
    });

    // 2) Recién ahora inicializamos el editor del idioma visible al cargar la página.
    if (panels[0]) {
        initEditor(panels[0].dataset.terminosPanel);
    }
});
</script>
@endsection
