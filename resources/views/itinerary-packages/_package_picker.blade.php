@php
    $destinos = $destinos ?? collect();
    $catalogoItinerarios = \App\Models\Itinerary::orderBy('nombre')
        ->get(['id', 'nombre', 'costo', 'costo_promo', 'destino_id', 'categoria_id']);
    $initialItemsData = $itemsSeleccionados->values()->map(fn($it, $i) => [
        'id' => $it->id,
        'nombre' => $it->nombre,
        'destino_id' => $it->destino_id,
        'categoria_id' => $it->categoria_id,
        'dia' => $it->pivot->dia ?? null,
        'cantidad' => $it->pivot->cantidad_pax_defecto ?? null,
        'dia_error' => $errors->first("itinerarios_dia.$i"),
        'cantidad_error' => $errors->first("itinerarios_cantidad.$i"),
    ])->values();
    $categoryTreeJson = $destinos->map(fn($destino) => [
        'id' => $destino->id,
        'nombre' => $destino->nombre,
        'categorias' => $destino->categorias->map(fn($categoria) => [
            'id' => $categoria->id,
            'nombre' => $categoria->nombre,
        ]),
    ]);
@endphp
<div>
    <script>
        window.initialPackageItems = @json($initialItemsData);
        window.packageCategoryTree = @json($categoryTreeJson);
        window.packageItinerarioCatalogo = @json($catalogoItinerarios);
    </script>

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
                    <th class="py-2 pr-2 font-medium w-20">Día</th>
                    <th class="py-2 pr-2 font-medium w-36">Destino</th>
                    <th class="py-2 pr-2 font-medium w-40">Categoría</th>
                    <th class="py-2 pr-2 font-medium min-w-[240px]">Actividad</th>
                    <th class="py-2 pr-2 font-medium w-28">Cant. pax</th>
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
    const tree = window.packageCategoryTree || [];
    const catalogo = window.packageItinerarioCatalogo || [];

    const catalogoPorId = {};
    catalogo.forEach((it) => { catalogoPorId[it.id] = it; });

    let rowSeq = 0;
    let draggedRow = null;

    const form = tbody.closest('form');
    form?.addEventListener('submit', () => {
        tbody.querySelectorAll('.package-item-row').forEach((tr) => {
            const actividadSelect = tr.querySelector('.package-item-actividad');
            const resolved = !!actividadSelect.value;
            tr.querySelector('.package-item-dia').disabled = !resolved;
            tr.querySelector('.package-item-cantidad').disabled = !resolved;
            actividadSelect.disabled = !resolved;
        });
    });

    function poblarCategoriaSelect(select, destinoId, selectedCategoriaId) {
        select.innerHTML = '';
        select.appendChild(new Option('Todas', ''));
        const destino = tree.find((d) => String(d.id) === String(destinoId));
        select.disabled = !destino;
        if (destino) {
            destino.categorias.forEach((c) => select.appendChild(new Option(c.nombre, c.id)));
        }
        select.value = selectedCategoriaId ? String(selectedCategoriaId) : '';
    }

    function poblarActividadSelect(select, destinoId, categoriaId, selectedId) {
        const actual = selectedId ?? select.value;
        select.innerHTML = '';
        select.appendChild(new Option('Selecciona...', ''));
        catalogo
            .filter((it) => (!destinoId || String(it.destino_id) === String(destinoId)))
            .filter((it) => (!categoriaId || String(it.categoria_id) === String(categoriaId)))
            .forEach((it) => select.appendChild(new Option(it.nombre, it.id)));
        select.value = actual ? String(actual) : '';
    }

    function createRow(data) {
        rowSeq++;
        const catalogItem = data.id ? catalogoPorId[data.id] : null;
        const destinoId = catalogItem ? catalogItem.destino_id : (data.destino_id ?? null);
        const categoriaId = catalogItem ? catalogItem.categoria_id : (data.categoria_id ?? null);

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
                <select class="package-item-destino w-full px-2 py-1 text-sm border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-purple-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
                    <option value="">Todos</option>
                </select>
            </td>
            <td class="py-1.5 pr-2 align-top">
                <select class="package-item-categoria w-full px-2 py-1 text-sm border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-purple-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100" disabled>
                    <option value="">Todas</option>
                </select>
            </td>
            <td class="py-1.5 pr-2 align-top">
                <select class="package-item-actividad w-full px-2 py-1 text-sm border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-purple-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100" name="itinerarios[]">
                    <option value="">Selecciona...</option>
                </select>
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
        const cantidadInput = tr.querySelector('.package-item-cantidad');
        const destinoSelect = tr.querySelector('.package-item-destino');
        const categoriaSelect = tr.querySelector('.package-item-categoria');
        const actividadSelect = tr.querySelector('.package-item-actividad');

        tree.forEach((d) => destinoSelect.appendChild(new Option(d.nombre, d.id)));
        destinoSelect.value = destinoId ? String(destinoId) : '';
        poblarCategoriaSelect(categoriaSelect, destinoId, categoriaId);
        poblarActividadSelect(actividadSelect, destinoId, categoriaId, data.id ?? null);

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

        diaInput.addEventListener('input', () => {
            diaInput.classList.remove('border-red-500', 'focus:ring-red-500');
            tr.querySelector('.package-item-dia-error').classList.add('hidden');
        });

        cantidadInput.addEventListener('input', () => {
            cantidadInput.classList.remove('border-red-500', 'focus:ring-red-500');
            tr.querySelector('.package-item-cantidad-error').classList.add('hidden');
        });

        destinoSelect.addEventListener('change', () => {
            poblarCategoriaSelect(categoriaSelect, destinoSelect.value, '');
            poblarActividadSelect(actividadSelect, destinoSelect.value, categoriaSelect.value, '');
        });

        categoriaSelect.addEventListener('change', () => {
            poblarActividadSelect(actividadSelect, destinoSelect.value, categoriaSelect.value, '');
        });

        tbody.appendChild(tr);
        emptyMsg.classList.add('hidden');

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
