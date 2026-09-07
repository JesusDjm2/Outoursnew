<script>
document.addEventListener('DOMContentLoaded', () => {
    function fillTokens(html, tokens) {
        Object.entries(tokens).forEach(([token, value]) => {
            html = html.split(token).join(value);
        });
        return html;
    }

    function firstElementFromHtml(html) {
        const wrapper = document.createElement('div');
        wrapper.innerHTML = html.trim();
        return wrapper.firstElementChild;
    }

    function setupRepeatable({ containerId, templateId, addBtnId, blockClass, removeBtnClass, minMessage, onAdd }) {
        const container = document.getElementById(containerId);
        const template = document.getElementById(templateId);
        const addBtn = document.getElementById(addBtnId);
        if (!container || !template || !addBtn) return;

        let index = parseInt(container.dataset.nextIndex, 10) || 0;

        addBtn.addEventListener('click', () => {
            const html = fillTokens(template.innerHTML, { '__I__': index });
            const block = firstElementFromHtml(html);
            container.appendChild(block);
            if (onAdd) onAdd(block);
            window.recomputeResumenFactura?.();
            index++;
        });

        container.addEventListener('click', (e) => {
            const removeBtn = e.target.closest('.' + removeBtnClass);
            if (!removeBtn) return;

            const blocks = container.querySelectorAll('.' + blockClass);
            if (minMessage && blocks.length <= 1) {
                alert(minMessage);
                return;
            }
            removeBtn.closest('.' + blockClass).remove();
            window.recomputeResumenFactura?.();
        });
    }

    function escapeHtml(str) {
        return String(str).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    }

    const hotelCatalogo = window.hotelCatalogo || [];
    const destinoTree = window.tourPickerCategoryTree || [];

    function buildArbolHoteles() {
        const arbol = [];
        destinoTree.forEach((destino) => {
            const hotelesDestino = hotelCatalogo.filter((h) => String(h.destino_id) === String(destino.id));
            if (!hotelesDestino.length) return;
            arbol.push({ id: destino.id, nombre: destino.nombre, hoteles: hotelesDestino });
        });
        const sinDestino = hotelCatalogo.filter((h) => !h.destino_id);
        if (sinDestino.length) arbol.push({ id: '', nombre: 'Sin destino', hoteles: sinDestino });
        return arbol;
    }

    function renderArbolHotelesHtml(arbol) {
        return arbol.map((destino) => `
            <div class="arbol-destino">
                <button type="button" class="arbol-destino-toggle w-full flex items-center justify-between gap-2 px-2.5 py-1.5 text-left font-medium text-gray-700 dark:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-700">
                    <span class="truncate">${escapeHtml(destino.nombre)}</span>
                    <i class="fas fa-chevron-right text-[10px] text-gray-400 shrink-0 transition-transform"></i>
                </button>
                <div class="arbol-hoteles hidden pl-3 ml-2.5 border-l border-gray-100 dark:border-slate-700">
                    ${destino.hoteles.map((h) => `
                        <button type="button" class="arbol-hotel w-full text-left px-2 py-1 text-gray-700 dark:text-slate-200 hover:bg-cyan-50 hover:text-cyan-700 dark:hover:bg-cyan-950/30 dark:hover:text-cyan-200 truncate" data-id="${h.id}" data-nombre="${escapeHtml(h.nombre)}">${escapeHtml(h.nombre)}</button>
                    `).join('')}
                </div>
            </div>
        `).join('');
    }

    const hotelPanel = document.getElementById('hotel-picker-panel');
    const hotelBuscar = document.getElementById('hotel-picker-buscar');
    const hotelArbolEl = document.getElementById('hotel-picker-arbol');
    let hotelPickerTarget = null;

    if (hotelArbolEl) {
        hotelArbolEl.innerHTML = renderArbolHotelesHtml(buildArbolHoteles());
    }

    function resetArbolHotelesFiltro() {
        hotelArbolEl.querySelectorAll('.arbol-destino, .arbol-hotel').forEach((el) => el.classList.remove('hidden'));
        hotelArbolEl.querySelectorAll('.arbol-hoteles').forEach((el) => el.classList.add('hidden'));
        hotelArbolEl.querySelectorAll('.fa-chevron-right').forEach((i) => i.classList.remove('rotate-90'));
    }

    hotelArbolEl?.addEventListener('click', (e) => {
        const destinoToggle = e.target.closest('.arbol-destino-toggle');
        if (destinoToggle) {
            destinoToggle.nextElementSibling.classList.toggle('hidden');
            destinoToggle.querySelector('i').classList.toggle('rotate-90');
            return;
        }
        const hotelBtn = e.target.closest('.arbol-hotel');
        if (hotelBtn) {
            seleccionarHotelEnPanel(hotelBtn.dataset.id, hotelBtn.dataset.nombre);
        }
    });

    hotelBuscar?.addEventListener('input', () => {
        const q = hotelBuscar.value.trim().toLowerCase();
        if (!q) {
            resetArbolHotelesFiltro();
            return;
        }
        hotelArbolEl.querySelectorAll('.arbol-hotel').forEach((btn) => {
            btn.classList.toggle('hidden', !btn.dataset.nombre.toLowerCase().includes(q));
        });
        hotelArbolEl.querySelectorAll('.arbol-destino').forEach((destEl) => {
            const visibles = destEl.querySelectorAll('.arbol-hotel:not(.hidden)').length;
            destEl.classList.toggle('hidden', visibles === 0);
            destEl.querySelector('.arbol-hoteles').classList.toggle('hidden', visibles === 0);
        });
    });

    function abrirHotelPicker(trigger, hiddenInput, labelEl) {
        if (!hotelPanel) return;
        hotelPickerTarget = { hiddenInput, labelEl };

        const rect = trigger.getBoundingClientRect();
        const panelWidth = Math.max(rect.width, 288);
        const panelMaxHeight = 320;

        let left = Math.max(8, rect.left);
        if (left + panelWidth > window.innerWidth - 8) left = Math.max(8, window.innerWidth - panelWidth - 8);

        const spaceBelow = window.innerHeight - rect.bottom;
        hotelPanel.style.width = panelWidth + 'px';
        hotelPanel.style.left = left + 'px';
        if (spaceBelow < panelMaxHeight && rect.top > spaceBelow) {
            hotelPanel.style.top = 'auto';
            hotelPanel.style.bottom = (window.innerHeight - rect.top + 4) + 'px';
        } else {
            hotelPanel.style.bottom = 'auto';
            hotelPanel.style.top = (rect.bottom + 4) + 'px';
        }

        hotelPanel.classList.remove('hidden');
        hotelBuscar.value = '';
        resetArbolHotelesFiltro();
        hotelBuscar.focus();
    }

    function cerrarHotelPicker() {
        hotelPanel?.classList.add('hidden');
        hotelPickerTarget = null;
    }

    function seleccionarHotelEnPanel(id, nombre) {
        if (!hotelPickerTarget) return;
        const { hiddenInput, labelEl } = hotelPickerTarget;
        hiddenInput.value = id;
        labelEl.textContent = nombre;
        labelEl.classList.remove('text-gray-400', 'dark:text-slate-500');
        hiddenInput.dispatchEvent(new Event('change'));
        cerrarHotelPicker();
    }

    document.addEventListener('click', (e) => {
        if (!hotelPanel || hotelPanel.classList.contains('hidden')) return;
        if (hotelPanel.contains(e.target) || e.target.closest('.hospedaje-hotel-trigger')) return;
        cerrarHotelPicker();
    });
    document.addEventListener('scroll', (e) => {
        if (!hotelPanel || hotelPanel.classList.contains('hidden')) return;
        if (hotelPanel.contains(e.target)) return;
        cerrarHotelPicker();
    }, true);
    window.addEventListener('resize', cerrarHotelPicker);

    function initHospedajeHotelTrigger(block) {
        const trigger = block.querySelector('.hospedaje-hotel-trigger');
        const hiddenInput = block.querySelector('.hospedaje-hotel-select');
        const labelEl = block.querySelector('.hospedaje-hotel-label');
        if (!trigger || !hiddenInput || !labelEl || trigger.dataset.pickerReady) return;
        trigger.dataset.pickerReady = '1';
        trigger.addEventListener('click', () => abrirHotelPicker(trigger, hiddenInput, labelEl));
    }

    function sincronizarRoomsSelect(block) {
        const roomsSelect = block.querySelector('.hospedaje-rooms-select');
        if (!roomsSelect) return;
        roomsSelect.innerHTML = '';
        block.querySelectorAll('.tipo-habitacion-row').forEach((row) => {
            const cantidad = parseInt(row.querySelector('.tipo-cantidad').textContent, 10) || 0;
            const roomIds = JSON.parse(row.dataset.roomIds || '[]');
            roomIds.slice(0, cantidad).forEach((id) => {
                const opt = document.createElement('option');
                opt.value = id;
                opt.selected = true;
                roomsSelect.appendChild(opt);
            });
        });
        window.recomputeResumenFactura?.();
    }

    function loadTiposHabitacion(block, hotelId) {
        const cont = block.querySelector('.hospedaje-tipos-habitacion');
        const roomsSelect = block.querySelector('.hospedaje-rooms-select');
        if (!cont || !roomsSelect) return;

        const selectedIds = (roomsSelect.dataset.selected || '').split(',').filter(Boolean);
        const rooms = (window.roomsByHotel && window.roomsByHotel[hotelId]) || [];

        cont.innerHTML = '';
        if (!rooms.length) {
            cont.innerHTML = '<p class="text-xs text-gray-400 dark:text-slate-500 px-2 py-1.5">Este hotel no tiene habitaciones registradas.</p>';
            sincronizarRoomsSelect(block);
            return;
        }

        const porTipo = {};
        rooms.forEach((r) => {
            (porTipo[r.nombre] = porTipo[r.nombre] || []).push(r);
        });

        Object.entries(porTipo).forEach(([nombre, instancias]) => {
            const cantidadInicial = instancias.filter((r) => selectedIds.includes(String(r.id))).length;
            const precio = instancias[0].precio_promo ?? instancias[0].precio_regular;
            const row = document.createElement('div');
            row.className = 'tipo-habitacion-row flex items-center justify-between gap-2 text-xs px-2 py-1.5';
            row.dataset.roomIds = JSON.stringify(instancias.map((r) => r.id));
            row.dataset.max = instancias.length;
            row.innerHTML = `
                <span class="flex-1 truncate text-gray-700 dark:text-slate-200">${nombre} <span class="text-gray-400 dark:text-slate-500">(${instancias.length} disp. · S/ ${Number(precio || 0).toFixed(2)})</span></span>
                <div class="flex items-center gap-1 shrink-0">
                    <button type="button" class="tipo-decr w-5 h-5 rounded bg-gray-100 hover:bg-gray-200 dark:bg-slate-700 dark:hover:bg-slate-600 dark:text-slate-200">−</button>
                    <span class="tipo-cantidad w-5 text-center font-medium text-gray-700 dark:text-slate-200">${cantidadInicial}</span>
                    <button type="button" class="tipo-incr w-5 h-5 rounded bg-gray-100 hover:bg-gray-200 dark:bg-slate-700 dark:hover:bg-slate-600 dark:text-slate-200">+</button>
                </div>
            `;
            cont.appendChild(row);
        });

        sincronizarRoomsSelect(block);
    }

    document.getElementById('hospedajes-container')?.addEventListener('click', (e) => {
        const incr = e.target.closest('.tipo-incr');
        const decr = e.target.closest('.tipo-decr');
        if (!incr && !decr) return;
        const row = e.target.closest('.tipo-habitacion-row');
        const span = row.querySelector('.tipo-cantidad');
        let cantidad = parseInt(span.textContent, 10) || 0;
        const max = parseInt(row.dataset.max, 10) || 0;
        if (incr && cantidad < max) cantidad++;
        if (decr && cantidad > 0) cantidad--;
        span.textContent = cantidad;
        sincronizarRoomsSelect(row.closest('.hospedaje-block'));
    });

    function initHospedajeRoomPicker(block) {
        const hotelSelect = block.querySelector('.hospedaje-hotel-select');
        const roomsSelect = block.querySelector('.hospedaje-rooms-select');
        initHospedajeHotelTrigger(block);
        if (!hotelSelect || !roomsSelect || roomsSelect.dataset.roomPickerReady) return;
        roomsSelect.dataset.roomPickerReady = '1';

        loadTiposHabitacion(block, hotelSelect.value);

        hotelSelect.addEventListener('change', () => {
            loadTiposHabitacion(block, hotelSelect.value);
        });

        const fechaIngresoInput = block.querySelector('[name*="[fecha_ingreso]"]');
        const fechaSalidaInput = block.querySelector('[name*="[fecha_salida]"]');
        fechaIngresoInput?.addEventListener('input', () => window.recomputeResumenFactura?.());
        fechaSalidaInput?.addEventListener('input', () => window.recomputeResumenFactura?.());
    }

    setupRepeatable({
        containerId: 'pasajeros-container',
        templateId: 'passenger-template',
        addBtnId: 'add-passenger-btn',
        blockClass: 'passenger-block',
        removeBtnClass: 'remove-passenger-btn',
        minMessage: null,
    });

    setupRepeatable({
        containerId: 'hospedajes-container',
        templateId: 'hospedaje-template',
        addBtnId: 'add-hospedaje-btn',
        blockClass: 'hospedaje-block',
        removeBtnClass: 'remove-hospedaje-btn',
        minMessage: null,
        onAdd: initHospedajeRoomPicker,
    });

    document.querySelectorAll('#hospedajes-container .hospedaje-block').forEach(initHospedajeRoomPicker);

    document.querySelectorAll('.proveedor-checkbox').forEach((checkbox) => {
        checkbox.addEventListener('change', () => {
            const wrap = checkbox.closest('label')?.querySelector('.proveedor-fechas-wrap');
            wrap?.classList.toggle('hidden', !checkbox.checked);
        });
    });

    function agregarFechaProveedor(wrap, fecha) {
        const proveedorId = wrap.dataset.proveedorId;
        const chipsCont = wrap.querySelector('.proveedor-fechas-chips');
        const yaExiste = Array.from(chipsCont.querySelectorAll('input[type="hidden"]')).some((inp) => inp.value === fecha);
        if (yaExiste) return;

        const chip = document.createElement('span');
        chip.className = 'proveedor-fecha-chip inline-flex items-center gap-1 rounded-full px-1.5 py-0.5 text-[10px] bg-amber-50 text-amber-700 dark:bg-amber-950/30 dark:text-amber-300';
        chip.innerHTML = `${fecha} <button type="button" class="proveedor-fecha-remove hover:text-red-600" title="Quitar fecha">&times;</button><input type="hidden" name="proveedores_fechas[${proveedorId}][]" value="${fecha}">`;
        chipsCont.appendChild(chip);
    }
    window.agregarFechaProveedor = agregarFechaProveedor;

    document.querySelectorAll('.proveedor-fecha-add').forEach((input) => {
        input.addEventListener('change', () => {
            if (!input.value) return;
            agregarFechaProveedor(input.closest('.proveedor-fechas-wrap'), input.value);
            input.value = '';
        });
    });

    document.querySelectorAll('.proveedor-fechas-chips').forEach((cont) => {
        cont.addEventListener('click', (e) => {
            const removeBtn = e.target.closest('.proveedor-fecha-remove');
            if (removeBtn) removeBtn.closest('.proveedor-fecha-chip').remove();
        });
    });
});
</script>
