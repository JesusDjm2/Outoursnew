@extends('layouts.app')
@section('title', $tour->nombre)
@section('content')

<div class="relative overflow-hidden rounded-3xl shadow-xl mb-8 hero-reveal">
    <div class="absolute inset-0">
        @if($tour->imagen_path)
            <img src="{{ asset('storage/' . $tour->imagen_path) }}" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full bg-gradient-to-br from-slate-800 via-slate-700 to-cyan-900"></div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/50 to-slate-950/10"></div>
    </div>

    <div class="relative px-6 py-14 sm:px-10 sm:py-20 text-white">
        <span class="inline-flex items-center gap-2 rounded-full bg-white/10 backdrop-blur px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.2em] border border-white/20">
            <i class="fas fa-hashtag"></i> {{ $tour->codigo }}
        </span>
        <h1 class="mt-4 text-3xl sm:text-5xl font-bold tracking-tight">{{ $tour->nombre }}</h1>
        <p class="mt-3 text-slate-200 flex items-center gap-2 text-sm sm:text-base">
            <i class="fas fa-calendar-days"></i>
            @if($tour->fecha_inicio && $tour->fecha_fin)
                {{ \Illuminate\Support\Carbon::parse($tour->fecha_inicio)->translatedFormat('d M Y') }}
                &mdash;
                {{ \Illuminate\Support\Carbon::parse($tour->fecha_fin)->translatedFormat('d M Y') }}
            @else
                Sin fechas de itinerario aún
            @endif
        </p>

        <div class="mt-8 flex flex-wrap gap-3">
            <a href="{{ route('tours.pdf', $tour) }}" target="_blank"
               class="inline-flex items-center gap-2 rounded-full bg-cyan-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-cyan-400 transition shadow-lg shadow-cyan-500/30">
                <i class="fas fa-file-pdf"></i> Descargar PDF
            </a>
            <a href="{{ route('tours.edit', $tour) }}"
               class="inline-flex items-center gap-2 rounded-full bg-white/10 border border-white/30 backdrop-blur px-5 py-2.5 text-sm font-semibold hover:bg-white/20 transition">
                <i class="fas fa-pen"></i> Editar Tour
            </a>
            <a href="{{ route('tours.habitaciones', $tour) }}"
               class="inline-flex items-center gap-2 rounded-full bg-white/10 border border-white/30 backdrop-blur px-5 py-2.5 text-sm font-semibold hover:bg-white/20 transition">
                <i class="fas fa-bed"></i> Asignar Habitaciones
            </a>
        </div>
    </div>
</div>

@if($tour->nombre_pax || $tour->agente || $tour->pax_adultos || $tour->pax_ninos)
<div class="space-y-4 mb-10">
    <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2 dark:text-slate-100">
        <i class="fas fa-user-tag text-blue-600"></i> Datos Pax
    </h2>
    <div class="scroll-reveal bg-white rounded-2xl shadow p-6 sm:p-8 dark:bg-slate-900 dark:shadow-slate-950/50">
        <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4 text-sm">
            @foreach([
                ['Agente', $tour->agente],
                ['Nombre Pax', $tour->nombre_pax],
                ['Edad', $tour->edad_pax],
                ['Contacto', $tour->contacto_pax],
                ['Canal', $tour->canal],
                ['Fecha de cotización', $tour->fecha_cotizacion],
                ['Pax adultos', $tour->pax_adultos],
                ['Pax niños', $tour->pax_ninos],
                ['País', $tour->pais],
                ['Código de país', $tour->codigo_pais],
                ['Departamento/Estado', $tour->departamento_estado],
                ['Fecha de llegada', $tour->fecha_llegada],
                ['Hora de llegada', $tour->hora_llegada],
            ] as [$label, $value])
                @if($value !== null && $value !== '')
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-slate-500">{{ $label }}</dt>
                    <dd class="text-gray-800 dark:text-slate-100">{{ $value }}</dd>
                </div>
                @endif
            @endforeach
        </dl>
    </div>
</div>
@endif

