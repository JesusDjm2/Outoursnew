@extends('layouts.app')
@section('title', $tour->nombre)
@section('content')

@php
    $resumen = $tour->calcularResumenFactura();
    $monedaSimbolo = $tour->moneda === 'PEN' ? 'S/ ' : '$ ';
    $formatMoney = fn ($valor) => $monedaSimbolo . number_format((float) $valor, 2);

    $estadoReservaBadge = fn (?string $estado) => match ($estado) {
        'confirmada' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400',
        'cancelada' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/30 dark:text-rose-400',
        'reservado_pasajero' => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/30 dark:text-indigo-400',
        default => 'bg-amber-50 text-amber-700 dark:bg-amber-950/30 dark:text-amber-400',
    };
    $estadoReservaIcono = fn (?string $estado) => match ($estado) {
        'confirmada' => 'fa-circle-check',
        'cancelada' => 'fa-circle-xmark',
        'reservado_pasajero' => 'fa-user-check',
        default => 'fa-clock',
    };
    $estadoReservaLabel = fn (?string $estado) => match ($estado) {
        'confirmada' => 'Confirmada',
        'cancelada' => 'Cancelada',
        'reservado_pasajero' => 'Reservado por el pasajero',
        default => 'Pendiente',
    };
@endphp

<div class="relative overflow-hidden rounded-3xl shadow-xl mb-8 hero-reveal">
    <div class="absolute inset-0">
        @if($tour->imagen_path)
            <img src="{{ asset('storage/' . $tour->imagen_path) }}" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full bg-gradient-to-br from-slate-100 via-slate-200 to-cyan-100 dark:from-slate-800 dark:via-slate-700 dark:to-cyan-900"></div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-white/95 via-white/75 to-white/40 dark:from-slate-950/95 dark:via-slate-950/60 dark:to-slate-950/20"></div>
    </div>

    <div class="relative px-5 py-5 sm:px-8 sm:py-6 text-gray-900 dark:text-white">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div class="min-w-0 lg:max-w-xl">
                <div class="flex items-center justify-between gap-3">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-black/5 backdrop-blur px-3 py-1.5 text-sm font-bold tracking-wide border border-black/10 dark:bg-white/10 dark:border-white/20">
                        <i class="fas fa-hashtag text-[11px] opacity-70"></i> {{ $tour->codigo }}
                    </span>
                    <div class="flex shrink-0 gap-1.5">
                        <a href="{{ route('tours.pdf', $tour) }}" target="_blank" title="Descargar PDF de cotización"
                           class="flex h-9 w-9 items-center justify-center rounded-full bg-cyan-500 text-white hover:bg-cyan-400 transition shadow-lg shadow-cyan-500/30">
                            <i class="fas fa-file-pdf text-sm"></i>
                        </a>
                        <a href="{{ route('tours.itinerario-pdf', $tour) }}" target="_blank" title="Descargar itinerario de viaje"
                           class="flex h-9 w-9 items-center justify-center rounded-full bg-purple-500 text-white hover:bg-purple-400 transition shadow-lg shadow-purple-500/30">
                            <i class="fas fa-book-open text-sm"></i>
                        </a>
                        <a href="{{ route('tours.edit', $tour) }}" title="Editar Tour"
                           class="flex h-9 w-9 items-center justify-center rounded-full bg-black/5 border border-black/10 backdrop-blur hover:bg-black/10 transition dark:bg-white/10 dark:border-white/30 dark:hover:bg-white/20">
                            <i class="fas fa-pen text-sm"></i>
                        </a>
                        <a href="{{ route('tours.habitaciones', $tour) }}" title="Asignar Habitaciones"
                           class="flex h-9 w-9 items-center justify-center rounded-full bg-black/5 border border-black/10 backdrop-blur hover:bg-black/10 transition dark:bg-white/10 dark:border-white/30 dark:hover:bg-white/20">
                            <i class="fas fa-bed text-sm"></i>
                        </a>
                    </div>
                </div>
                <p class="mt-2 text-gray-600 dark:text-slate-200 flex items-center gap-2 text-sm">
                    <i class="fas fa-calendar-days"></i>
                    @if($tour->fecha_inicio && $tour->fecha_fin)
                        {{ \Illuminate\Support\Carbon::parse($tour->fecha_inicio)->translatedFormat('d M Y') }}
                        &mdash;
                        {{ \Illuminate\Support\Carbon::parse($tour->fecha_fin)->translatedFormat('d M Y') }}
                    @else
                        Sin fechas de itinerario aún
                    @endif
                </p>

                @php
                    $paxDetalles = [
                        ['label' => 'Agente', 'value' => $tour->agente],
                        ['label' => 'Nombre Pax', 'value' => $tour->nombre_pax],
                        ['label' => 'Edad', 'value' => $tour->edad_pax],
                        ['label' => 'Contacto', 'value' => trim(($tour->codigo_pais ?? '') . ' ' . ($tour->contacto_pax ?? ''))],
                        ['label' => 'Canal', 'value' => $tour->canal],
                        ['label' => 'Fecha cotización', 'value' => $tour->fecha_cotizacion],
                        ['label' => 'Pax adultos', 'value' => $tour->pax_adultos],
                        ['label' => 'Pax niños', 'value' => $tour->pax_ninos],
                        ['label' => 'País', 'value' => $tour->pais],
                        ['label' => 'Depto/Estado', 'value' => $tour->departamento_estado],
                        ['label' => 'Fecha llegada', 'value' => $tour->fecha_llegada],
                        ['label' => 'Hora llegada', 'value' => $tour->hora_llegada],
                    ];
                @endphp
                <dl class="mt-4 grid grid-cols-2 sm:grid-cols-3 gap-x-5 gap-y-3 border-t border-black/10 dark:border-white/15 pt-4">
                    @foreach($paxDetalles as $item)
                    <div class="min-w-0">
                        <dt class="text-[10px] font-semibold uppercase tracking-wider text-gray-500 dark:text-slate-400">{{ $item['label'] }}</dt>
                        @if($item['value'] !== null && $item['value'] !== '')
                            <dd class="text-sm font-medium text-gray-800 dark:text-slate-100 truncate">{{ $item['value'] }}</dd>
                        @else
                            <dd class="text-sm italic text-amber-600 dark:text-amber-400/90 truncate">Sin datos</dd>
                        @endif
                    </div>
                    @endforeach
                </dl>
            </div>

            <div class="w-full lg:w-80 lg:shrink-0">
                <div class="rounded-2xl border border-black/10 bg-black/5 backdrop-blur-md p-4 shadow-xl shadow-black/10 dark:border-white/15 dark:bg-white/10 dark:shadow-black/20">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-500 dark:text-slate-200">
                            <i class="fas fa-file-invoice-dollar mr-1.5 text-emerald-600 dark:text-emerald-300"></i> Total del tour
                        </span>
                        @if($resumen['total_descuento'] > 0)
                            <span class="shrink-0 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-semibold px-2 py-0.5 border border-emerald-200 dark:bg-emerald-400/20 dark:text-emerald-200 dark:border-emerald-300/30">
                                -{{ $formatMoney($resumen['total_descuento']) }}
                            </span>
                        @endif
                    </div>
                    <p class="mt-1 text-2xl sm:text-[1.75rem] font-bold text-gray-900 dark:text-white leading-tight">{{ $formatMoney($resumen['pv_final']) }}</p>

                    <dl class="mt-3 space-y-1 text-xs border-t border-black/10 dark:border-white/15 pt-3">
                        <div class="flex items-center justify-between">
                            <dt class="text-gray-500 dark:text-slate-300">P.V. Regular</dt>
                            <dd class="font-medium text-gray-800 dark:text-slate-100">{{ $formatMoney($resumen['pv_regular']) }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-gray-500 dark:text-slate-300">P.V. Promo</dt>
                            <dd class="font-medium text-gray-800 dark:text-slate-100">{{ $formatMoney($resumen['pv_promo']) }}</dd>
                        </div>
                        @if(($tour->precio_adicional ?? 0) > 0)
                        <div class="flex items-center justify-between">
                            <dt class="text-gray-500 dark:text-slate-300">Precio adicional</dt>
                            <dd class="font-medium text-gray-800 dark:text-slate-100">{{ $formatMoney($tour->precio_adicional) }}</dd>
                        </div>
                        @endif
                        @if(($tour->descuento_especial ?? 0) > 0)
                        <div class="flex items-center justify-between">
                            <dt class="text-gray-500 dark:text-slate-300">Descuento especial</dt>
                            <dd class="font-medium text-gray-800 dark:text-slate-100">{{ $formatMoney($tour->descuento_especial) }}</dd>
                        </div>
                        @endif
                    </dl>

                    <div class="mt-3 flex items-center justify-between rounded-xl bg-black/5 px-3 py-2 border border-black/5 dark:bg-white/10 dark:border-white/10">
                        <span class="text-[11px] text-gray-600 dark:text-slate-200"><i class="fas fa-hand-holding-dollar mr-1.5"></i> Reserva ({{ (int) ($tour->reserva_pct ?? 0) }}%)</span>
                        <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-300">{{ $formatMoney($resumen['monto_reserva']) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

<div class="grid grid-cols-1 lg:grid-cols-10 gap-6 mb-10">
    <div class="lg:col-span-7 space-y-4">
        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2 dark:text-slate-100">
            <i class="fas fa-route text-purple-600"></i> Actividad
        </h2>

        @forelse($tour->itineraries as $index => $itinerary)
        <details class="group scroll-reveal bg-white rounded-2xl shadow overflow-hidden dark:bg-slate-900 dark:shadow-slate-950/50">
            <summary class="list-none cursor-pointer flex items-center gap-4 p-5 hover:bg-gray-50 dark:hover:bg-slate-800/60 [&::-webkit-details-marker]:hidden">
                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-purple-600 text-white flex items-center justify-center font-bold text-sm">
                    {{ $index + 1 }}
                </div>
                <h3 class="flex-1 min-w-0 text-base font-semibold text-gray-900 truncate dark:text-slate-100">{{ $itinerary->nombre }}</h3>
                <i class="fas fa-chevron-right text-gray-400 text-xs transition-transform duration-200 group-open:rotate-90 dark:text-slate-500 shrink-0"></i>
            </summary>
            <div class="border-t border-gray-100 px-5 pt-4 pb-5 dark:border-slate-800">
                @if($itinerary->descripcion)
                    <div class="rich-text text-gray-600 dark:text-slate-400 [&_a]:underline [&_a]:text-blue-600 dark:[&_a]:text-blue-400 [&_ul]:list-disc [&_ol]:list-decimal [&_ul]:pl-5 [&_ol]:pl-5 [&_p]:mb-1 last:[&_p]:mb-0">{!! $itinerary->descripcion !!}</div>
                @endif

                @if($itinerary->incluye || $itinerary->no_incluye)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                    @if($itinerary->incluye)
                    <div class="rounded-xl bg-emerald-50 border border-emerald-100 p-4 dark:bg-emerald-900/20 dark:border-emerald-900/40">
                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700 mb-1 dark:text-emerald-400"><i class="fas fa-check-circle mr-1"></i> Incluye</p>
                        <div class="rich-text text-sm text-emerald-800 dark:text-emerald-300 [&_ul]:list-disc [&_ol]:list-decimal [&_ul]:pl-5 [&_ol]:pl-5 [&_p]:mb-1 last:[&_p]:mb-0">{!! $itinerary->incluye !!}</div>
                    </div>
                    @endif
                    @if($itinerary->no_incluye)
                    <div class="rounded-xl bg-rose-50 border border-rose-100 p-4 dark:bg-rose-900/20 dark:border-rose-900/40">
                        <p class="text-xs font-semibold uppercase tracking-wide text-rose-700 mb-1 dark:text-rose-400"><i class="fas fa-times-circle mr-1"></i> No incluye</p>
                        <div class="rich-text text-sm text-rose-800 dark:text-rose-300 [&_ul]:list-disc [&_ol]:list-decimal [&_ul]:pl-5 [&_ol]:pl-5 [&_p]:mb-1 last:[&_p]:mb-0">{!! $itinerary->no_incluye !!}</div>
                    </div>
                    @endif
                </div>
                @endif

                @if(!$itinerary->descripcion && !$itinerary->incluye && !$itinerary->no_incluye)
                    <p class="text-sm text-gray-400 dark:text-slate-500">Sin más detalles registrados para esta actividad.</p>
                @endif
            </div>
        </details>
        @empty
        <div class="bg-white rounded-2xl shadow px-6 py-10 text-center text-gray-500 dark:bg-slate-900 dark:shadow-slate-950/50 dark:text-slate-400">
            Este tour aún no tiene itinerarios.
        </div>
        @endforelse
    </div>

    <div class="lg:col-span-3 space-y-4">
        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2 dark:text-slate-100">
            <i class="fas fa-hotel text-indigo-600"></i> Hoteles
        </h2>
        <div class="scroll-reveal bg-white rounded-2xl shadow p-5 dark:bg-slate-900 dark:shadow-slate-950/50">
            @if($tour->hospedajes->isEmpty())
                <p class="text-sm text-gray-400 dark:text-slate-500">Esta cotización no tiene hoteles asignados.</p>
            @else
                <div class="space-y-3">
                    @foreach($tour->hospedajes as $hospedaje)
                    <div class="rounded-xl border border-gray-200 p-3 dark:border-slate-800">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-lg overflow-hidden bg-gray-100 shrink-0 dark:bg-slate-800">
                                @if($hospedaje->hotel->imagen_path)
                                    <img src="{{ asset('storage/' . $hospedaje->hotel->imagen_path) }}" class="h-full w-full object-cover">
                                @else
                                    <div class="h-full w-full flex items-center justify-center text-gray-300 dark:text-slate-600"><i class="fas fa-hotel"></i></div>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-sm font-semibold text-gray-800 truncate dark:text-slate-100">{{ $hospedaje->hotel->nombre }}</p>
                                    <span class="shrink-0 inline-flex items-center gap-1 rounded-full text-[10px] font-medium px-2 py-0.5 {{ $estadoReservaBadge($hospedaje->estado_reserva) }}">
                                        <i class="fas {{ $estadoReservaIcono($hospedaje->estado_reserva) }}"></i> {{ $estadoReservaLabel($hospedaje->estado_reserva) }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-slate-400">
                                    <i class="fas fa-calendar-days w-4 text-gray-400 dark:text-slate-500"></i>
                                    {{ \Illuminate\Support\Carbon::parse($hospedaje->fecha_ingreso)->translatedFormat('d M') }}
                                    &mdash;
                                    {{ \Illuminate\Support\Carbon::parse($hospedaje->fecha_salida)->translatedFormat('d M Y') }}
                                </p>
                            </div>
                        </div>
                        @if($hospedaje->asignaciones->isNotEmpty())
                        <div class="flex flex-wrap gap-1.5 mt-3">
                            @foreach($hospedaje->asignaciones as $asignacion)
                            <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 text-indigo-700 text-[11px] px-2 py-1 dark:bg-indigo-900/40 dark:text-indigo-300">
                                <i class="fas fa-user"></i> {{ $asignacion->passenger->nombre }}
                                @if($asignacion->room)
                                    · <i class="fas fa-bed"></i> {{ $asignacion->room->nombre }} #{{ $asignacion->room->numero_habitacion }}
                                @endif
                            </span>
                            @endforeach
                        </div>
                        @else
                        <p class="text-xs text-gray-400 mt-3 dark:text-slate-500">Sin habitaciones asignadas todavía.</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2 pt-2 dark:text-slate-100">
            <i class="fas fa-truck-fast text-amber-600"></i> Proveedores
        </h2>
        <div class="scroll-reveal bg-white rounded-2xl shadow p-5 dark:bg-slate-900 dark:shadow-slate-950/50">
            @if($tour->proveedores->isEmpty())
                <p class="text-sm text-gray-400 dark:text-slate-500">Esta cotización no tiene proveedores asignados.</p>
            @else
                <div class="space-y-3">
                    @foreach($tour->proveedores as $proveedor)
                    <div class="rounded-xl border border-gray-200 p-3 dark:border-slate-800">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-lg overflow-hidden bg-gray-100 shrink-0 dark:bg-slate-800">
                                @if($proveedor->imagenes->isNotEmpty())
                                    <img src="{{ asset('storage/' . $proveedor->imagenes->first()->path) }}" class="h-full w-full object-cover">
                                @else
                                    <div class="h-full w-full flex items-center justify-center text-gray-300 dark:text-slate-600"><i class="fas fa-industry"></i></div>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-sm font-semibold text-gray-800 truncate dark:text-slate-100">{{ $proveedor->nombre }}</p>
                                    <span class="shrink-0 flex items-center gap-1.5">
                                        <span class="rounded-full bg-amber-50 text-amber-700 text-[10px] font-medium px-2 py-0.5 dark:bg-amber-900/40 dark:text-amber-300">
                                            {{ $proveedor->tipo?->nombre ?? 'Sin tipo' }}
                                        </span>
                                        <span class="inline-flex items-center gap-1 rounded-full text-[10px] font-medium px-2 py-0.5 {{ $estadoReservaBadge($proveedor->pivot->estado_reserva) }}">
                                            <i class="fas {{ $estadoReservaIcono($proveedor->pivot->estado_reserva) }}"></i> {{ $estadoReservaLabel($proveedor->pivot->estado_reserva) }}
                                        </span>
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 truncate dark:text-slate-400">
                                    {{ $proveedor->email }}{{ $proveedor->email && $proveedor->telefono ? ' · ' : '' }}{{ $proveedor->telefono }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

@if($tour->notas_adicionales)
<div class="space-y-4 mb-10">
    <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2 dark:text-slate-100">
        <i class="fas fa-note-sticky text-emerald-600"></i> Notas Adicionales
    </h2>
    <div class="scroll-reveal bg-white rounded-2xl shadow p-6 sm:p-8 dark:bg-slate-900 dark:shadow-slate-950/50">
        <div class="rich-text text-sm text-gray-700 dark:text-slate-300 [&_a]:underline [&_a]:text-blue-600 dark:[&_a]:text-blue-400 [&_ul]:list-disc [&_ol]:list-decimal [&_ul]:pl-5 [&_ol]:pl-5">{!! $tour->notas_adicionales !!}</div>
    </div>
</div>
@endif

@if($tour->passengers->isNotEmpty())
<div class="space-y-4 mt-10">
    <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2 dark:text-slate-100">
        <i class="fas fa-user-friends text-green-600"></i> Pasajeros
    </h2>
    <div class="scroll-reveal bg-white rounded-2xl shadow overflow-hidden dark:bg-slate-900 dark:shadow-slate-950/50">
        <table class="w-full">
            <thead class="bg-gray-50 dark:bg-slate-800">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Edad</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Correo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">PDF</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
                @foreach($tour->passengers as $passenger)
                <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/60">
                    <td class="px-6 py-3 text-sm text-gray-800 dark:text-slate-100">{{ $passenger->nombre }}</td>
                    <td class="px-6 py-3 text-sm text-gray-600 dark:text-slate-400">{{ $passenger->edad }}</td>
                    <td class="px-6 py-3 text-sm text-gray-600 dark:text-slate-400">{{ $passenger->correo }}</td>
                    <td class="px-6 py-3 text-sm">
                        @if($passenger->pdf_path)
                            <a href="{{ asset('storage/' . $passenger->pdf_path) }}" target="_blank" class="text-blue-600 hover:underline dark:text-blue-400"><i class="fas fa-file-pdf"></i> Ver</a>
                        @else
                            <span class="text-gray-400 dark:text-slate-500">-</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        gsap.registerPlugin(ScrollTrigger);

        gsap.from('.hero-reveal', { opacity: 0, y: -24, duration: 0.9, ease: 'power3.out' });

        gsap.utils.toArray('.scroll-reveal').forEach((el, i) => {
            gsap.from(el, {
                opacity: 0,
                y: 32,
                duration: 0.7,
                ease: 'power3.out',
                scrollTrigger: {
                    trigger: el,
                    start: 'top 88%',
                },
            });
        });
    });
</script>
@endpush
@endsection
