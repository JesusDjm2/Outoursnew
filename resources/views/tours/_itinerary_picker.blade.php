@php
    $destinos = $destinos ?? collect();
    $paquetesItinerario = \App\Models\ItineraryPackage::orderBy('nombre')->get(['id', 'nombre', 'dias']);
    $catalogoItinerarios = \App\Models\Itinerary::orderBy('nombre')->get(['id', 'nombre', 'costo', 'costo_promo']);
    $initialItinerariosData = $itinerariosSeleccionados->values()->map(fn($it, $i) => [
        'id' => $it->id,
        'nombre' => $it->nombre,
        'costo' => $it->costo,
        'costo_promo' => $it->costo_promo,
        'fecha' => $it->pivot->fecha ?? null,
        'cantidad' => $it->pivot->cantidad_pax ?? null,
        'fecha_error' => $errors->first("itinerarios_fecha.$i"),
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
        window.initialItinerarios = @json($initialItinerariosData);
        window.tourPickerCategoryTree = @json($categoryTreeJson);
        window.itinerarioCatalogo = @json($catalogoItinerarios);
    </script>

    <datalist id="tours-datalist">
        @foreach($catalogoItinerarios->unique('nombre') as $it)
            <option value="{{ $it->nombre }}"></option>
        @endforeach
    </datalist>

    @if($paquetesItinerario->isNotEmpty())
    <div class="rounded-lg border border-purple-200 bg-purple-50 p-3 mb-4 dark:border-purple-900/50 dark:bg-purple-950/20">
        <p class="text-xs font-medium text-purple-700 dark:text-purple-300 mb-2">¿Deseas usar un paquete de itinerarios ya creado?</p>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Paquete</label>
                <select id="paquete-select" class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
                    <option value="">No usar paquete...</option>
                    @foreach($paquetesItinerario as $paquete)
                        <option value="{{ $paquete->id }}">{{ $paquete->nombre }} ({{ $paquete->dias }} {{ $paquete->dias == 1 ? 'día' : 'días' }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Fecha de inicio (Día 1)</label>
                <input type="date" id="paquete-fecha-inicio" class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
            </div>
            <button type="button" id="aplicar-paquete-btn" class="bg-purple-600 text-white px-4 py-1.5 rounded-lg text-sm hover:bg-purple-700 transition disabled:opacity-50" disabled>
                <i class="fas fa-check"></i> Aplicar paquete
            </button>
        </div>
        <p class="text-[11px] text-purple-500 dark:text-purple-400 mt-2">Se agregan las filas del paquete a las que ya tengas; usa "Limpiar" abajo si quieres empezar de cero.</p>
    </div>
    @endif

    @if($destinos->isNotEmpty())
    <div class="rounded-lg border border-gray-200 dark:border-slate-700 p-3 mb-4">
        <p class="text-xs font-medium text-gray-500 dark:text-slate-400 mb-2">Buscar por clasificación (recomendado si hay muchos tours)</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Destino</label>
                <select id="picker-destino" class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
                    <option value="">Selecciona...</option>
                    @foreach($destinos as $destino)
                        <option value="{{ $destino->id }}">{{ $destino->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Categoría <span class="text-gray-400">(opcional)</span></label>
                <select id="picker-categoria" class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100" disabled>
                    <option value="">Elige un destino...</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Tour / Actividad</label>
                <select id="picker-itinerario" class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100" disabled>
                    <option value="">Elige un destino...</option>
                </select>
            </div>
        </div>
        <button type="button" id="add-fila-desde-picker-btn" class="mt-3 bg-purple-600 text-white px-4 py-1.5 rounded-lg text-sm hover:bg-purple-700 transition disabled:opacity-50" disabled>
            <i class="fas fa-plus"></i> Agregar al Tour
        </button>
    </div>
    @endif

    <div class="flex items-center justify-end gap-2 mb-3">
        <button type="button" id="add-fila-btn" class="bg-purple-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-purple-700 transition">
            <i class="fas fa-plus"></i> Fila
        </button>
        <button type="button" id="limpiar-filas-btn" class="bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg text-sm hover:bg-gray-300 transition dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600">
            Limpiar
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm border-collapse">
            <thead>
                <tr class="text-left text-xs uppercase text-gray-500 dark:text-slate-400 border-b border-gray-200 dark:border-slate-700">
                    <th class="w-6"></th>
                    <th class="py-2 pr-2 font-medium">Fecha</th>
                    <th class="py-2 pr-2 font-medium">Tour / Actividad</th>
                    <th class="py-2 pr-2 font-medium w-20">Cant.</th>
                    <th class="py-2 pr-2 font-medium w-24">Distr.</th>
                    <th class="py-2 pr-2 font-medium w-28">Total Línea</th>
                    <th class="py-2 font-medium w-10">Acc.</th>
                </tr>
            </thead>
            <tbody id="itinerarios-seleccionados"></tbody>
        </table>
    </div>
    <p id="itinerarios-empty-msg" class="text-sm text-gray-400 dark:text-slate-500 mt-2">
        Aún no agregaste filas. Usa "+ Fila" para comenzar.
    </p>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const tbody = document.getElementById('itinerarios-seleccionados');
    const emptyMsg = document.getElementById('itinerarios-empty-msg');
    const paxAdultosInput = document.querySelector('input[name="pax_adultos"]');
    window.itinerarioCostos = window.itinerarioCostos || {};
    window.itinerarioCostosPromo = window.itinerarioCostosPromo || {};

    const catalogoPorNombre = {};
    (window.itinerarioCatalogo || []).forEach((it) => {
        catalogoPorNombre[it.nombre.trim().toLowerCase()] = it;
        window.itinerarioCostos[it.id] = it.costo;
        window.itinerarioCostosPromo[it.id] = it.costo_promo ?? it.costo;
    });

    let rowSeq = 0;
    let draggedRow = null;

    const form = tbody.closest('form');
    form?.addEventListener('submit', () => {
        tbody.querySelectorAll('.itinerario-row').forEach((tr) => {
            const idInput = tr.querySelector('.itinerario-id-input');
            const resolved = !!idInput.value;
            tr.querySelector('.itinerario-fecha').disabled = !resolved;
            tr.querySelector('.itinerario-cantidad').disabled = !resolved;
            idInput.disabled = !resolved;
        });
    });

    function recomputeRow(tr) {
        const cantidadInput = tr.querySelector('.itinerario-cantidad');
        const totalInput = tr.querySelector('.itinerario-total');
        const distrInput = tr.querySelector('.itinerario-distr');
        const idInput = tr.querySelector('.itinerario-id-input');
        const cant = parseFloat(cantidadInput.value || '0') || 0;
        const regular = parseFloat(window.itinerarioCostos[idInput.value] ?? 0) || 0;
        const promo = parseFloat(window.itinerarioCostosPromo[idInput.value] ?? regular) || 0;
        const total = promo * cant;
        totalInput.value = total ? total.toFixed(2) : '';
        distrInput.value = cant ? (total / cant).toFixed(2) : '';
    }

    function setRowResolved(tr, resolved) {
        const noMatchMsg = tr.querySelector('.itinerario-no-match-msg');
        noMatchMsg.classList.toggle('hidden', resolved);
    }

    function applyMatch(tr, tourInput, match) {
        const idInput = tr.querySelector('.itinerario-id-input');
        if (match) {
            idInput.value = match.id;
            window.itinerarioCostos[match.id] = match.costo;
            window.itinerarioCostosPromo[match.id] = match.costo_promo ?? match.costo;
            setRowResolved(tr, true);
        } else {
            idInput.value = '';
            setRowResolved(tr, false);
        }
        recomputeRow(tr);
        window.recomputeResumenFactura?.();
    }

    function createRow(data) {
        rowSeq++;
        const rowId = 'row-' + rowSeq;
        const esFilaCargada = data.cantidad !== undefined && data.cantidad !== null;

        const tr = document.createElement('tr');
        tr.className = 'itinerario-row border-b border-gray-100 dark:border-slate-800';
        tr.draggable = true;
        tr.innerHTML = `
            <td class="text-gray-400 dark:text-slate-500 align-top pt-2 cursor-grab" title="Arrastrar para reordenar">
                <i class="fas fa-grip-vertical"></i>
            </td>
            <td class="py-1.5 pr-2 align-top">
                <input type="date" class="itinerario-fecha w-full px-2 py-1 text-sm border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-purple-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100" name="itinerarios_fecha[]">
                <p class="itinerario-fecha-error text-[11px] text-red-600 dark:text-red-400 mt-0.5 hidden"></p>
            </td>
            <td class="py-1.5 pr-2 align-top">
                <input type="text" list="tours-datalist" id="${rowId}-tour" class="itinerario-tour-input w-full px-2 py-1 text-sm border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-purple-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100" placeholder="Escribe o elige un tour...">
                <input type="hidden" class="itinerario-id-input" name="itinerarios[]">
                <p class="itinerario-no-match-msg text-[11px] text-amber-600 dark:text-amber-400 mt-0.5 hidden">No coincide con ningún tour del catálogo.</p>
            </td>
            <td class="py-1.5 pr-2 align-top">
                <input type="number" min="1" class="itinerario-cantidad w-full px-2 py-1 text-sm border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-purple-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100" name="itinerarios_cantidad[]">
                <p class="itinerario-cantidad-error text-[11px] text-red-600 dark:text-red-400 mt-0.5 hidden"></p>
            </td>
            <td class="py-1.5 pr-2 align-top">
                <input type="text" disabled class="itinerario-distr w-full px-2 py-1 text-sm border border-gray-200 rounded-lg bg-gray-100 text-gray-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-500">
            </td>
            <td class="py-1.5 pr-2 align-top">
                <input type="text" disabled class="itinerario-total w-full px-2 py-1 text-sm border border-gray-200 rounded-lg bg-gray-100 text-gray-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-500">
            </td>
            <td class="py-1.5 align-top">
                <button type="button" class="remove-itinerario-btn text-red-600 hover:text-red-800 dark:text-red-400"><i class="fas fa-trash"></i></button>
            </td>
        `;

        const fechaInput = tr.querySelector('.itinerario-fecha');
        const tourInput = tr.querySelector('.itinerario-tour-input');
        const idInput = tr.querySelector('.itinerario-id-input');
        const cantidadInput = tr.querySelector('.itinerario-cantidad');

        fechaInput.value = data.fecha || '';
        cantidadInput.value = data.cantidad ?? (parseFloat(paxAdultosInput?.value || '1') || 1);
        cantidadInput.dataset.autoMode = esFilaCargada ? 'false' : 'true';

        if (data.fecha_error) {
            fechaInput.classList.add('border-red-500', 'focus:ring-red-500');
            const fechaErrorMsg = tr.querySelector('.itinerario-fecha-error');
            fechaErrorMsg.textContent = data.fecha_error;
            fechaErrorMsg.classList.remove('hidden');
        }
        if (data.cantidad_error) {
            cantidadInput.classList.add('border-red-500', 'focus:ring-red-500');
            const cantidadErrorMsg = tr.querySelector('.itinerario-cantidad-error');
            cantidadErrorMsg.textContent = data.cantidad_error;
            cantidadErrorMsg.classList.remove('hidden');
        }

        let ultimoValorValido = '';

        fechaInput.addEventListener('input', () => {
            fechaInput.classList.remove('border-red-500', 'focus:ring-red-500');
            tr.querySelector('.itinerario-fecha-error').classList.add('hidden');
            window.recomputeResumenFactura?.();
        });

        cantidadInput.addEventListener('input', () => {
            cantidadInput.dataset.autoMode = 'false';
            cantidadInput.classList.remove('border-red-500', 'focus:ring-red-500');
            tr.querySelector('.itinerario-cantidad-error').classList.add('hidden');
            recomputeRow(tr);
            window.recomputeResumenFactura?.();
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
                tr.querySelector('.itinerario-id-input').value = '';
                setRowResolved(tr, true);
                recomputeRow(tr);
                window.recomputeResumenFactura?.();
                return;
            }
            const match = catalogoPorNombre[value.toLowerCase()];
            if (match) {
                ultimoValorValido = match.nombre;
            }
            applyMatch(tr, tourInput, match || null);
        });

        tbody.appendChild(tr);
        emptyMsg.classList.add('hidden');

        if (data.id) {
            tourInput.value = data.nombre;
            ultimoValorValido = data.nombre;
            idInput.value = data.id;
            window.itinerarioCostos[data.id] = data.costo;
            window.itinerarioCostosPromo[data.id] = data.costo_promo ?? data.costo;
        }
        setRowResolved(tr, true);

        recomputeRow(tr);

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

    if (paxAdultosInput) {
        paxAdultosInput.addEventListener('input', () => {
            tbody.querySelectorAll('.itinerario-row').forEach((tr) => {
                const cantidadInput = tr.querySelector('.itinerario-cantidad');
                if (cantidadInput.dataset.autoMode === 'true') {
                    cantidadInput.value = parseFloat(paxAdultosInput.value || '0') || 0;
                    recomputeRow(tr);
                }
            });
            window.recomputeResumenFactura?.();
        });
    }

    const destinoSelect = document.getElementById('picker-destino');
    if (destinoSelect) {
        const categoriaSelect = document.getElementById('picker-categoria');
        const itinerarioSelect = document.getElementById('picker-itinerario');
        const addFromPickerBtn = document.getElementById('add-fila-desde-picker-btn');
        const tree = window.tourPickerCategoryTree || [];

        function resetSelect(select, placeholder, disabled) {
            select.innerHTML = '';
            select.appendChild(new Option(placeholder, ''));
            select.disabled = disabled;
        }

        function fetchItinerariosParaPicker() {
            resetSelect(itinerarioSelect, 'Cargando...', true);
            addFromPickerBtn.disabled = true;
            const params = new URLSearchParams();
            if (destinoSelect.value) params.set('destino_id', destinoSelect.value);
            if (categoriaSelect.value) params.set('categoria_id', categoriaSelect.value);
            fetch('{{ route('itineraries.search') }}?' + params.toString())
                .then((res) => res.json())
                .then((items) => {
                    resetSelect(itinerarioSelect, items.length ? 'Selecciona...' : 'Sin resultados con este filtro', false);
                    items.forEach((it) => {
                        const precios = [];
                        if (it.costo !== null && it.costo !== undefined && it.costo !== '') precios.push('$' + parseFloat(it.costo).toFixed(2));
                        if (it.costo_promo !== null && it.costo_promo !== undefined && it.costo_promo !== '') precios.push('promo $' + parseFloat(it.costo_promo).toFixed(2));
                        const sufijo = precios.length ? ' — ' + precios.join(' / ') : '';
                        const opt = new Option(it.nombre + sufijo, it.id);
                        opt.dataset.nombre = it.nombre;
                        opt.dataset.costo = it.costo ?? '';
                        opt.dataset.costoPromo = it.costo_promo ?? '';
                        itinerarioSelect.appendChild(opt);
                    });
                });
        }

        destinoSelect.addEventListener('change', () => {
            const destino = tree.find((d) => String(d.id) === String(destinoSelect.value));
            resetSelect(categoriaSelect, destino ? 'Todas' : 'Elige un destino...', !destino);
            if (destino) {
                destino.categorias.forEach((c) => categoriaSelect.appendChild(new Option(c.nombre, c.id)));
            }
            if (destino) {
                fetchItinerariosParaPicker();
            } else {
                resetSelect(itinerarioSelect, 'Elige un destino...', true);
                addFromPickerBtn.disabled = true;
            }
        });

        categoriaSelect.addEventListener('change', fetchItinerariosParaPicker);

        itinerarioSelect.addEventListener('change', () => {
            addFromPickerBtn.disabled = !itinerarioSelect.value;
        });

        addFromPickerBtn.addEventListener('click', () => {
            if (!itinerarioSelect.value) return;
            const opt = itinerarioSelect.options[itinerarioSelect.selectedIndex];
            createRow({
                id: itinerarioSelect.value,
                nombre: opt.dataset.nombre,
                costo: opt.dataset.costo || null,
                costo_promo: opt.dataset.costoPromo || null,
            });
            window.recomputeResumenFactura?.();
        });
    }

    (window.initialItinerarios || []).forEach((it) => createRow(it));
    window.recomputeResumenFactura?.();

    document.getElementById('add-fila-btn').addEventListener('click', () => {
        createRow({});
        window.recomputeResumenFactura?.();
    });

    const paqueteSelect = document.getElementById('paquete-select');
    const paqueteFechaInicio = document.getElementById('paquete-fecha-inicio');
    const aplicarPaqueteBtn = document.getElementById('aplicar-paquete-btn');

    function actualizarEstadoBotonPaquete() {
        if (!aplicarPaqueteBtn) return;
        aplicarPaqueteBtn.disabled = !paqueteSelect.value || !paqueteFechaInicio.value;
    }
    paqueteSelect?.addEventListener('change', actualizarEstadoBotonPaquete);
    paqueteFechaInicio?.addEventListener('input', actualizarEstadoBotonPaquete);

    aplicarPaqueteBtn?.addEventListener('click', () => {
        if (!paqueteSelect.value || !paqueteFechaInicio.value) return;
        aplicarPaqueteBtn.disabled = true;

        fetch(`/itinerary-packages/${paqueteSelect.value}/items`)
            .then((res) => res.json())
            .then((items) => {
                const inicio = new Date(paqueteFechaInicio.value + 'T00:00:00');
                items.forEach((item) => {
                    const fecha = new Date(inicio);
                    fecha.setDate(fecha.getDate() + (parseInt(item.dia, 10) || 1) - 1);
                    const fechaStr = fecha.toISOString().slice(0, 10);
                    createRow({
                        id: item.id,
                        nombre: item.nombre,
                        costo: item.costo,
                        costo_promo: item.costo_promo,
                        fecha: fechaStr,
                        cantidad: item.cantidad_pax_defecto ?? (parseFloat(paxAdultosInput?.value || '1') || 1),
                    });
                });
                window.recomputeResumenFactura?.();
                paqueteSelect.value = '';
            })
            .finally(() => actualizarEstadoBotonPaquete());
    });

    document.getElementById('limpiar-filas-btn').addEventListener('click', () => {
        tbody.innerHTML = '';
        emptyMsg.classList.remove('hidden');
        window.recomputeResumenFactura?.();
    });

    tbody.addEventListener('click', (e) => {
        const removeBtn = e.target.closest('.remove-itinerario-btn');
        if (removeBtn) {
            removeBtn.closest('tr').remove();
            if (!tbody.querySelector('tr')) emptyMsg.classList.remove('hidden');
            window.recomputeResumenFactura?.();
        }
    });
});
</script>
