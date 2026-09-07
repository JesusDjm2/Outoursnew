@php
    $tour = $tour ?? null;
    $idiomaValue = old('idioma', $tour?->idioma ?? 'ingles');
    $monedaValue = old('moneda', $tour?->moneda ?? 'USD');
    $idiomasOpciones = ['ingles' => 'Inglés', 'espanol' => 'Español', 'portugues' => 'Portugués'];
@endphp
<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">Idioma</label>
        <div class="flex items-center gap-4">
            @foreach($idiomasOpciones as $value => $label)
                <label class="flex items-center gap-1.5 text-sm text-gray-700 dark:text-slate-300">
                    <input type="radio" name="idioma" value="{{ $value }}"
                           {{ $idiomaValue === $value ? 'checked' : '' }}
                           class="text-blue-600 focus:ring-blue-500 dark:border-slate-600">
                    {{ $label }}
                </label>
            @endforeach
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">Moneda</label>
        <div class="flex w-fit rounded-lg border border-gray-300 overflow-hidden dark:border-slate-700">
            <label class="cursor-pointer">
                <input type="radio" name="moneda" value="USD" class="peer hidden" {{ $monedaValue === 'USD' ? 'checked' : '' }}>
                <span class="block px-3 py-1.5 text-sm font-medium text-gray-600 peer-checked:bg-blue-600 peer-checked:text-white dark:text-slate-300">Dólar (USD)</span>
            </label>
            <label class="cursor-pointer border-l border-gray-300 dark:border-slate-700">
                <input type="radio" name="moneda" value="PEN" class="peer hidden" {{ $monedaValue === 'PEN' ? 'checked' : '' }}>
                <span class="block px-3 py-1.5 text-sm font-medium text-gray-600 peer-checked:bg-blue-600 peer-checked:text-white dark:text-slate-300">Soles (PEN)</span>
            </label>
        </div>
    </div>
</div>
