@extends('layouts.app')
@section('title', 'Asignar Habitaciones - ' . $tour->nombre)
@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-slate-100">Asignar Habitaciones</h1>
    <p class="text-sm text-gray-500 mt-1 dark:text-slate-400">Tour: {{ $tour->nombre }} ({{ $tour->codigo }})</p>
    <a href="{{ route('tours.show', $tour) }}" class="text-blue-600 text-sm mt-1 inline-block dark:text-blue-400"><i class="fas fa-arrow-left"></i> Volver al tour</a>
</div>

@if($tour->hospedajes->isEmpty())
    <div class="bg-white rounded-xl shadow p-10 text-center text-gray-500 dark:bg-slate-900 dark:shadow-slate-950/50 dark:text-slate-400">
        Este tour aún no tiene hospedajes. Agrega uno desde <a href="{{ route('tours.edit', $tour) }}" class="text-indigo-600 underline dark:text-indigo-400">Editar Tour</a>.
    </div>
@elseif($tour->passengers->isEmpty())
    <div class="bg-white rounded-xl shadow p-10 text-center text-gray-500 dark:bg-slate-900 dark:shadow-slate-950/50 dark:text-slate-400">
        Este tour aún no tiene pasajeros. Agrega alguno desde <a href="{{ route('tours.edit', $tour) }}" class="text-indigo-600 underline dark:text-indigo-400">Editar Tour</a>.
    </div>
@else
<form method="POST" action="{{ route('tours.habitaciones.store', $tour) }}" class="space-y-6">
    @csrf
    @foreach($tour->hospedajes as $hospedaje)
    @php
        $asignacionesActuales = $hospedaje->asignaciones->keyBy('passenger_id');
    @endphp
    <div class="bg-white rounded-xl shadow p-6 md:p-8 dark:bg-slate-900 dark:shadow-slate-950/50">
        <h2 class="font-semibold text-gray-800 mb-1 dark:text-slate-100">
            <i class="fas fa-hotel text-indigo-600 mr-1"></i> {{ $hospedaje->hotel->nombre }}
        </h2>
        <p class="text-sm text-gray-500 mb-4 dark:text-slate-400">
            {{ \Illuminate\Support\Carbon::parse($hospedaje->fecha_ingreso)->translatedFormat('d M Y') }}
            &mdash;
            {{ \Illuminate\Support\Carbon::parse($hospedaje->fecha_salida)->translatedFormat('d M Y') }}
        </p>

        @if($hospedaje->hotel->rooms->isEmpty())
            <p class="text-sm text-gray-500 dark:text-slate-400">Este hotel no tiene habitaciones registradas. <a href="{{ route('hotels.edit', $hospedaje->hotel) }}" class="text-indigo-600 underline dark:text-indigo-400">Agregar habitaciones</a>.</p>
        @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-slate-800">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Pasajero</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Habitación</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
                    @foreach($tour->passengers as $passenger)
                    <tr>
                        <td class="px-4 py-2 text-sm text-gray-800 dark:text-slate-100">{{ $passenger->nombre }}</td>
                        <td class="px-4 py-2 text-sm">
                            <select name="asignaciones[{{ $hospedaje->id }}][{{ $passenger->id }}]"
                                    class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
                                <option value="">Sin asignar</option>
                                @foreach($hospedaje->hotel->rooms as $room)
                                <option value="{{ $room->id }}"
                                    {{ optional($asignacionesActuales->get($passenger->id))->room_id === $room->id ? 'selected' : '' }}>
                                    {{ $room->nombre }} · #{{ $room->numero_habitacion }} · {{ $room->cantidad_personas }} pax
                                </option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
    @endforeach

    <div class="flex gap-3">
        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">Guardar Asignaciones</button>
        <a href="{{ route('tours.show', $tour) }}" class="bg-gray-300 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-400 transition dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600">Cancelar</a>
    </div>
</form>
@endif
@endsection
