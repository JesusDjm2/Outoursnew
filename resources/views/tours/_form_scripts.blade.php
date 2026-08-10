<script src="https://cdnjs.cloudflare.com/ajax/libs/tom-select/2.3.1/js/tom-select.complete.min.js"></script>
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

    function initHospedajeRoomPicker(block) {
        const hotelSelect = block.querySelector('.hospedaje-hotel-select');
        const roomsSelect = block.querySelector('.hospedaje-rooms-select');
        if (!hotelSelect || !roomsSelect || roomsSelect.tomselect) return;

        const selectedIds = (roomsSelect.dataset.selected || '').split(',').filter(Boolean);

        const ts = new TomSelect(roomsSelect, {
            plugins: ['remove_button'],
            valueField: 'id',
            labelField: 'label',
            searchField: 'label',
            options: [],
            placeholder: 'Elegir habitaciones (opcional)...',
            onItemAdd: () => window.recomputeResumenFactura?.(),
            onItemRemove: () => window.recomputeResumenFactura?.(),
        });

        function loadRoomsFor(hotelId) {
            const rooms = (window.roomsByHotel && window.roomsByHotel[hotelId]) || [];
            ts.clear(true);
            ts.clearOptions();
            rooms.forEach((room) => ts.addOption(room));
            ts.refreshOptions(false);
        }

        loadRoomsFor(hotelSelect.value);
        if (selectedIds.length) {
            ts.setValue(selectedIds, true);
            window.recomputeResumenFactura?.();
        }

        hotelSelect.addEventListener('change', () => {
            loadRoomsFor(hotelSelect.value);
            window.recomputeResumenFactura?.();
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
});
</script>
