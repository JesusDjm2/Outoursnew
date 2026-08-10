@php
    $tour = $tour ?? null;
    $resumen = $tour ? $tour->calcularResumenFactura() : ['pv_regular' => 0, 'pv_promo' => 0, 'total_descuento' => 0, 'pv_final' => 0];
@endphp
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/quill/2.0.2/quill.snow.min.css">
@endpush
<div class="bg-white rounded-xl shadow p-6 dark:bg-slate-900 dark:shadow-slate-950/50">
    <h2 class="font-semibold text-gray-800 mb-4 dark:text-slate-100"><i class="fas fa-file-invoice-dollar text-emerald-600 mr-1"></i> Resumen — Factura</h2>

    <div class="grid grid-cols-2 gap-y-3 gap-x-4 items-center max-w-md">
        <span class="text-sm text-gray-600 dark:text-slate-300">P.V. Regular</span>
        <span id="resumen-pv-regular" class="text-right font-bold text-gray-800 dark:text-slate-100" data-value="{{ $resumen['pv_regular'] }}"></span>

        <span class="text-sm text-gray-600 dark:text-slate-300">P.V. Promo</span>
        <span id="resumen-pv-promo" class="text-right font-bold text-gray-800 dark:text-slate-100" data-value="{{ $resumen['pv_promo'] }}"></span>

        <span class="text-sm text-gray-600 dark:text-slate-300">Total descuento</span>
        <span id="resumen-total-descuento" class="text-right text-gray-700 dark:text-slate-300" data-value="{{ $resumen['total_descuento'] }}"></span>

        <label for="precio_adicional" class="text-sm text-gray-600 dark:text-slate-300">Precio adicional</label>
        <input type="number" step="0.01" id="precio_adicional" name="precio_adicional"
               value="{{ old('precio_adicional', $tour->precio_adicional ?? 0) }}"
               class="w-full text-right px-2 py-1 text-sm border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">

        <label for="descuento_especial" class="text-sm text-gray-600 dark:text-slate-300">Descuento especial</label>
        <input type="number" step="0.01" id="descuento_especial" name="descuento_especial"
               value="{{ old('descuento_especial', $tour->descuento_especial ?? 0) }}"
               class="w-full text-right px-2 py-1 text-sm border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">

        <span class="text-base font-semibold text-gray-800 dark:text-slate-100">P.V. Final</span>
        <span id="resumen-pv-final" class="text-right text-2xl font-bold text-emerald-600 dark:text-emerald-400" data-value="{{ $resumen['pv_final'] }}"></span>
    </div>

    <hr class="my-5 border-gray-200 dark:border-slate-700">

    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">Notas Adicionales</label>
            <div id="editor-notas-adicionales" class="bg-white dark:bg-slate-800 rounded-b-lg">{!! old('notas_adicionales', $tour->notas_adicionales ?? '') !!}</div>
            <input type="hidden" name="notas_adicionales" id="input-notas-adicionales">
            <p class="text-xs text-gray-400 mt-1 dark:text-slate-500">Se guarda con la cotización y se imprime en el documento final.</p>
        </div>
        <div class="max-w-[160px]">
            <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-slate-300">Reserva (%)</label>
            <input type="number" min="0" max="100" name="reserva_pct" value="{{ old('reserva_pct', $tour->reserva_pct ?? 30) }}" class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
        </div>
    </div>

    <div class="flex flex-wrap gap-3 mt-6">
        <button type="submit" class="bg-emerald-600 text-white px-6 py-2 rounded-lg hover:bg-emerald-700 transition">
            <i class="fas fa-save"></i> Guardar
        </button>
        @if($tour)
            <a href="{{ route('tours.pdf', $tour) }}" target="_blank" class="bg-slate-600 text-white px-4 py-2 rounded-lg hover:bg-slate-700 transition">
                <i class="fas fa-file-pdf"></i> Generar documento/PDF
            </a>
        @else
            <span class="bg-gray-200 text-gray-400 px-4 py-2 rounded-lg cursor-not-allowed dark:bg-slate-800 dark:text-slate-600" title="Guarda el tour primero para generar el PDF">
                <i class="fas fa-file-pdf"></i> Generar documento/PDF
            </span>
        @endif
        <a href="{{ route('tours.index') }}" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-400 transition dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600">Cancelar</a>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/quill/2.0.2/quill.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const notasQuill = new Quill('#editor-notas-adicionales', {
        theme: 'snow',
        modules: {
            toolbar: [['bold', 'italic', 'underline'], [{ list: 'ordered' }, { list: 'bullet' }], ['link']],
        },
    });
    const notasInput = document.getElementById('input-notas-adicionales');
    document.getElementById('editor-notas-adicionales').closest('form')?.addEventListener('submit', () => {
        notasInput.value = notasQuill.getText().trim() === '' ? '' : notasQuill.root.innerHTML;
    });

    const els = {
        pvRegular: document.getElementById('resumen-pv-regular'),
        pvPromo: document.getElementById('resumen-pv-promo'),
        totalDescuento: document.getElementById('resumen-total-descuento'),
        pvFinal: document.getElementById('resumen-pv-final'),
    };
    const precioAdicionalInput = document.getElementById('precio_adicional');
    const descuentoEspecialInput = document.getElementById('descuento_especial');

    function monedaSimbolo() {
        const checked = document.querySelector('input[name="moneda"]:checked');
        return checked && checked.value === 'PEN' ? 'S/ ' : '$ ';
    }

    function formatMoney(value) {
        return monedaSimbolo() + (parseFloat(value) || 0).toFixed(2);
    }

    function recompute() {
        let pvRegular = 0;
        let pvPromo = 0;

        document.querySelectorAll('.itinerario-row').forEach((tr) => {
            const idInput = tr.querySelector('.itinerario-id-input');
            const id = idInput?.value;
            if (!id) return;
            const cantidad = parseFloat(tr.querySelector('.itinerario-cantidad')?.value || '0') || 0;
            const regular = parseFloat(window.itinerarioCostos?.[id] ?? 0) || 0;
            const promo = parseFloat(window.itinerarioCostosPromo?.[id] ?? regular) || 0;
            pvRegular += regular * cantidad;
            pvPromo += promo * cantidad;
        });

        document.querySelectorAll('.hospedaje-block').forEach((block) => {
            const fechaIngreso = block.querySelector('[name*="[fecha_ingreso]"]')?.value;
            const fechaSalida = block.querySelector('[name*="[fecha_salida]"]')?.value;
            if (!fechaIngreso || !fechaSalida) return;
            const noches = Math.max(1, Math.round((new Date(fechaSalida) - new Date(fechaIngreso)) / 86400000));
            const roomsSelect = block.querySelector('.hospedaje-rooms-select');
            const selectedIds = roomsSelect ? Array.from(roomsSelect.selectedOptions).map((o) => o.value) : [];
            selectedIds.forEach((roomId) => {
                const info = window.roomPrices?.[roomId];
                if (!info) return;
                const regular = parseFloat(info.precio_regular || 0) || 0;
                const promo = parseFloat(info.precio_promo ?? info.precio_regular ?? 0) || 0;
                pvRegular += regular * noches;
                pvPromo += promo * noches;
            });
        });

        const precioAdicional = parseFloat(precioAdicionalInput?.value || '0') || 0;
        const descuentoEspecial = parseFloat(descuentoEspecialInput?.value || '0') || 0;
        const totalDescuento = pvRegular - pvPromo;
        const pvFinal = pvPromo + precioAdicional - descuentoEspecial;

        els.pvRegular.textContent = formatMoney(pvRegular);
        els.pvPromo.textContent = formatMoney(pvPromo);
        els.totalDescuento.textContent = formatMoney(totalDescuento);
        els.pvFinal.textContent = formatMoney(pvFinal);
    }

    window.recomputeResumenFactura = recompute;

    precioAdicionalInput?.addEventListener('input', recompute);
    descuentoEspecialInput?.addEventListener('input', recompute);
    document.querySelectorAll('input[name="moneda"]').forEach((r) => r.addEventListener('change', recompute));

    recompute();
});
</script>
