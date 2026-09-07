@extends('layouts.app')
@section('title', 'Hoteles y Habitaciones')
@section('content')

<div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-4">
    <form method="GET" action="{{ route('hotels.index') }}" class="flex flex-1 gap-2 max-w-lg">
        <div class="relative flex-1">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-slate-500"></i>
            <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por nombre, código o destino..."
                   class="w-full pl-10 {{ $q ? 'pr-9' : 'pr-3' }} py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
            @if($q)
            <a href="{{ route('hotels.index') }}" title="Limpiar búsqueda"
               class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:text-slate-500 dark:hover:text-slate-300">
                <i class="fas fa-xmark"></i>
            </a>
            @endif
        </div>
        <button type="submit" class="shrink-0 bg-indigo-600 text-white px-4 py-1.5 rounded-lg hover:bg-indigo-700 transition text-sm font-medium">
            <i class="fas fa-search mr-1"></i> Buscar
        </button>
    </form>
    <button type="button" id="btn-nuevo-hotel" class="shrink-0 sm:ml-auto bg-indigo-600 text-white px-4 py-1.5 rounded-lg hover:bg-indigo-700 transition text-sm font-medium">
        <i class="fas fa-plus mr-1"></i> Nuevo Hotel
    </button>
</div>

<div class="space-y-3">
    @forelse($hotels as $hotel)
    <details class="group bg-white rounded-xl shadow overflow-hidden dark:bg-slate-900 dark:shadow-slate-950/50">
        <summary class="list-none cursor-pointer flex items-center justify-between gap-3 px-5 py-3.5 hover:bg-gray-50 dark:hover:bg-slate-800/60 [&::-webkit-details-marker]:hidden">
            <div class="flex items-center gap-3 min-w-0">
                <i class="fas fa-chevron-right text-gray-400 text-xs transition-transform duration-200 group-open:rotate-90 dark:text-slate-500 shrink-0"></i>
                <div class="h-9 w-9 rounded-lg overflow-hidden bg-gray-100 shrink-0 flex items-center justify-center dark:bg-slate-800">
                    @if($hotel->imagen_path)
                        <img src="{{ asset('storage/' . $hotel->imagen_path) }}" class="h-full w-full object-cover">
                    @else
                        <i class="fas fa-hotel text-gray-300 text-sm dark:text-slate-600"></i>
                    @endif
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        @if($hotel->codigo)
                            <span class="shrink-0 px-1.5 py-0.5 rounded bg-indigo-100 text-indigo-700 text-[10px] font-semibold dark:bg-indigo-950/40 dark:text-indigo-300">{{ $hotel->codigo }}</span>
                        @endif
                        <span class="font-semibold text-gray-800 dark:text-slate-100 truncate">{{ $hotel->nombre }}</span>
                    </div>
                    <p class="text-xs text-gray-400 truncate dark:text-slate-500">{{ $hotel->destino->nombre ?? 'Sin destino' }}</p>
                </div>
                <span class="shrink-0 rounded-full bg-teal-50 text-teal-700 text-xs font-medium px-2.5 py-1 dark:bg-teal-900/30 dark:text-teal-300">
                    {{ $hotel->rooms_count }} {{ $hotel->rooms_count == 1 ? 'hab.' : 'habs.' }}
                </span>
            </div>
            <div class="flex items-center gap-3 shrink-0" onclick="event.stopPropagation();">
                <button type="button" class="js-edit-hotel text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                    data-id="{{ $hotel->id }}" data-destino-id="{{ $hotel->destino_id }}" data-codigo="{{ $hotel->codigo }}"
                    data-nombre="{{ $hotel->nombre }}" data-direccion="{{ $hotel->direccion }}" data-telefono="{{ $hotel->telefono }}"
                    data-email="{{ $hotel->email }}" data-imagen="{{ $hotel->imagen_path ? asset('storage/' . $hotel->imagen_path) : '' }}"
                    title="Editar hotel">
                    <i class="fas fa-edit"></i>
                </button>
                <form action="{{ route('hotels.destroy', $hotel) }}" method="POST" onsubmit="return swalConfirmSubmit(event, '¿Eliminar este hotel?')">
                    @csrf @method('DELETE')
                    <button class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300" title="Eliminar hotel"><i class="fas fa-trash"></i></button>
                </form>
            </div>
        </summary>
        <div class="border-t border-gray-100 px-5 py-4 dark:border-slate-800">
            @if($hotel->direccion || $hotel->telefono || $hotel->email)
            <div class="flex flex-wrap gap-x-5 gap-y-1 text-xs text-gray-500 mb-4 dark:text-slate-400">
                @if($hotel->direccion)<span><i class="fas fa-map-marker-alt w-4 text-gray-400 dark:text-slate-500"></i> {{ $hotel->direccion }}</span>@endif
                @if($hotel->telefono)<span><i class="fas fa-phone w-4 text-gray-400 dark:text-slate-500"></i> {{ $hotel->telefono }}</span>@endif
                @if($hotel->email)<span><i class="fas fa-envelope w-4 text-gray-400 dark:text-slate-500"></i> {{ $hotel->email }}</span>@endif
            </div>
            @endif

            @if($hotel->rooms->isEmpty())
                <p class="text-sm text-gray-400 mb-3 dark:text-slate-500">Sin habitaciones todavía.</p>
            @else
                <ul class="divide-y divide-gray-100 mb-3 dark:divide-slate-800">
                    @foreach($hotel->rooms as $room)
                    <li class="flex items-center justify-between py-2.5 px-2 -mx-2 gap-3 rounded-lg transition hover:bg-gray-50 dark:hover:bg-slate-800/60">
                        <div class="min-w-0 flex flex-wrap items-center gap-x-3 gap-y-1">
                            <span class="text-sm font-medium text-gray-800 truncate dark:text-slate-100">{{ $room->nombre }}</span>
                            <span class="shrink-0 inline-flex items-center gap-1 text-xs text-gray-500 dark:text-slate-400" title="Número de habitación">
                                <i class="fas fa-door-open text-[10px] text-gray-400 dark:text-slate-500"></i> Hab. {{ $room->numero_habitacion }}
                            </span>
                            <span class="shrink-0 inline-flex items-center gap-1 text-xs text-gray-500 dark:text-slate-400" title="Capacidad de pasajeros">
                                <i class="fas fa-user-group text-[10px] text-gray-400 dark:text-slate-500"></i> {{ $room->cantidad_personas }} pax máx.
                            </span>
                        </div>
                        <div class="flex items-center gap-4 shrink-0">
                            <div class="text-right text-xs" title="Precio por noche">
                                <span class="text-sm font-medium text-gray-700 dark:text-slate-200">{{ number_format($room->precio_regular ?? 0, 2) }}</span>
                                <span class="text-gray-400 dark:text-slate-500">/ noche</span>
                                @if($room->precio_promo)
                                    <span class="block text-emerald-600 dark:text-emerald-400">Promo: {{ number_format($room->precio_promo, 2) }} / noche</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-3">
                                <button type="button" class="js-edit-room text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                    data-id="{{ $room->id }}" data-hotel-id="{{ $hotel->id }}" data-nombre="{{ $room->nombre }}"
                                    data-numero="{{ $room->numero_habitacion }}" data-capacidad="{{ $room->cantidad_personas }}"
                                    data-precio-regular="{{ $room->precio_regular }}" data-precio-promo="{{ $room->precio_promo }}"
                                    title="Editar habitación">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('rooms.destroy', [$hotel, $room]) }}" method="POST" onsubmit="return swalConfirmSubmit(event, '¿Eliminar esta habitación?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300" title="Eliminar habitación"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            @endif
            <button type="button" class="js-add-room inline-flex items-center gap-1.5 text-sm font-medium text-teal-600 hover:text-teal-800 dark:text-teal-400 dark:hover:text-teal-300"
                data-hotel-id="{{ $hotel->id }}">
                <i class="fas fa-plus"></i> Agregar habitación
            </button>
        </div>
    </details>
    @empty
    <div class="bg-white rounded-xl shadow px-6 py-10 text-center text-gray-500 dark:bg-slate-900 dark:shadow-slate-950/50 dark:text-slate-400">
        {{ $q ? 'No se encontraron hoteles para "' . $q . '".' : 'Aún no hay hoteles registrados.' }}
    </div>
    @endforelse
</div>

<div class="mt-4">
    {{ $hotels->links() }}
</div>

{{-- Modal: Hotel (crear/editar) --}}
<div id="hotel-modal" class="fixed inset-0 z-50 hidden">
    <div id="hotel-modal-backdrop" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
    <div class="relative flex min-h-full items-center justify-center p-4 pointer-events-none">
        <div role="dialog" aria-modal="true" aria-labelledby="hotel-modal-title"
             class="pointer-events-auto relative w-full max-w-lg max-h-[90vh] overflow-y-auto bg-white rounded-2xl shadow-2xl dark:bg-slate-900">
            <div class="flex items-start justify-between gap-4 p-5 border-b border-gray-100 dark:border-slate-800">
                <h2 id="hotel-modal-title" class="text-lg font-bold text-gray-800 dark:text-slate-100">Nuevo Hotel</h2>
                <button type="button" id="hotel-modal-close" aria-label="Cerrar" class="text-gray-400 hover:text-gray-600 dark:text-slate-500 dark:hover:text-slate-300">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>
            <form id="hotel-form" method="POST" action="{{ route('hotels.store') }}" enctype="multipart/form-data" class="p-5 space-y-3">
                @csrf
                <input type="hidden" name="_method" id="hotel-method" value="PUT" disabled>
                <input type="hidden" name="_editing_id" id="hotel-editing-id" value="">

                @if($destinos->isEmpty())
                    <p class="text-sm text-amber-600 dark:text-amber-400">
                        Aún no hay destinos registrados. <a href="{{ route('destinos.create') }}" target="_blank" class="underline">Crea uno primero</a> y luego recarga esta página.
                    </p>
                @else
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Destino</label>
                    <select name="destino_id" id="hotel-destino" required
                        class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
                        <option value="">Selecciona...</option>
                        @foreach($destinos as $destino)
                            <option value="{{ $destino->id }}">{{ $destino->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Código</label>
                        <input type="text" name="codigo" id="hotel-codigo" maxlength="20" placeholder="TE, TEA..."
                            class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none uppercase dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Nombre</label>
                        <input type="text" name="nombre" id="hotel-nombre" required
                            class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Dirección</label>
                    <input type="text" name="direccion" id="hotel-direccion"
                        class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Teléfono</label>
                        <input type="text" name="telefono" id="hotel-telefono"
                            class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Email</label>
                        <input type="email" name="email" id="hotel-email"
                            class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Imagen</label>
                    <div class="flex items-center gap-3">
                        <img id="hotel-imagen-preview" src="" class="h-12 w-12 rounded-lg object-cover hidden shrink-0">
                        <input type="file" name="imagen" id="hotel-imagen" accept="image/*"
                            class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="bg-indigo-600 text-white px-5 py-2 text-sm font-medium rounded-lg hover:bg-indigo-700 transition">Guardar</button>
                    <button type="button" id="hotel-modal-cancel" class="bg-gray-300 text-gray-800 px-5 py-2 text-sm font-medium rounded-lg hover:bg-gray-400 transition dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Habitación (crear/editar) --}}
<div id="room-modal" class="fixed inset-0 z-50 hidden">
    <div id="room-modal-backdrop" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
    <div class="relative flex min-h-full items-center justify-center p-4 pointer-events-none">
        <div role="dialog" aria-modal="true" aria-labelledby="room-modal-title"
             class="pointer-events-auto relative w-full max-w-md bg-white rounded-2xl shadow-2xl dark:bg-slate-900">
            <div class="flex items-start justify-between gap-4 p-5 border-b border-gray-100 dark:border-slate-800">
                <h2 id="room-modal-title" class="text-lg font-bold text-gray-800 dark:text-slate-100">Nueva Habitación</h2>
                <button type="button" id="room-modal-close" aria-label="Cerrar" class="text-gray-400 hover:text-gray-600 dark:text-slate-500 dark:hover:text-slate-300">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>
            <form id="room-form" method="POST" action="" class="p-5 space-y-3">
                @csrf
                <input type="hidden" name="_method" id="room-method" value="PUT" disabled>
                <input type="hidden" name="_editing_id" id="room-editing-id" value="">
                <input type="hidden" name="hotel_id" id="room-hotel-id" value="">

                <div class="grid grid-cols-3 gap-3">
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Nombre</label>
                        <input type="text" name="nombre" id="room-nombre" required
                            class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">N° Hab.</label>
                        <input type="text" name="numero_habitacion" id="room-numero" required
                            class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Capacidad</label>
                        <input type="number" name="cantidad_personas" id="room-capacidad" min="1" required
                            class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Precio regular</label>
                        <input type="number" step="0.01" min="0" name="precio_regular" id="room-precio-regular" required
                            class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Precio promo</label>
                        <input type="number" step="0.01" min="0" name="precio_promo" id="room-precio-promo"
                            class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="bg-teal-600 text-white px-5 py-2 text-sm font-medium rounded-lg hover:bg-teal-700 transition">Guardar</button>
                    <button type="button" id="room-modal-cancel" class="bg-gray-300 text-gray-800 px-5 py-2 text-sm font-medium rounded-lg hover:bg-gray-400 transition dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const hotelUpdateUrlTemplate = "{{ route('hotels.update', ['hotel' => '__ID__']) }}";
    const roomStoreUrlTemplate = "{{ route('rooms.store', ['hotel' => '__HOTEL__']) }}";
    const roomUpdateUrlTemplate = "{{ route('rooms.update', ['hotel' => '__HOTEL__', 'room' => '__ROOM__']) }}";

    // --- Modal Hotel ---
    const hotelModal = document.getElementById('hotel-modal');
    const hotelForm = document.getElementById('hotel-form');
    const hotelStoreUrl = hotelForm.action;
    const hotelTitle = document.getElementById('hotel-modal-title');
    const hotelDestino = document.getElementById('hotel-destino');
    const hotelCodigo = document.getElementById('hotel-codigo');
    const hotelNombre = document.getElementById('hotel-nombre');
    const hotelDireccion = document.getElementById('hotel-direccion');
    const hotelTelefono = document.getElementById('hotel-telefono');
    const hotelEmail = document.getElementById('hotel-email');
    const hotelImagen = document.getElementById('hotel-imagen');
    const hotelImagenPreview = document.getElementById('hotel-imagen-preview');
    const hotelMethod = document.getElementById('hotel-method');
    const hotelEditingId = document.getElementById('hotel-editing-id');

    function openHotelModal() {
        hotelModal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
    function closeHotelModal() {
        hotelModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
    function hotelCreateMode() {
        hotelTitle.textContent = 'Nuevo Hotel';
        hotelForm.action = hotelStoreUrl;
        hotelMethod.disabled = true;
        hotelEditingId.value = '';
        hotelCodigo.value = '';
        hotelNombre.value = '';
        hotelDireccion.value = '';
        hotelTelefono.value = '';
        hotelEmail.value = '';
        if (hotelDestino) hotelDestino.value = '';
        hotelImagen.value = '';
        hotelImagenPreview.src = '';
        hotelImagenPreview.classList.add('hidden');
    }
    function hotelEditMode(data) {
        hotelTitle.textContent = 'Editar Hotel';
        hotelForm.action = hotelUpdateUrlTemplate.replace('__ID__', data.id);
        hotelMethod.disabled = false;
        hotelEditingId.value = data.id;
        hotelCodigo.value = data.codigo || '';
        hotelNombre.value = data.nombre || '';
        hotelDireccion.value = data.direccion || '';
        hotelTelefono.value = data.telefono || '';
        hotelEmail.value = data.email || '';
        if (hotelDestino) hotelDestino.value = data.destinoId || '';
        hotelImagen.value = '';
        if (data.imagen) {
            hotelImagenPreview.src = data.imagen;
            hotelImagenPreview.classList.remove('hidden');
        } else {
            hotelImagenPreview.src = '';
            hotelImagenPreview.classList.add('hidden');
        }
    }

    document.getElementById('btn-nuevo-hotel')?.addEventListener('click', () => {
        hotelCreateMode();
        openHotelModal();
    });
    document.querySelectorAll('.js-edit-hotel').forEach(btn => {
        btn.addEventListener('click', () => {
            hotelEditMode({
                id: btn.dataset.id,
                destinoId: btn.dataset.destinoId,
                codigo: btn.dataset.codigo,
                nombre: btn.dataset.nombre,
                direccion: btn.dataset.direccion,
                telefono: btn.dataset.telefono,
                email: btn.dataset.email,
                imagen: btn.dataset.imagen,
            });
            openHotelModal();
        });
    });
    document.getElementById('hotel-modal-close')?.addEventListener('click', closeHotelModal);
    document.getElementById('hotel-modal-cancel')?.addEventListener('click', closeHotelModal);
    document.getElementById('hotel-modal-backdrop')?.addEventListener('click', closeHotelModal);

    // --- Modal Room ---
    const roomModal = document.getElementById('room-modal');
    const roomForm = document.getElementById('room-form');
    const roomTitle = document.getElementById('room-modal-title');
    const roomNombre = document.getElementById('room-nombre');
    const roomNumero = document.getElementById('room-numero');
    const roomCapacidad = document.getElementById('room-capacidad');
    const roomPrecioRegular = document.getElementById('room-precio-regular');
    const roomPrecioPromo = document.getElementById('room-precio-promo');
    const roomMethod = document.getElementById('room-method');
    const roomEditingId = document.getElementById('room-editing-id');
    const roomHotelId = document.getElementById('room-hotel-id');

    function openRoomModal() {
        roomModal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
    function closeRoomModal() {
        roomModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
    function roomCreateMode(hotelId) {
        roomTitle.textContent = 'Nueva Habitación';
        roomForm.action = roomStoreUrlTemplate.replace('__HOTEL__', hotelId);
        roomMethod.disabled = true;
        roomEditingId.value = '';
        roomHotelId.value = hotelId;
        roomNombre.value = '';
        roomNumero.value = '';
        roomCapacidad.value = 1;
        roomPrecioRegular.value = '';
        roomPrecioPromo.value = '';
    }
    function roomEditMode(data) {
        roomTitle.textContent = 'Editar Habitación';
        roomForm.action = roomUpdateUrlTemplate.replace('__HOTEL__', data.hotelId).replace('__ROOM__', data.id);
        roomMethod.disabled = false;
        roomEditingId.value = data.id;
        roomHotelId.value = data.hotelId;
        roomNombre.value = data.nombre || '';
        roomNumero.value = data.numero || '';
        roomCapacidad.value = data.capacidad || 1;
        roomPrecioRegular.value = data.precioRegular || '';
        roomPrecioPromo.value = data.precioPromo || '';
    }

    document.querySelectorAll('.js-add-room').forEach(btn => {
        btn.addEventListener('click', () => {
            roomCreateMode(btn.dataset.hotelId);
            openRoomModal();
        });
    });
    document.querySelectorAll('.js-edit-room').forEach(btn => {
        btn.addEventListener('click', () => {
            roomEditMode({
                id: btn.dataset.id,
                hotelId: btn.dataset.hotelId,
                nombre: btn.dataset.nombre,
                numero: btn.dataset.numero,
                capacidad: btn.dataset.capacidad,
                precioRegular: btn.dataset.precioRegular,
                precioPromo: btn.dataset.precioPromo,
            });
            openRoomModal();
        });
    });
    document.getElementById('room-modal-close')?.addEventListener('click', closeRoomModal);
    document.getElementById('room-modal-cancel')?.addEventListener('click', closeRoomModal);
    document.getElementById('room-modal-backdrop')?.addEventListener('click', closeRoomModal);

    document.addEventListener('keydown', (e) => {
        if (e.key !== 'Escape') return;
        if (!hotelModal.classList.contains('hidden')) closeHotelModal();
        if (!roomModal.classList.contains('hidden')) closeRoomModal();
    });

    // Si la validación falló, reabrimos el modal correspondiente con lo que el usuario había escrito.
    @if ($errors->any())
        @if (old('numero_habitacion') !== null || old('cantidad_personas') !== null)
            @if (old('_editing_id'))
                roomEditMode({
                    id: '{{ old('_editing_id') }}',
                    hotelId: '{{ old('hotel_id') }}',
                    nombre: @json(old('nombre')),
                    numero: @json(old('numero_habitacion')),
                    capacidad: @json(old('cantidad_personas')),
                    precioRegular: @json(old('precio_regular')),
                    precioPromo: @json(old('precio_promo')),
                });
            @else
                roomCreateMode('{{ old('hotel_id') }}');
                roomNombre.value = @json(old('nombre'));
                roomNumero.value = @json(old('numero_habitacion'));
                roomCapacidad.value = @json(old('cantidad_personas'));
                roomPrecioRegular.value = @json(old('precio_regular'));
                roomPrecioPromo.value = @json(old('precio_promo'));
            @endif
            openRoomModal();
        @elseif (old('nombre') !== null)
            @if (old('_editing_id'))
                hotelEditMode({
                    id: '{{ old('_editing_id') }}',
                    destinoId: '{{ old('destino_id') }}',
                    codigo: @json(old('codigo')),
                    nombre: @json(old('nombre')),
                    direccion: @json(old('direccion')),
                    telefono: @json(old('telefono')),
                    email: @json(old('email')),
                    imagen: '',
                });
            @else
                hotelCreateMode();
                hotelCodigo.value = @json(old('codigo'));
                hotelNombre.value = @json(old('nombre'));
                hotelDireccion.value = @json(old('direccion'));
                hotelTelefono.value = @json(old('telefono'));
                hotelEmail.value = @json(old('email'));
                if (hotelDestino) hotelDestino.value = '{{ old('destino_id') }}';
            @endif
            openHotelModal();
        @endif
    @endif
});
</script>
@endsection
