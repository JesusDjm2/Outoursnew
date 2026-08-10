@php
    $hospId = optional($hospedaje)->id;
    $selectedRoomIds = old("hospedajes.$i.rooms", optional($hospedaje)->rooms?->pluck('id')->all() ?? []);
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

    <div class="mb-3">
        <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-300">Hotel</label>
        <select name="hospedajes[{{ $i }}][hotel_id]" required
                class="hospedaje-hotel-select w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
            <option value="">Seleccionar hotel...</option>
            @foreach($hoteles ?? [] as $hotelOption)
            <option value="{{ $hotelOption->id }}"
                {{ (int) old("hospedajes.$i.hotel_id", optional($hospedaje)->hotel_id) === $hotelOption->id ? 'selected' : '' }}>
                {{ $hotelOption->nombre }}
            </option>
            @endforeach
        </select>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-300">Fecha de ingreso</label>
            <input type="date" name="hospedajes[{{ $i }}][fecha_ingreso]"
                   value="{{ old("hospedajes.$i.fecha_ingreso", optional($hospedaje)->fecha_ingreso) }}" required
                   class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-300">Fecha de salida</label>
            <input type="date" name="hospedajes[{{ $i }}][fecha_salida]"
                   value="{{ old("hospedajes.$i.fecha_salida", optional($hospedaje)->fecha_salida) }}" required
                   class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
        </div>
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-300">Habitaciones reservadas (opcional)</label>
        <select name="hospedajes[{{ $i }}][rooms][]" multiple
                class="hospedaje-rooms-select w-full text-sm dark:bg-slate-800 dark:text-slate-100"
                data-selected="{{ implode(',', $selectedRoomIds) }}"></select>
        <p class="text-[11px] text-gray-400 mt-1 dark:text-slate-500">Si no eliges ninguna, al asignar habitaciones por pasajero se podrá elegir entre todas las del hotel.</p>
    </div>
</div>