<div class="space-y-6 mb-10">
    <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2 dark:text-slate-100">
        <i class="fas fa-route text-purple-600"></i> Itinerario
    </h2>

    @forelse($tour->itineraries as $index => $itinerary)
    <div class="scroll-reveal bg-white rounded-2xl shadow p-6 sm:p-8 dark:bg-slate-900 dark:shadow-slate-950/50">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 w-11 h-11 rounded-full bg-purple-600 text-white flex items-center justify-center font-bold">
                {{ $index + 1 }}
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-100">{{ $itinerary->nombre }}</h3>
                @if($itinerary->descripcion)
                    <p class="text-gray-600 mt-1 dark:text-slate-400">{{ $itinerary->descripcion }}</p>
                @endif

                @if($itinerary->incluye || $itinerary->no_incluye)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                    @if($itinerary->incluye)
                    <div class="rounded-xl bg-emerald-50 border border-emerald-100 p-4 dark:bg-emerald-900/20 dark:border-emerald-900/40">
                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700 mb-1 dark:text-emerald-400"><i class="fas fa-check-circle mr-1"></i> Incluye</p>
                        <p class="text-sm text-emerald-800 whitespace-pre-line dark:text-emerald-300">{{ $itinerary->incluye }}</p>
                    </div>
                    @endif
                    @if($itinerary->no_incluye)
                    <div class="rounded-xl bg-rose-50 border border-rose-100 p-4 dark:bg-rose-900/20 dark:border-rose-900/40">
                        <p class="text-xs font-semibold uppercase tracking-wide text-rose-700 mb-1 dark:text-rose-400"><i class="fas fa-times-circle mr-1"></i> No incluye</p>
                        <p class="text-sm text-rose-800 whitespace-pre-line dark:text-rose-300">{{ $itinerary->no_incluye }}</p>
                    </div>
                    @endif
                </div>
                @endif

            </div>
        </div>
    </div>
    @empty
    <p class="text-gray-500 dark:text-slate-400">Este tour aún no tiene itinerarios.</p>
    @endforelse
</div>

@if($tour->hospedajes->isNotEmpty())
<div class="space-y-4 mb-10">
    <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2 dark:text-slate-100">
        <i class="fas fa-hotel text-indigo-600"></i> Hospedajes
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($tour->hospedajes as $hospedaje)
        <div class="scroll-reveal rounded-xl border border-gray-200 bg-white shadow overflow-hidden dark:border-slate-800 dark:bg-slate-900 dark:shadow-slate-950/50">
            <div class="h-28 bg-gray-100 dark:bg-slate-800">
                @if($hospedaje->hotel->imagen_path)
                    <img src="{{ asset('storage/' . $hospedaje->hotel->imagen_path) }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-300 dark:text-slate-600"><i class="fas fa-hotel text-2xl"></i></div>
                @endif
            </div>
            <div class="p-4">
                <p class="font-semibold text-gray-800 dark:text-slate-100">{{ $hospedaje->hotel->nombre }}</p>
                <p class="text-xs text-gray-500 mt-1 dark:text-slate-400">
                    <i class="fas fa-calendar-days w-4 text-gray-400 dark:text-slate-500"></i>
                    {{ \Illuminate\Support\Carbon::parse($hospedaje->fecha_ingreso)->translatedFormat('d M') }}
                    &mdash;
                    {{ \Illuminate\Support\Carbon::parse($hospedaje->fecha_salida)->translatedFormat('d M Y') }}
                </p>
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
        </div>
        @endforeach
    </div>
</div>
@endif

@if($tour->proveedores->isNotEmpty())
<div class="space-y-4">
    <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2 dark:text-slate-100">
        <i class="fas fa-truck-fast text-amber-600"></i> Proveedores
    </h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($tour->proveedores as $proveedor)
        <div class="scroll-reveal bg-white rounded-2xl shadow overflow-hidden dark:bg-slate-900 dark:shadow-slate-950/50">
            @if($proveedor->imagenes->isNotEmpty())
            <div class="grid grid-cols-3 gap-0.5 bg-gray-100 dark:bg-slate-800">
                @foreach($proveedor->imagenes->take(3) as $imagen)
                    <img src="{{ asset('storage/' . $imagen->path) }}" class="h-20 w-full object-cover">
                @endforeach
            </div>
            @endif
            <div class="p-4">
                <div class="flex items-center justify-between gap-2">
                    <p class="font-semibold text-gray-800 dark:text-slate-100">{{ $proveedor->nombre }}</p>
                    <span class="shrink-0 rounded-full bg-amber-50 text-amber-700 text-[11px] font-medium px-2.5 py-1 dark:bg-amber-900/40 dark:text-amber-300">
                        {{ $proveedor->tipo?->nombre ?? 'Sin tipo' }}
                    </span>
                </div>
                <p class="text-sm text-gray-500 mt-1 space-y-0.5 dark:text-slate-400">
                    @if($proveedor->email)<a href="mailto:{{ $proveedor->email }}" class="block hover:text-amber-600"><i class="fas fa-envelope w-4 text-gray-400 dark:text-slate-500"></i> {{ $proveedor->email }}</a>@endif
                    @if($proveedor->telefono)<a href="tel:{{ $proveedor->telefono }}" class="block hover:text-amber-600"><i class="fas fa-phone w-4 text-gray-400 dark:text-slate-500"></i> {{ $proveedor->telefono }}</a>@endif
                    @if($proveedor->direccion)<span class="block"><i class="fas fa-map-marker-alt w-4 text-gray-400 dark:text-slate-500"></i> {{ $proveedor->direccion }}</span>@endif
                </p>
            </div>
        </div>
        @endforeach
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
