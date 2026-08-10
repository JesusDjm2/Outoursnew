@php
    $passId = optional($passenger)->id;
@endphp
<div class="passenger-block bg-green-50 border border-green-100 rounded-xl p-4 mb-3 dark:bg-green-900/20 dark:border-green-900/40">
    <div class="flex items-start justify-between mb-3">
        <h4 class="text-sm font-semibold text-green-800 dark:text-green-300"><i class="fas fa-user mr-1"></i> Pasajero</h4>
        <button type="button" class="remove-passenger-btn text-red-500 hover:text-red-700 text-xs dark:text-red-400 dark:hover:text-red-300">
            <i class="fas fa-times"></i> Quitar
        </button>
    </div>

    @if($passId)
        <input type="hidden" name="pasajeros[{{ $i }}][id]" value="{{ $passId }}">
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-300">Nombre</label>
            <input type="text" name="pasajeros[{{ $i }}][nombre]"
                   value="{{ old("pasajeros.$i.nombre", optional($passenger)->nombre) }}" required
                   class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-300">Fecha de nacimiento</label>
            <input type="date" name="pasajeros[{{ $i }}][fecha_nacimiento]"
                   value="{{ old("pasajeros.$i.fecha_nacimiento", optional($passenger)->fecha_nacimiento) }}"
                   class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-300">Edad</label>
            <input type="number" name="pasajeros[{{ $i }}][edad]" min="0"
                   value="{{ old("pasajeros.$i.edad", optional($passenger)->edad) }}"
                   class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-300">Correo</label>
            <input type="email" name="pasajeros[{{ $i }}][correo]"
                   value="{{ old("pasajeros.$i.correo", optional($passenger)->correo) }}"
                   class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
        </div>
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-300">
            Imagen o PDF @if(optional($passenger)->imagen_path || optional($passenger)->pdf_path) (dejar vacío para mantener) @endif
        </label>
        <input type="file" name="pasajeros[{{ $i }}][archivo]" accept="image/*,.pdf"
               class="w-full text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none px-3 py-1.5 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
        @if(optional($passenger)->imagen_path)
            <a href="{{ asset('storage/' . $passenger->imagen_path) }}" target="_blank" class="text-xs text-green-700 underline dark:text-green-400">Ver imagen actual</a>
        @elseif(optional($passenger)->pdf_path)
            <a href="{{ asset('storage/' . $passenger->pdf_path) }}" target="_blank" class="text-xs text-green-700 underline dark:text-green-400">Ver PDF actual</a>
        @endif
    </div>
</div>
