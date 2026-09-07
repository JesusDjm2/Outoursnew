@php
    $hospId = optional($hospedaje)->id;
    $selectedRoomIds = old("hospedajes.$i.rooms", optional($hospedaje)->rooms?->pluck('id')->all() ?? []);
    $selectedHotelId = old("hospedajes.$i.hotel_id", optional($hospedaje)->hotel_id);
    $selectedHotelNombre = $selectedHotelId
        ? optional(($hoteles ?? collect())->firstWhere('id', (int) $selectedHotelId))->nombre
        : null;
@endphp
<div class="hospedaje-block bg-cyan-50 border border-cyan-100 rounded-xl p-4 mb-3 dark:bg-cyan-900/20 dark:border-cyan-900/40">
    <div class="flex items-start justify-between mb-3">
        <h4 class="text-sm font-semibold text-cyan-800 dark:text-cyan-300"><i class="fas fa-bed mr-1"></i> Hospedaje</h4>
        <button type="button" class="remove-hospedaje-btn text-red-500 hover:text-red-700 text-xs dark:text-red-400 dark:hover:text-red-300">
            <i class="fas fa-times"></i> Quitar
        </button>
    </div>

    @if($hospId)
        <input type="hidden" name="hospedajes[{{ $i }}][id]" value="{{ $hospId }}">
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 mb-1">
        <div class="lg:col-span-5">
            <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-300">Hotel</label>
            <button type="button" class="hospedaje-hotel-trigger w-full flex items-center justify-between gap-1 px-3 py-1.5 text-sm border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-cyan-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
                <span class="hospedaje-hotel-label truncate {{ $selectedHotelNombre ? '' : 'text-gray-400 dark:text-slate-500' }}">{{ $selectedHotelNombre ?? 'Selecciona hotel...' }}</span>
                <i class="fas fa-chevron-down text-[10px] text-gray-400 shrink-0"></i>
            </button>
            <input type="hidden" name="hospedajes[{{ $i }}][hotel_id]" class="hospedaje-hotel-select" value="{{ $selectedHotelId }}">
        </div>
        <div class="lg:col-span-2">
            <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-300">Fecha de ingreso</label>
            <input type="date" name="hospedajes[{{ $i }}][fecha_ingreso]"
                   value="{{ old("hospedajes.$i.fecha_ingreso", optional($hospedaje)->fecha_ingreso) }}" required
                   class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
        </div>
        <div class="lg:col-span-2">
            <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-300">Fecha de salida</label>
            <input type="date" name="hospedajes[{{ $i }}][fecha_salida]"
                   value="{{ old("hospedajes.$i.fecha_salida", optional($hospedaje)->fecha_salida) }}" required
                   class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
        </div>
        <div class="lg:col-span-3">
            <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-300">Habitaciones (opcional)</label>
            <div class="hospedaje-tipos-habitacion border border-gray-300 rounded-lg divide-y divide-gray-100 max-h-32 overflow-y-auto dark:border-slate-700 dark:divide-slate-700 dark:bg-slate-800">
                <p class="text-xs text-gray-400 dark:text-slate-500 px-2 py-1.5">Elige un hotel primero</p>
            </div>
            <select name="hospedajes[{{ $i }}][rooms][]" multiple hidden
                    class="hospedaje-rooms-select"
                    data-selected="{{ implode(',', $selectedRoomIds) }}"></select>
        </div>
    </div>
    <p class="text-[11px] text-gray-400 dark:text-slate-500">Elige el tipo de habitación y cuántas necesitas; la habitación exacta se asigna después, desde "Asignar Habitaciones".</p>
</div>
