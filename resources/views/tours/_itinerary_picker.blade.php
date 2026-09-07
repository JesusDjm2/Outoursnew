@php
    $destinos = $destinos ?? collect();
    $paqueteSeleccionadoId = $paqueteSeleccionadoId ?? null;
    $mostrarSelectorPaquetes = $mostrarSelectorPaquetes ?? false;
    $paquetesItinerario = $mostrarSelectorPaquetes
        ? \App\Models\ItineraryPackage::orderBy('nombre')->get(['id', 'nombre', 'dias'])
        : collect();
    $catalogoItinerarios = \App\Models\Itinerary::orderBy('nombre')
        ->get(['id', 'nombre', 'codigo', 'costo', 'costo_promo', 'costo_nino', 'costo_promo_nino', 'destino_id', 'categoria_id']);
    $initialItinerariosData = $itinerariosSeleccionados->values()->map(fn($it, $i) => [
        'id' => $it->id,
        'nombre' => $it->nombre,
        'costo' => $it->costo,
        'costo_promo' => $it->costo_promo,
        'costo_nino' => $it->costo_nino,
        'costo_promo_nino' => $it->costo_promo_nino,
        'destino_id' => $it->destino_id,
        'categoria_id' => $it->categoria_id,
        'fecha' => $it->pivot->fecha ?? null,
        'cantidad' => $it->pivot->cantidad_pax ?? null,
        'cantidad_ninos' => $it->pivot->cantidad_pax_ninos ?? null,
        'fecha_error' => $errors->first("itinerarios_fecha.$i"),
        'cantidad_error' => $errors->first("itinerarios_cantidad.$i"),
    ])->values();

    $paqueteYaAplicado = $mostrarSelectorPaquetes && $paqueteSeleccionadoId;
    $paqueteSeleccionadoModel = null;
    if ($paqueteYaAplicado) {
        $paqueteSeleccionadoModel = \App\Models\ItineraryPackage::find($paqueteSeleccionadoId);
        if ($paqueteSeleccionadoModel && empty(old('itinerarios'))) {
            $paqueteSeleccionadoModel->load('itineraries');
            $itemsDelPaquete = $paqueteSeleccionadoModel->itineraries->values()->map(fn($it) => [
                'id' => $it->id,
                'nombre' => $it->nombre,
                'costo' => $it->costo,
                'costo_promo' => $it->costo_promo,
                'costo_nino' => $it->costo_nino,
                'costo_promo_nino' => $it->costo_promo_nino,
                'destino_id' => $it->destino_id,
                'categoria_id' => $it->categoria_id,
                'fecha' => null,
                'cantidad' => $it->pivot->cantidad_pax_defecto,
                'cantidad_ninos' => null,
                'fecha_error' => null,
                'cantidad_error' => null,
            ]);
            $initialItinerariosData = $initialItinerariosData->concat($itemsDelPaquete)->values();
        }
    }
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

    @if($paqueteYaAplicado && $paqueteSeleccionadoModel)
    <div id="paquete-cargado-aviso" class="rounded-lg border border-purple-200 bg-purple-50 p-3 mb-4 dark:border-purple-900/50 dark:bg-purple-950/20 flex items-start justify-between gap-3">
        <p class="text-xs font-medium text-purple-700 dark:text-purple-300"><i class="fas fa-circle-check"></i> Se cargaron las actividades del paquete "{{ $paqueteSeleccionadoModel->nombre }}". Solo falta poner la fecha de cada día en la lista de abajo.</p>
        <button type="button" onclick="document.getElementById('paquete-cargado-aviso').remove()" class="text-purple-500 hover:text-purple-700 dark:text-purple-300 dark:hover:text-purple-100" title="Cerrar">
            <i class="fas fa-xmark"></i>
        </button>
    </div>
    @endif

    <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
        @if($mostrarSelectorPaquetes && $paquetesItinerario->isNotEmpty() && !$paqueteYaAplicado)
        <div class="flex flex-wrap items-center gap-2 text-xs text-gray-500 dark:text-slate-400">
            <i class="fas fa-layer-group text-gray-400 dark:text-slate-500"></i>
            <span>¿Partir de un paquete predeterminado?</span>
            <select id="paquete-select" class="px-2 py-1 text-xs border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-purple-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
                <option value="">Elegir paquete...</option>
                @foreach($paquetesItinerario as $paquete)
                    <option value="{{ $paquete->id }}">{{ $paquete->nombre }} ({{ $paquete->dias }} {{ $paquete->dias == 1 ? 'día' : 'días' }})</option>
                @endforeach
            </select>
            <button type="button" id="aplicar-paquete-btn" class="text-purple-600 hover:text-purple-800 font-medium disabled:opacity-40 disabled:cursor-not-allowed dark:text-purple-400 dark:hover:text-purple-300" disabled>
                <i class="fas fa-check"></i> Aplicar
            </button>
        </div>
        @else
        <span></span>
        @endif
        <button type="button" id="limpiar-filas-btn" class="bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg text-sm hover:bg-gray-300 transition dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600">
            Limpiar
        </button>
    </div>

    <div id="proveedor-quick-add" class="hidden fixed inset-0 z-[60] items-center justify-center bg-black/40 p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-sm p-5 dark:bg-slate-800 dark:border dark:border-slate-700">
            <div class="flex items-start justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-slate-100">
                    <i class="fas fa-truck-fast text-amber-600 mr-1"></i> Agregar proveedor
                </h3>
                <button type="button" id="proveedor-quick-add-close" class="text-gray-400 hover:text-gray-600 dark:text-slate-500 dark:hover:text-slate-300">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>
            <p class="text-xs text-gray-500 dark:text-slate-400 mb-3">Para <strong id="proveedor-quick-add-dia" class="text-gray-700 dark:text-slate-200"></strong></p>
            <select id="proveedor-quick-add-select" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-amber-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 mb-4">
                <option value="">Elegir proveedor...</option>
                @foreach(($proveedores ?? []) as $proveedorOption)
                    <option value="{{ $proveedorOption->id }}">{{ $proveedorOption->nombre }} ({{ $proveedorOption->tipo?->nombre ?? 'Sin tipo' }})</option>
                @endforeach
            </select>
            <div class="flex justify-end gap-2">
                <button type="button" id="proveedor-quick-add-cancel" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 dark:text-slate-400 dark:hover:text-slate-200">
                    Cancelar
                </button>
                <button type="button" id="proveedor-quick-add-confirm" class="bg-amber-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-amber-700 transition">
                    <i class="fas fa-check"></i> Agregar
                </button>
            </div>
        </div>
    </div>

    <div id="actividad-picker-panel" class="hidden fixed z-50 w-72 max-h-80 overflow-y-auto bg-white border border-gray-200 rounded-lg shadow-xl dark:bg-slate-800 dark:border-slate-700">
        <div class="p-2 border-b border-gray-100 dark:border-slate-700 sticky top-0 bg-white dark:bg-slate-800">
            <input type="text" id="actividad-picker-buscar" class="w-full px-2 py-1 text-xs border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-purple-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" placeholder="Buscar actividad...">
        </div>
        <div id="actividad-picker-arbol" class="text-sm py-1"></div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm border-collapse">
            <thead>
                <tr class="text-left text-xs uppercase text-gray-500 dark:text-slate-400 border-b border-gray-200 dark:border-slate-700">
                    <th class="w-6"></th>
                    <th class="py-2 pr-2 font-medium w-14">Día</th>
                    <th class="py-2 pr-2 font-medium w-36">Fecha</th>
                    <th class="py-2 pr-2 font-medium min-w-[280px]">Actividad</th>
                    <th class="py-2 pr-2 font-medium w-16">Adultos</th>
                    <th class="py-2 pr-2 font-medium w-16">Niños</th>
                    <th class="py-2 pr-2 font-medium w-24">P. Confidencial</th>
                    <th class="py-2 pr-2 font-medium w-28">Total (Venta)</th>
                    <th class="py-2 font-medium w-24">Acc.</th>
                </tr>
            </thead>
            <tbody id="itinerarios-seleccionados"></tbody>
        </table>
    </div>
    <p id="itinerarios-empty-msg" class="text-sm text-gray-400 dark:text-slate-500 mt-2">
        Aún no agregaste filas. Usa "+ Fila" para comenzar.
    </p>

    <div class="mt-3">
        <button type="button" id="add-fila-btn" class="bg-purple-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-purple-700 transition">
            <i class="fas fa-plus"></i> Fila
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const tbody = document.getElementById('itinerarios-seleccionados');
    const emptyMsg = document.getElementById('itinerarios-empty-msg');
    const paxAdultosInput = document.querySelector('input[name="pax_adultos"]');
    const paxNinosInput = document.querySelector('input[name="pax_ninos"]');
    const tree = window.tourPickerCategoryTree || [];
    const catalogo = window.itinerarioCatalogo || [];
    window.itinerarioCostos = window.itinerarioCostos || {};
    window.itinerarioCostosPromo = window.itinerarioCostosPromo || {};
    window.itinerarioCostosNino = window.itinerarioCostosNino || {};
    window.itinerarioCostosPromoNino = window.itinerarioCostosPromoNino || {};

    catalogo.forEach((it) => {
        window.itinerarioCostos[it.id] = it.costo;
        window.itinerarioCostosPromo[it.id] = it.costo_promo ?? it.costo;
        window.itinerarioCostosNino[it.id] = it.costo_nino;
        window.itinerarioCostosPromoNino[it.id] = it.costo_promo_nino ?? it.costo_nino;
    });

    function actualizarVistaPorDia() {
        const rows = Array.from(tbody.querySelectorAll('.itinerario-row'));

        const fechas = Array.from(new Set(rows.map((tr) => tr.querySelector('.itinerario-fecha').value).filter(Boolean))).sort();

        const rowsPorFecha = {};
        const rowsSinFecha = [];
        rows.forEach((tr) => {
            const v = tr.querySelector('.itinerario-fecha').value;
            if (v) (rowsPorFecha[v] = rowsPorFecha[v] || []).push(tr);
            else rowsSinFecha.push(tr);
        });

        fechas.forEach((fecha) => (rowsPorFecha[fecha] || []).forEach((tr) => tbody.appendChild(tr)));
        rowsSinFecha.forEach((tr) => tbody.appendChild(tr));

        rows.forEach((tr) => {
            const val = tr.querySelector('.itinerario-fecha').value;
            const diaLabel = tr.querySelector('.itinerario-dia-label');
            if (!diaLabel) return;
            diaLabel.textContent = val ? `Día ${fechas.indexOf(val) + 1}` : '—';
        });
    }

    window.actualizarVistaPorDia = actualizarVistaPorDia;

    function agregarHotelParaDia(fecha) {
        const addHospedajeBtn = document.getElementById('add-hospedaje-btn');
        if (!addHospedajeBtn) {
            alert('Primero registra un hotel en el catálogo para poder agregarlo aquí.');
            return;
        }
        addHospedajeBtn.click();
        requestAnimationFrame(() => {
            const blocks = document.querySelectorAll('#hospedajes-container .hospedaje-block');
            const block = blocks[blocks.length - 1];
            if (!block) return;

            if (fecha) {
                const fechaIngreso = block.querySelector('[name*="[fecha_ingreso]"]');
                const fechaSalida = block.querySelector('[name*="[fecha_salida]"]');
                if (fechaIngreso) {
                    fechaIngreso.value = fecha;
                    fechaIngreso.dispatchEvent(new Event('input'));
                }
                if (fechaSalida) {
                    const salida = new Date(fecha + 'T00:00:00');
                    salida.setDate(salida.getDate() + 1);
                    fechaSalida.value = salida.toISOString().slice(0, 10);
                    fechaSalida.dispatchEvent(new Event('input'));
                }
            }

            block.scrollIntoView({ behavior: 'smooth', block: 'center' });
            block.classList.add('ring-2', 'ring-cyan-400');
            setTimeout(() => block.classList.remove('ring-2', 'ring-cyan-400'), 1500);
            block.querySelector('.hospedaje-hotel-select')?.focus();
            actualizarVistaPorDia();
        });
    }

    const proveedorQuickAdd = document.getElementById('proveedor-quick-add');
    const proveedorQuickAddDia = document.getElementById('proveedor-quick-add-dia');
    const proveedorQuickAddSelect = document.getElementById('proveedor-quick-add-select');
    let proveedorQuickAddActive = false;
    let proveedorQuickAddFecha = '';

    function abrirSelectorProveedor(fecha, diaLabel) {
        if (!proveedorQuickAdd) return;
        proveedorQuickAddActive = true;
        proveedorQuickAddFecha = fecha || '';
        proveedorQuickAddDia.textContent = diaLabel;
        proveedorQuickAddSelect.value = '';
        proveedorQuickAdd.classList.remove('hidden');
        proveedorQuickAdd.classList.add('flex');
        proveedorQuickAddSelect.focus();
    }

    function cerrarSelectorProveedor() {
        proveedorQuickAddActive = false;
        proveedorQuickAdd.classList.add('hidden');
        proveedorQuickAdd.classList.remove('flex');
    }

    document.getElementById('proveedor-quick-add-cancel')?.addEventListener('click', cerrarSelectorProveedor);
    document.getElementById('proveedor-quick-add-close')?.addEventListener('click', cerrarSelectorProveedor);
    proveedorQuickAdd?.addEventListener('click', (e) => {
        if (e.target === proveedorQuickAdd) cerrarSelectorProveedor();
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && proveedorQuickAddActive) cerrarSelectorProveedor();
    });

    document.getElementById('proveedor-quick-add-confirm')?.addEventListener('click', () => {
        const proveedorId = proveedorQuickAddSelect.value;
        if (!proveedorId || !proveedorQuickAddActive) return;

        const checkbox = document.querySelector(`input[name="proveedores[]"][value="${proveedorId}"]`);
        if (!checkbox) {
            alert('No se encontró ese proveedor en la lista de abajo.');
            return;
        }
        checkbox.checked = true;
        checkbox.dispatchEvent(new Event('change'));

        if (proveedorQuickAddFecha) {
            const wrap = checkbox.closest('label')?.querySelector('.proveedor-fechas-wrap');
            if (wrap) window.agregarFechaProveedor?.(wrap, proveedorQuickAddFecha);
        }

        const card = checkbox.closest('label');
        card?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        card?.classList.add('ring-2', 'ring-amber-400');
        setTimeout(() => card?.classList.remove('ring-2', 'ring-amber-400'), 1500);

        cerrarSelectorProveedor();
        actualizarVistaPorDia();
    });

    let rowSeq = 0;
    let draggedRow = null;

    const form = tbody.closest('form');
    form?.addEventListener('submit', () => {
        tbody.querySelectorAll('.itinerario-row').forEach((tr) => {
            const actividadSelect = tr.querySelector('.itinerario-actividad');
            const resolved = !!actividadSelect.value;
            tr.querySelector('.itinerario-fecha').disabled = !resolved;
            tr.querySelector('.itinerario-cantidad').disabled = !resolved;
            tr.querySelector('.itinerario-cantidad-ninos').disabled = !resolved;
            actividadSelect.disabled = !resolved;
        });
    });

    function recomputeRow(tr) {
        const cantidadInput = tr.querySelector('.itinerario-cantidad');
        const cantidadNinosInput = tr.querySelector('.itinerario-cantidad-ninos');
        const totalInput = tr.querySelector('.itinerario-total');
        const distrInput = tr.querySelector('.itinerario-distr');
        const actividadSelect = tr.querySelector('.itinerario-actividad');
        const cant = parseFloat(cantidadInput.value || '0') || 0;
        const cantNinos = parseFloat(cantidadNinosInput.value || '0') || 0;
        const regular = parseFloat(window.itinerarioCostos[actividadSelect.value] ?? 0) || 0;
        const promo = parseFloat(window.itinerarioCostosPromo[actividadSelect.value] ?? regular) || 0;
        const regularNino = parseFloat(window.itinerarioCostosNino[actividadSelect.value] ?? 0) || 0;
        const promoNino = parseFloat(window.itinerarioCostosPromoNino[actividadSelect.value] ?? regularNino) || 0;
        const confidencial = (regular * cant) + (regularNino * cantNinos);
        const total = (promo * cant) + (promoNino * cantNinos);
        totalInput.value = total ? total.toFixed(2) : '';
        distrInput.value = confidencial ? confidencial.toFixed(2) : '';
    }

    function escapeHtml(str) {
        return String(str).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    }

    function buildArbolActividades() {
        const arbol = [];
        tree.forEach((destino) => {
            const actividadesDestino = catalogo.filter((it) => String(it.destino_id) === String(destino.id));
            if (!actividadesDestino.length) return;
            const categorias = [];
            (destino.categorias || []).forEach((categoria) => {
                const actividadesCategoria = actividadesDestino.filter((it) => String(it.categoria_id) === String(categoria.id));
                if (actividadesCategoria.length) categorias.push({ id: categoria.id, nombre: categoria.nombre, actividades: actividadesCategoria });
            });
            const sinCategoria = actividadesDestino.filter((it) => !it.categoria_id);
            if (sinCategoria.length) categorias.push({ id: '', nombre: 'Sin categoría', actividades: sinCategoria });
            arbol.push({ id: destino.id, nombre: destino.nombre, categorias });
        });
        return arbol;
    }

    function renderArbolHtml(arbol) {
        return arbol.map((destino) => `
            <div class="arbol-destino">
                <button type="button" class="arbol-destino-toggle w-full flex items-center justify-between gap-2 px-2.5 py-1.5 text-left font-medium text-gray-700 dark:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-700">
                    <span class="truncate">${escapeHtml(destino.nombre)}</span>
                    <i class="fas fa-chevron-right text-[10px] text-gray-400 shrink-0 transition-transform"></i>
                </button>
                <div class="arbol-categorias hidden pl-3 ml-2.5 border-l border-gray-100 dark:border-slate-700">
                    ${destino.categorias.map((categoria) => `
                        <div class="arbol-categoria">
                            <button type="button" class="arbol-categoria-toggle w-full flex items-center justify-between gap-2 px-2 py-1 text-left text-gray-600 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-700">
                                <span class="truncate">${escapeHtml(categoria.nombre)}</span>
                                <i class="fas fa-chevron-right text-[10px] text-gray-400 shrink-0 transition-transform"></i>
                            </button>
                            <div class="arbol-actividades hidden pl-3 ml-2.5 border-l border-gray-100 dark:border-slate-700">
                                ${categoria.actividades.map((act) => `
                                    <button type="button" class="arbol-actividad w-full text-left px-2 py-1 text-gray-700 dark:text-slate-200 hover:bg-purple-50 hover:text-purple-700 dark:hover:bg-purple-950/30 dark:hover:text-purple-200 truncate" data-id="${act.id}" data-nombre="${escapeHtml(act.nombre)}">${escapeHtml(act.nombre)}</button>
                                `).join('')}
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `).join('');
    }

    const actividadPanel = document.getElementById('actividad-picker-panel');
    const actividadBuscar = document.getElementById('actividad-picker-buscar');
    const actividadArbolEl = document.getElementById('actividad-picker-arbol');
    let actividadPickerTarget = null;

    if (actividadArbolEl) {
        actividadArbolEl.innerHTML = renderArbolHtml(buildArbolActividades());
    }

    function resetArbolFiltro() {
        actividadArbolEl.querySelectorAll('.arbol-destino, .arbol-categoria, .arbol-actividad').forEach((el) => el.classList.remove('hidden'));
        actividadArbolEl.querySelectorAll('.arbol-categorias, .arbol-actividades').forEach((el) => el.classList.add('hidden'));
        actividadArbolEl.querySelectorAll('.fa-chevron-right').forEach((i) => i.classList.remove('rotate-90'));
    }

    actividadArbolEl?.addEventListener('click', (e) => {
        const destinoToggle = e.target.closest('.arbol-destino-toggle');
        if (destinoToggle) {
            destinoToggle.nextElementSibling.classList.toggle('hidden');
            destinoToggle.querySelector('i').classList.toggle('rotate-90');
            return;
        }
        const categoriaToggle = e.target.closest('.arbol-categoria-toggle');
        if (categoriaToggle) {
            categoriaToggle.nextElementSibling.classList.toggle('hidden');
            categoriaToggle.querySelector('i').classList.toggle('rotate-90');
            return;
        }
        const actividadBtn = e.target.closest('.arbol-actividad');
        if (actividadBtn) {
            seleccionarActividadEnPanel(actividadBtn.dataset.id, actividadBtn.dataset.nombre);
        }
    });

    actividadBuscar?.addEventListener('input', () => {
        const q = actividadBuscar.value.trim().toLowerCase();
        if (!q) {
            resetArbolFiltro();
            return;
        }

        actividadArbolEl.querySelectorAll('.arbol-actividad').forEach((btn) => {
            btn.classList.toggle('hidden', !btn.dataset.nombre.toLowerCase().includes(q));
        });
        actividadArbolEl.querySelectorAll('.arbol-categoria').forEach((catEl) => {
            const visibles = catEl.querySelectorAll('.arbol-actividad:not(.hidden)').length;
            catEl.classList.toggle('hidden', visibles === 0);
            catEl.querySelector('.arbol-actividades').classList.toggle('hidden', visibles === 0);
        });
        actividadArbolEl.querySelectorAll('.arbol-destino').forEach((destEl) => {
            const visibles = destEl.querySelectorAll('.arbol-categoria:not(.hidden)').length;
            destEl.classList.toggle('hidden', visibles === 0);
            destEl.querySelector('.arbol-categorias').classList.toggle('hidden', visibles === 0);
        });
    });

    function abrirActividadPicker(trigger, hiddenInput, labelEl, tr) {
        if (!actividadPanel) return;
        actividadPickerTarget = { hiddenInput, labelEl, tr };

        const rect = trigger.getBoundingClientRect();
        const panelWidth = Math.max(rect.width, 288);
        const panelMaxHeight = 320;

        let left = Math.max(8, rect.left);
        if (left + panelWidth > window.innerWidth - 8) left = Math.max(8, window.innerWidth - panelWidth - 8);

        const spaceBelow = window.innerHeight - rect.bottom;
        actividadPanel.style.width = panelWidth + 'px';
        actividadPanel.style.left = left + 'px';
        if (spaceBelow < panelMaxHeight && rect.top > spaceBelow) {
            actividadPanel.style.top = 'auto';
            actividadPanel.style.bottom = (window.innerHeight - rect.top + 4) + 'px';
        } else {
            actividadPanel.style.bottom = 'auto';
            actividadPanel.style.top = (rect.bottom + 4) + 'px';
        }

        actividadPanel.classList.remove('hidden');
        actividadBuscar.value = '';
        resetArbolFiltro();
        actividadBuscar.focus();
    }

    function cerrarActividadPicker() {
        actividadPanel?.classList.add('hidden');
        actividadPickerTarget = null;
    }

    function seleccionarActividadEnPanel(id, nombre) {
        if (!actividadPickerTarget) return;
        const { hiddenInput, labelEl, tr } = actividadPickerTarget;
        hiddenInput.value = id;
        labelEl.textContent = nombre;
        labelEl.classList.remove('text-gray-400', 'dark:text-slate-500');
        recomputeRow(tr);
        window.recomputeResumenFactura?.();
        cerrarActividadPicker();
    }

    document.addEventListener('click', (e) => {
        if (!actividadPanel || actividadPanel.classList.contains('hidden')) return;
        if (actividadPanel.contains(e.target) || e.target.closest('.itinerario-actividad-trigger')) return;
        cerrarActividadPicker();
    });
    document.addEventListener('scroll', (e) => {
        if (!actividadPanel || actividadPanel.classList.contains('hidden')) return;
        if (actividadPanel.contains(e.target)) return;
        cerrarActividadPicker();
    }, true);
    window.addEventListener('resize', cerrarActividadPicker);

    function createRow(data) {
        rowSeq++;
        const esFilaCargada = data.cantidad !== undefined && data.cantidad !== null;

        const tr = document.createElement('tr');
        tr.className = 'itinerario-row border-b border-gray-100 dark:border-slate-800';
        tr.draggable = true;
        tr.innerHTML = `
            <td class="text-gray-400 dark:text-slate-500 align-top pt-2 cursor-grab" title="Arrastrar para reordenar">
                <i class="fas fa-grip-vertical"></i>
            </td>
            <td class="py-1.5 pr-2 align-top pt-2.5">
                <span class="itinerario-dia-label text-xs font-semibold text-gray-500 dark:text-slate-400">—</span>
            </td>
            <td class="py-1.5 pr-2 align-top">
                <input type="date" class="itinerario-fecha w-full px-2 py-1 text-sm border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-purple-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100" name="itinerarios_fecha[]">
                <p class="itinerario-fecha-error text-[11px] text-red-600 dark:text-red-400 mt-0.5 hidden"></p>
            </td>
            <td class="py-1.5 pr-2 align-top">
                <button type="button" class="itinerario-actividad-trigger w-full flex items-center justify-between gap-1 px-2 py-1 text-sm border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-purple-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
                    <span class="itinerario-actividad-label truncate text-gray-400 dark:text-slate-500">Selecciona actividad...</span>
                    <i class="fas fa-chevron-down text-[10px] text-gray-400 shrink-0"></i>
                </button>
                <input type="hidden" class="itinerario-actividad" name="itinerarios[]" value="">
            </td>
            <td class="py-1.5 pr-2 align-top">
                <input type="number" min="0" class="itinerario-cantidad w-full px-2 py-1 text-sm border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-purple-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100" name="itinerarios_cantidad[]">
                <p class="itinerario-cantidad-error text-[11px] text-red-600 dark:text-red-400 mt-0.5 hidden"></p>
            </td>
            <td class="py-1.5 pr-2 align-top">
                <input type="number" min="0" class="itinerario-cantidad-ninos w-full px-2 py-1 text-sm border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-purple-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100" name="itinerarios_cantidad_ninos[]">
            </td>
            <td class="py-1.5 pr-2 align-top">
                <input type="text" disabled title="Precio confidencial (solo lectura)" class="itinerario-distr w-full px-2 py-1 text-sm border border-gray-200 rounded-lg bg-gray-100 text-gray-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-500">
            </td>
            <td class="py-1.5 pr-2 align-top">
                <input type="text" disabled class="itinerario-total w-full px-2 py-1 text-sm border border-gray-200 rounded-lg bg-gray-100 text-gray-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-500">
            </td>
            <td class="py-1.5 align-top whitespace-nowrap">
                <button type="button" class="row-add-hotel-btn text-cyan-600 hover:text-cyan-800 dark:text-cyan-400 mr-2" title="Agregar hotel para esta actividad"><i class="fas fa-hotel"></i></button>
                <button type="button" class="row-add-proveedor-btn text-amber-600 hover:text-amber-800 dark:text-amber-400 mr-2" title="Agregar proveedor para esta actividad"><i class="fas fa-truck-fast"></i></button>
                <button type="button" class="remove-itinerario-btn text-red-600 hover:text-red-800 dark:text-red-400" title="Quitar fila"><i class="fas fa-trash"></i></button>
            </td>
        `;

        const fechaInput = tr.querySelector('.itinerario-fecha');
        const cantidadInput = tr.querySelector('.itinerario-cantidad');
        const cantidadNinosInput = tr.querySelector('.itinerario-cantidad-ninos');
        const actividadTrigger = tr.querySelector('.itinerario-actividad-trigger');
        const actividadLabel = tr.querySelector('.itinerario-actividad-label');
        const actividadSelect = tr.querySelector('.itinerario-actividad');

        if (data.id) {
            actividadSelect.value = data.id;
            actividadLabel.textContent = data.nombre || ('#' + data.id);
            actividadLabel.classList.remove('text-gray-400', 'dark:text-slate-500');
        }

        actividadTrigger.addEventListener('click', () => {
            abrirActividadPicker(actividadTrigger, actividadSelect, actividadLabel, tr);
        });

        const esFilaCargadaNinos = data.cantidad_ninos !== undefined && data.cantidad_ninos !== null;

        fechaInput.value = data.fecha || '';
        cantidadInput.value = data.cantidad ?? (parseFloat(paxAdultosInput?.value || '1') || 1);
        cantidadInput.dataset.autoMode = esFilaCargada ? 'false' : 'true';
        cantidadNinosInput.value = data.cantidad_ninos ?? (parseFloat(paxNinosInput?.value || '0') || 0);
        cantidadNinosInput.dataset.autoMode = esFilaCargadaNinos ? 'false' : 'true';

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

        fechaInput.addEventListener('input', () => {
            fechaInput.classList.remove('border-red-500', 'focus:ring-red-500');
            tr.querySelector('.itinerario-fecha-error').classList.add('hidden');
            actualizarVistaPorDia();
            window.recomputeResumenFactura?.();
        });

        cantidadInput.addEventListener('input', () => {
            cantidadInput.dataset.autoMode = 'false';
            cantidadInput.classList.remove('border-red-500', 'focus:ring-red-500');
            tr.querySelector('.itinerario-cantidad-error').classList.add('hidden');
            recomputeRow(tr);
            window.recomputeResumenFactura?.();
        });

        cantidadNinosInput.addEventListener('input', () => {
            cantidadNinosInput.dataset.autoMode = 'false';
            recomputeRow(tr);
            window.recomputeResumenFactura?.();
        });

        tr.querySelector('.row-add-hotel-btn').addEventListener('click', () => {
            agregarHotelParaDia(fechaInput.value || null);
        });

        tr.querySelector('.row-add-proveedor-btn').addEventListener('click', () => {
            const val = fechaInput.value;
            const nombreActividad = actividadLabel.textContent || 'esta actividad';
            const label = val
                ? new Date(val + 'T00:00:00').toLocaleDateString('es-PE', { day: '2-digit', month: '2-digit', year: 'numeric' })
                : nombreActividad + ' (sin fecha)';
            abrirSelectorProveedor(val || '', label);
        });

        tbody.appendChild(tr);
        emptyMsg.classList.add('hidden');

        recomputeRow(tr);
        actualizarVistaPorDia();

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

    if (paxNinosInput) {
        paxNinosInput.addEventListener('input', () => {
            tbody.querySelectorAll('.itinerario-row').forEach((tr) => {
                const cantidadNinosInput = tr.querySelector('.itinerario-cantidad-ninos');
                if (cantidadNinosInput.dataset.autoMode === 'true') {
                    cantidadNinosInput.value = parseFloat(paxNinosInput.value || '0') || 0;
                    recomputeRow(tr);
                }
            });
            window.recomputeResumenFactura?.();
        });
    }

    (window.initialItinerarios || []).forEach((it) => createRow(it));
    actualizarVistaPorDia();
    window.recomputeResumenFactura?.();

    document.getElementById('add-fila-btn').addEventListener('click', () => {
        createRow({});
        window.recomputeResumenFactura?.();
    });

    const paqueteSelect = document.getElementById('paquete-select');
    const aplicarPaqueteBtn = document.getElementById('aplicar-paquete-btn');

    function actualizarEstadoBotonPaquete() {
        if (!aplicarPaqueteBtn) return;
        aplicarPaqueteBtn.disabled = !paqueteSelect.value;
    }
    paqueteSelect?.addEventListener('change', actualizarEstadoBotonPaquete);

    aplicarPaqueteBtn?.addEventListener('click', () => {
        if (!paqueteSelect.value) return;
        aplicarPaqueteBtn.disabled = true;

        fetch(`/itinerary-packages/${paqueteSelect.value}/items`)
            .then((res) => res.json())
            .then((items) => {
                items.forEach((item) => {
                    createRow({
                        id: item.id,
                        nombre: item.nombre,
                        costo: item.costo,
                        costo_promo: item.costo_promo,
                        costo_nino: item.costo_nino,
                        costo_promo_nino: item.costo_promo_nino,
                        fecha: null,
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
            actualizarVistaPorDia();
            if (!tbody.querySelector('.itinerario-row')) emptyMsg.classList.remove('hidden');
            window.recomputeResumenFactura?.();
        }
    });
});
</script>
