@php
    $catalogoItinerarios = \App\Models\Itinerary::orderBy('nombre')->get(['id', 'nombre', 'costo', 'costo_promo']);
    $initialItemsData = $itemsSeleccionados->values()->map(fn($it, $i) => [
        'id' => $it->id,
        'nombre' => $it->nombre,
        'dia' => $it->pivot->dia ?? null,
        'cantidad' => $it->pivot->cantidad_pax_defecto ?? null,
        'dia_error' => $errors->first("itinerarios_dia.$i"),
        'cantidad_error' => $errors->first("itinerarios_cantidad.$i"),
    ])->values();
@endphp
<div>
    <script>
        window.initialPackageItems = @json($initialItemsData);
        window.packageItinerarioCatalogo = @json($catalogoItinerarios);
    </script>

    <datalist id="package-datalist">
        @foreach($catalogoItinerarios->unique('nombre') as $it)
            <option value="{{ $it->nombre }}"></option>
        @endforeach
    </datalist>

    <div class="flex items-center justify-end gap-2 mb-3">
        <button type="button" id="add-package-fila-btn" class="bg-purple-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-purple-700 transition">
            <i class="fas fa-plus"></i> Fila
        </button>
        <button type="button" id="limpiar-package-filas-btn" class="bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg text-sm hover:bg-gray-300 transition dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600">
            Limpiar
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm border-collapse">
            <thead>
                <tr class="text-left text-xs uppercase text-gray-500 dark:text-slate-400 border-b border-gray-200 dark:border-slate-700">
                    <th class="w-6"></th>
                    <th class="py-2 pr-2 font-medium w-24">Día</th>
                    <th class="py-2 pr-2 font-medium">Tour / Actividad</th>
                    <th class="py-2 pr-2 font-medium w-32">Cant. pax (defecto)</th>
                    <th class="py-2 font-medium w-10">Acc.</th>
                </tr>
            </thead>
            <tbody id="package-items-seleccionados"></tbody>
        </table>
    </div>
    <p id="package-items-empty-msg" class="text-sm text-gray-400 dark:text-slate-500 mt-2">
        Aún no agregaste días. Usa "+ Fila" para comenzar.
    </p>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const tbody = document.getElementById('package-items-seleccionados');
    const emptyMsg = document.getElementById('package-items-empty-msg');

    const catalogoPorNombre = {};
    (window.packageItinerarioCatalogo || []).forEach((it) => {
        catalogoPorNombre[it.nombre.trim().toLowerCase()] = it;
    });

    let rowSeq = 0;
    let draggedRow = null;

    const form = tbody.closest('form');
    form?.addEventListener('submit', () => {
        tbody.querySelectorAll('.package-item-row').forEach((tr) => {
            const idInput = tr.querySelector('.package-item-id-input');
            const resolved = !!idInput.value;
            tr.querySelector('.package-item-dia').disabled = !resolved;
            tr.querySelector('.package-item-cantidad').disabled = !resolved;
            idInput.disabled = !resolved;
        });
    });

    function setRowResolved(tr, resolved) {
        tr.querySelector('.package-item-no-match-msg').classList.toggle('hidden', resolved);
    }

    function createRow(data) {
        rowSeq++;
        const rowId = 'package-row-' + rowSeq;

        const tr = document.createElement('tr');
        tr.className = 'package-item-row border-b border-gray-100 dark:border-slate-800';
        tr.draggable = true;
        tr.innerHTML = `
            <td class="text-gray-400 dark:text-slate-500 align-top pt-2 cursor-grab" title="Arrastrar para reordenar">
                <i class="fas fa-grip-vertical"></i>
            </td>
            <td class="py-1.5 pr-2 align-top">
                <input type="number" min="1" class="package-item-dia w-full px-2 py-1 text-sm border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-purple-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100" name="itinerarios_dia[]">
                <p class="package-item-dia-error text-[11px] text-red-600 dark:text-red-400 mt-0.5 hidden"></p>
            </td>
            <td class="py-1.5 pr-2 align-top">
                <input type="text" list="package-datalist" id="${rowId}-tour" class="package-item-tour-input w-full px-2 py-1 text-sm border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-purple-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100" placeholder="Escribe o elige un tour...">
                <input type="hidden" class="package-item-id-input" name="itinerarios[]">
                <p class="package-item-no-match-msg text-[11px] text-amber-600 dark:text-amber-400 mt-0.5 hidden">No coincide con ningún tour del catálogo.</p>
            </td>
            <td class="py-1.5 pr-2 align-top">
                <input type="number" min="1" class="package-item-cantidad w-full px-2 py-1 text-sm border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-purple-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100" name="itinerarios_cantidad[]">
                <p class="package-item-cantidad-error text-[11px] text-red-600 dark:text-red-400 mt-0.5 hidden"></p>
            </td>
            <td class="py-1.5 align-top">
                <button type="button" class="remove-package-item-btn text-red-600 hover:text-red-800 dark:text-red-400"><i class="fas fa-trash"></i></button>
            </td>
        `;

        const diaInput = tr.querySelector('.package-item-dia');
        const tourInput = tr.querySelector('.package-item-tour-input');
        const idInput = tr.querySelector('.package-item-id-input');
        const cantidadInput = tr.querySelector('.package-item-cantidad');

        diaInput.value = data.dia || '';
        cantidadInput.value = data.cantidad ?? 1;

        if (data.dia_error) {
            diaInput.classList.add('border-red-500', 'focus:ring-red-500');
            const msg = tr.querySelector('.package-item-dia-error');
            msg.textContent = data.dia_error;
            msg.classList.remove('hidden');
        }
        if (data.cantidad_error) {
            cantidadInput.classList.add('border-red-500', 'focus:ring-red-500');
            const msg = tr.querySelector('.package-item-cantidad-error');
            msg.textContent = data.cantidad_error;
            msg.classList.remove('hidden');
        }

        let ultimoValorValido = '';

        diaInput.addEventListener('input', () => {
            diaInput.classList.remove('border-red-500', 'focus:ring-red-500');
            tr.querySelector('.package-item-dia-error').classList.add('hidden');
        });

        cantidadInput.addEventListener('input', () => {
            cantidadInput.classList.remove('border-red-500', 'focus:ring-red-500');
            tr.querySelector('.package-item-cantidad-error').classList.add('hidden');
        });

        tourInput.addEventListener('mousedown', () => {
            ultimoValorValido = tourInput.value;
            tourInput.value = '';
        });

        tourInput.addEventListener('blur', () => {
            if (!tourInput.value.trim() && ultimoValorValido) {
                tourInput.value = ultimoValorValido;
            }
        });

        tourInput.addEventListener('input', () => {
            const value = tourInput.value.trim();
            if (!value) {
                idInput.value = '';
                setRowResolved(tr, true);
                return;
            }
            const match = catalogoPorNombre[value.toLowerCase()];
            if (match) {
                ultimoValorValido = match.nombre;
                idInput.value = match.id;
                setRowResolved(tr, true);
            } else {
                idInput.value = '';
                setRowResolved(tr, false);
            }
        });

        tbody.appendChild(tr);
        emptyMsg.classList.add('hidden');

        if (data.id) {
            tourInput.value = data.nombre;
            ultimoValorValido = data.nombre;
            idInput.value = data.id;
        }
        setRowResolved(tr, true);

        tr.addEventListener('dragstart', () => {
            draggedRow = tr;
            tr.classList.add('opacity-40');
        });
        tr.addEventListener('dragend', () => {
            draggedRow = null;
            tr.classList.remove('opacity-40');
        });
    }

    tbody.addEventListener('dragover', (e) => {
        e.preventDefault();
        if (!draggedRow) return;
        const targetRow = e.target.closest('tr');
        if (!targetRow || targetRow === draggedRow) return;
        const rect = targetRow.getBoundingClientRect();
        const before = (e.clientY - rect.top) < rect.height / 2;
        tbody.insertBefore(draggedRow, before ? targetRow : targetRow.nextSibling);
    });

    (window.initialPackageItems || []).forEach((it) => createRow(it));

    document.getElementById('add-package-fila-btn').addEventListener('click', () => createRow({}));

    document.getElementById('limpiar-package-filas-btn').addEventListener('click', () => {
        tbody.innerHTML = '';
        emptyMsg.classList.remove('hidden');
    });

    tbody.addEventListener('click', (e) => {
        const removeBtn = e.target.closest('.remove-package-item-btn');
        if (removeBtn) {
            removeBtn.closest('tr').remove();
            if (!tbody.querySelector('tr')) emptyMsg.classList.remove('hidden');
        }
    });
});
</script>
