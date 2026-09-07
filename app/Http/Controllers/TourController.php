<?php

namespace App\Http\Controllers;

use App\Models\Destino;
use App\Models\HospedajePasajero;
use App\Models\Hotel;
use App\Models\Itinerary;
use App\Models\ItineraryPackage;
use App\Models\Passenger;
use App\Models\Proveedor;
use App\Models\ProveedorTourFecha;
use App\Models\Room;
use App\Models\Tour;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TourController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');

        $query = Tour::with('itineraries')->latest();

        if (auth()->user()->hasRole('Agencia')) {
            $query->where('agencia_id', auth()->id());
        }

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('codigo', 'like', "%{$q}%")
                    ->orWhere('nombre', 'like', "%{$q}%")
                    ->orWhere('nombre_pax', 'like', "%{$q}%")
                    ->orWhere('agente', 'like', "%{$q}%");
            });
        }

        $tours = $query->paginate(15)->withQueryString();
        $paquetes = ItineraryPackage::orderBy('nombre')->get(['id', 'nombre', 'dias', 'descripcion']);
        return view('tours.index', compact('tours', 'paquetes', 'q'));
    }

    public function createChoice()
    {
        $paquetes = ItineraryPackage::orderBy('nombre')->get(['id', 'nombre', 'dias', 'descripcion']);
        return view('tours.create-choice', compact('paquetes'));
    }

    public function create(Request $request)
    {
        $proveedores = Proveedor::with('tipo')->orderBy('nombre')->get();
        $hoteles = Hotel::with('rooms', 'destino')->orderBy('nombre')->get();
        $itinerariosSeleccionados = $this->buildItinerariosSeleccionadosFromOld();
        $destinos = Destino::with('categorias')->orderBy('nombre')->get();
        $paqueteSeleccionadoId = $request->query('paquete');
        return view('tours.create', compact('proveedores', 'hoteles', 'itinerariosSeleccionados', 'destinos', 'paqueteSeleccionadoId'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateTour($request);

        $fechas = $this->computeFechaRange($validated['itinerarios_fecha'] ?? []);

        $data = $this->extractTourFields($validated);
        $data['agencia_id'] = auth()->id();
        $data['fecha_inicio'] = $fechas['fecha_inicio'];
        $data['fecha_fin'] = $fechas['fecha_fin'];
        $data['codigo'] = $this->generateCodigo(auth()->user(), $validated['nombre_pax'] ?? '', $fechas['fecha_inicio']);
        $data['nombre'] = $data['codigo'];
        if ($request->hasFile('archivo')) {
            $data = array_merge($data, $this->storeArchivoUnico($request->file('archivo'), 'tours'));
        }

        $tour = DB::transaction(function () use ($request, $validated, $data) {
            $tour = Tour::create($data);
            $this->syncItinerarios($tour, $validated);
            $this->syncPasajeros($request, $tour, $validated['pasajeros'] ?? []);
            $this->syncHospedajes($tour, $validated['hospedajes'] ?? []);
            $this->syncProveedores($request, $tour, $validated['proveedores'] ?? []);
            return $tour;
        });

        return redirect()->route('tours.show', $tour)->with('success', 'Tour creado exitosamente.');
    }

    public function show(Tour $tour)
    {
        $tour->load([
            'itineraries',
            'hospedajes.hotel',
            'hospedajes.rooms',
            'hospedajes.asignaciones.passenger',
            'hospedajes.asignaciones.room',
            'proveedores.imagenes',
            'proveedores.tipo',
            'passengers',
        ]);
        return view('tours.show', compact('tour'));
    }

    public function edit(Tour $tour)
    {
        $tour->load('itineraries', 'proveedores', 'proveedorFechas', 'hospedajes.rooms', 'passengers');
        $proveedores = Proveedor::with('tipo')->orderBy('nombre')->get();
        $hoteles = Hotel::with('rooms', 'destino')->orderBy('nombre')->get();
        $itinerariosSeleccionados = old('itinerarios') !== null
            ? $this->buildItinerariosSeleccionadosFromOld()
            : $tour->itineraries;
        $destinos = Destino::with('categorias')->orderBy('nombre')->get();
        return view('tours.edit', compact('tour', 'proveedores', 'hoteles', 'itinerariosSeleccionados', 'destinos'));
    }

    public function update(Request $request, Tour $tour)
    {
        $validated = $this->validateTour($request, $tour);

        $fechas = $this->computeFechaRange($validated['itinerarios_fecha'] ?? []);

        $data = $this->extractTourFields($validated);
        $data['fecha_inicio'] = $fechas['fecha_inicio'];
        $data['fecha_fin'] = $fechas['fecha_fin'];
        if ($request->hasFile('archivo')) {
            if ($tour->imagen_path) Storage::disk('public')->delete($tour->imagen_path);
            if ($tour->pdf_path) Storage::disk('public')->delete($tour->pdf_path);
            $data = array_merge($data, $this->storeArchivoUnico($request->file('archivo'), 'tours'));
        }

        DB::transaction(function () use ($request, $validated, $data, $tour) {
            $tour->update($data);
            $this->syncItinerarios($tour, $validated);
            $this->syncPasajeros($request, $tour, $validated['pasajeros'] ?? []);
            $this->syncHospedajes($tour, $validated['hospedajes'] ?? []);
            $this->syncProveedores($request, $tour, $validated['proveedores'] ?? []);
        });

        return redirect()->route('tours.show', $tour)->with('success', 'Tour actualizado.');
    }

    public function destroy(Tour $tour)
    {
        if ($tour->imagen_path) Storage::disk('public')->delete($tour->imagen_path);
        if ($tour->pdf_path) Storage::disk('public')->delete($tour->pdf_path);
        foreach ($tour->passengers as $passenger) {
            if ($passenger->imagen_path) Storage::disk('public')->delete($passenger->imagen_path);
            if ($passenger->pdf_path) Storage::disk('public')->delete($passenger->pdf_path);
        }
        $tour->delete();
        return redirect()->route('tours.index')->with('success', 'Tour eliminado.');
    }

    public function duplicate(Tour $tour)
    {
        $tour->load('itineraries', 'hospedajes.rooms', 'proveedores', 'proveedorFechas');

        $nuevo = DB::transaction(function () use ($tour) {
            $data = $tour->only([
                'idioma', 'moneda', 'precio_adicional', 'descuento_especial', 'notas_adicionales', 'reserva_pct',
                'agente', 'canal', 'pax_adultos', 'pax_ninos', 'pais', 'codigo_pais', 'departamento_estado',
                'fecha_llegada', 'hora_llegada', 'fecha_salida_viaje', 'hora_salida_viaje',
                'fecha_inicio', 'fecha_fin',
            ]);
            $data['agencia_id'] = auth()->id();
            $data['fecha_cotizacion'] = now()->toDateString();
            $data['nombre_pax'] = null;
            $data['contacto_pax'] = null;
            $data['edad_pax'] = null;
            $data['codigo'] = $this->generateCodigo(auth()->user(), '', $data['fecha_inicio']);
            $data['nombre'] = $data['codigo'];

            $nuevo = Tour::create($data);

            $pivotData = [];
            foreach ($tour->itineraries as $itinerario) {
                $pivotData[$itinerario->id] = [
                    'fecha' => $itinerario->pivot->fecha,
                    'cantidad_pax' => $itinerario->pivot->cantidad_pax,
                    'cantidad_pax_ninos' => $itinerario->pivot->cantidad_pax_ninos,
                    'orden' => $itinerario->pivot->orden,
                ];
            }
            $nuevo->itineraries()->sync($pivotData);

            foreach ($tour->hospedajes as $hospedaje) {
                $nuevoHospedaje = $nuevo->hospedajes()->create([
                    'hotel_id' => $hospedaje->hotel_id,
                    'fecha_ingreso' => $hospedaje->fecha_ingreso,
                    'fecha_salida' => $hospedaje->fecha_salida,
                ]);
                $nuevoHospedaje->rooms()->sync($hospedaje->rooms->pluck('id')->all());
            }

            $nuevo->proveedores()->sync($tour->proveedores->pluck('id')->all());

            $filasFechas = $tour->proveedorFechas->map(fn ($pf) => [
                'tour_id' => $nuevo->id,
                'proveedor_id' => $pf->proveedor_id,
                'fecha' => $pf->fecha,
                'created_at' => now(),
                'updated_at' => now(),
            ])->all();
            if ($filasFechas) {
                ProveedorTourFecha::insertOrIgnore($filasFechas);
            }

            return $nuevo;
        });

        return redirect()->route('tours.edit', $nuevo)->with('success', 'Cotización duplicada. Completa los datos del nuevo cliente y guarda.');
    }

    public function pdf(Tour $tour)
    {
        $tour->load([
            'itineraries',
            'hospedajes.hotel',
            'hospedajes.rooms',
            'agencia',
        ]);

        $agencia = $tour->agencia;
        $resumen = $tour->calcularResumenFactura();
        $terminos = $agencia?->terminosCondiciones($tour->idioma);

        $defaults = ['#0f172a', '#0891b2', '#6d28d9'];
        $colores = $agencia?->colores;
        $brand = [
            'color1' => $colores[0] ?? $defaults[0],
            'color2' => $colores[1] ?? $colores[0] ?? $defaults[1],
            'color3' => $colores[2] ?? $colores[1] ?? $colores[0] ?? $defaults[2],
        ];

        $nombreArchivo = 'Cotizacion_' . $tour->codigo . '_' . ($tour->nombre_pax ?: 'sin_nombre');
        $nombreArchivo = preg_replace('/[^A-Za-z0-9_\-]+/', '_', $nombreArchivo) . '.pdf';

        $localeMap = ['espanol' => 'es', 'ingles' => 'en', 'portugues' => 'pt'];
        $locale = $localeMap[$tour->idioma] ?? 'en';
        $localeAnterior = app()->getLocale();
        app()->setLocale($locale);

        $pdf = Pdf::loadView('tours.pdf', compact('tour', 'agencia', 'resumen', 'terminos', 'brand'))->setPaper('a4');
        $response = $pdf->stream($nombreArchivo);

        app()->setLocale($localeAnterior);

        return $response;
    }

    public function itinerarioPdf(Tour $tour)
    {
        $tour->load([
            'itineraries.destino',
            'itineraries.categoria',
            'hospedajes.hotel',
            'hospedajes.rooms',
            'passengers',
            'agencia',
        ]);

        $agencia = $tour->agencia;
        $terminos = $agencia?->terminosCondiciones($tour->idioma);

        $defaults = ['#295353', '#5f7e7e', '#94a9a9'];
        $colores = $agencia?->colores;
        $brand = [
            'color1' => $colores[0] ?? $defaults[0],
            'color2' => $colores[1] ?? $colores[0] ?? $defaults[1],
            'color3' => $colores[2] ?? $colores[1] ?? $colores[0] ?? $defaults[2],
        ];

        ['dias' => $dias, 'sinFecha' => $sinFecha] = $this->buildItinerarioDias($tour);

        $nombreArchivo = 'Itinerario_' . $tour->codigo . '_' . ($tour->nombre_pax ?: 'sin_nombre');
        $nombreArchivo = preg_replace('/[^A-Za-z0-9_\-]+/', '_', $nombreArchivo) . '.pdf';

        $localeMap = ['espanol' => 'es', 'ingles' => 'en', 'portugues' => 'pt'];
        $locale = $localeMap[$tour->idioma] ?? 'es';
        $localeAnterior = app()->getLocale();
        app()->setLocale($locale);

        $pdf = Pdf::loadView('tours.itinerario-pdf', compact('tour', 'agencia', 'brand', 'dias', 'sinFecha', 'terminos'))->setPaper('a4');
        $response = $pdf->stream($nombreArchivo);

        app()->setLocale($localeAnterior);

        return $response;
    }

    private function buildItinerarioDias(Tour $tour): array
    {
        $porFecha = $tour->itineraries
            ->filter(fn ($it) => $it->pivot->fecha)
            ->groupBy(fn ($it) => \Carbon\Carbon::parse($it->pivot->fecha)->format('Y-m-d'));

        $sinFecha = $tour->itineraries
            ->filter(fn ($it) => !$it->pivot->fecha)
            ->sortBy('pivot.orden')
            ->values();

        $dias = [];
        $numero = 1;

        foreach ($porFecha->keys()->sort()->values() as $fecha) {
            $actividades = $porFecha->get($fecha)->sortBy('pivot.orden')->values();

            $hospedaje = $tour->hospedajes->first(function ($h) use ($fecha) {
                return $h->fecha_ingreso && \Carbon\Carbon::parse($h->fecha_ingreso)->format('Y-m-d') === $fecha;
            });

            $dias[] = [
                'numero' => $numero++,
                'fecha' => \Carbon\Carbon::parse($fecha),
                'actividades' => $actividades,
                'hospedaje' => $hospedaje,
            ];
        }

        return ['dias' => $dias, 'sinFecha' => $sinFecha];
    }

    public function habitaciones(Tour $tour)
    {
        $tour->load(['hospedajes.hotel.rooms', 'hospedajes.rooms', 'hospedajes.asignaciones', 'passengers']);
        return view('tours.habitaciones', compact('tour'));
    }

    public function guardarHabitaciones(Request $request, Tour $tour)
    {
        $tour->load('hospedajes.hotel', 'passengers');
        $hospedajeIds = $tour->hospedajes->pluck('id')->all();
        $passengerIds = $tour->passengers->pluck('id')->all();

        $validator = Validator::make($request->all(), [
            'asignaciones' => 'nullable|array',
        ]);

        $validator->after(function ($validator) use ($request, $tour, $hospedajeIds, $passengerIds) {
            $asignaciones = $request->input('asignaciones', []);
            $capacidadUsada = [];

            foreach ($asignaciones as $hospedajeId => $porPasajero) {
                if (!in_array((int) $hospedajeId, $hospedajeIds, true)) {
                    $validator->errors()->add('asignaciones', 'Hospedaje inválido.');
                    continue;
                }

                $hospedaje = $tour->hospedajes->firstWhere('id', (int) $hospedajeId);

                foreach ($porPasajero as $passengerId => $roomId) {
                    if (empty($roomId)) {
                        continue;
                    }

                    if (!in_array((int) $passengerId, $passengerIds, true)) {
                        $validator->errors()->add('asignaciones', 'Pasajero inválido.');
                        continue;
                    }

                    $room = Room::find($roomId);
                    if (!$room || $room->hotel_id !== $hospedaje->hotel_id) {
                        $validator->errors()->add(
                            "asignaciones.$hospedajeId.$passengerId",
                            'Esa habitación no pertenece al hotel de este hospedaje.'
                        );
                        continue;
                    }

                    $capacidadUsada[$hospedajeId][$room->id][] = $passengerId;

                    $conflicto = HospedajePasajero::where('room_id', $room->id)
                        ->where('hospedaje_id', '!=', $hospedaje->id)
                        ->whereHas('hospedaje', function ($q) use ($hospedaje) {
                            $q->where('fecha_ingreso', '<=', $hospedaje->fecha_salida)
                              ->where('fecha_salida', '>=', $hospedaje->fecha_ingreso);
                        })
                        ->with('hospedaje.tour')
                        ->first();

                    if ($conflicto) {
                        $validator->errors()->add(
                            "asignaciones.$hospedajeId.$passengerId",
                            "La habitación {$room->nombre} #{$room->numero_habitacion} ya está reservada para el tour \"{$conflicto->hospedaje->tour->nombre}\" en fechas que se cruzan."
                        );
                    }
                }
            }

            foreach ($capacidadUsada as $hospedajeId => $porRoom) {
                foreach ($porRoom as $roomId => $passengerList) {
                    $room = Room::find($roomId);
                    if ($room && count($passengerList) > $room->cantidad_personas) {
                        $validator->errors()->add(
                            'asignaciones',
                            "La habitación {$room->nombre} (#{$room->numero_habitacion}) tiene capacidad para {$room->cantidad_personas} persona(s), pero se asignaron " . count($passengerList) . ' en ese hospedaje.'
                        );
                    }
                }
            }
        });

        $validated = $validator->validate();
        $asignaciones = $validated['asignaciones'] ?? [];

        DB::transaction(function () use ($asignaciones, $tour) {
            foreach ($tour->hospedajes as $hospedaje) {
                foreach ($tour->passengers as $passenger) {
                    $roomId = $asignaciones[$hospedaje->id][$passenger->id] ?? null;

                    if (empty($roomId)) {
                        HospedajePasajero::where('hospedaje_id', $hospedaje->id)
                            ->where('passenger_id', $passenger->id)
                            ->delete();
                        continue;
                    }

                    HospedajePasajero::updateOrCreate(
                        ['hospedaje_id' => $hospedaje->id, 'passenger_id' => $passenger->id],
                        ['room_id' => $roomId]
                    );
                }
            }
        });

        return redirect()->route('tours.show', $tour)->with('success', 'Habitaciones asignadas correctamente.');
    }

    private function validateTour(Request $request, ?Tour $tour = null): array
    {
        $validator = Validator::make($request->all(), [
            'archivo' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:10240',
            'idioma' => 'nullable|in:ingles,espanol,portugues',
            'moneda' => 'nullable|in:USD,PEN',
            'precio_adicional' => 'nullable|numeric',
            'descuento_especial' => 'nullable|numeric',
            'notas_adicionales' => 'nullable|string',
            'reserva_pct' => 'nullable|integer|min:0|max:100',

            // Paso 1: Datos Pax
            'agente' => 'nullable|string|max:255',
            'nombre_pax' => 'nullable|string|max:255',
            'edad_pax' => 'nullable|integer|min:0|max:120',
            'contacto_pax' => 'nullable|string|max:255',
            'canal' => 'nullable|string|max:255',
            'fecha_cotizacion' => 'nullable|date',
            'pax_adultos' => 'nullable|integer|min:0',
            'pax_ninos' => 'nullable|integer|min:0',
            'pais' => 'nullable|string|max:255',
            'codigo_pais' => 'nullable|string|max:10',
            'departamento_estado' => 'nullable|string|max:255',
            'fecha_llegada' => 'nullable|date',
            'hora_llegada' => 'nullable|date_format:H:i,H:i:s',
            'fecha_salida_viaje' => 'nullable|date',
            'hora_salida_viaje' => 'nullable|date_format:H:i,H:i:s',

            'itinerarios' => 'nullable|array',
            'itinerarios.*' => 'nullable|integer|exists:itineraries,id',
            'itinerarios_fecha' => 'nullable|array',
            'itinerarios_fecha.*' => 'nullable|date',
            'itinerarios_cantidad' => 'nullable|array',
            'itinerarios_cantidad.*' => 'nullable|integer|min:1',
            'itinerarios_cantidad_ninos' => 'nullable|array',
            'itinerarios_cantidad_ninos.*' => 'nullable|integer|min:0',

            'pasajeros' => 'nullable|array',
            'pasajeros.*.id' => 'nullable|integer|exists:passengers,id',
            'pasajeros.*.nombre' => 'required|string|max:255',
            'pasajeros.*.fecha_nacimiento' => 'nullable|date',
            'pasajeros.*.edad' => 'nullable|integer|min:0',
            'pasajeros.*.correo' => 'nullable|email|max:255',
            'pasajeros.*.archivo' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:10240',

            'hospedajes' => 'nullable|array',
            'hospedajes.*.id' => 'nullable|integer|exists:hospedajes,id',
            'hospedajes.*.hotel_id' => 'required|exists:hotels,id',
            'hospedajes.*.fecha_ingreso' => 'required|date',
            'hospedajes.*.fecha_salida' => 'required|date|after_or_equal:hospedajes.*.fecha_ingreso',
            'hospedajes.*.rooms' => 'nullable|array',
            'hospedajes.*.rooms.*' => 'integer|exists:rooms,id',

            'proveedores' => 'nullable|array',
            'proveedores.*' => 'exists:proveedores,id',
            'proveedores_fechas' => 'nullable|array',
            'proveedores_fechas.*' => 'nullable|array',
            'proveedores_fechas.*.*' => 'nullable|date',
        ]);

        $validator->after(function ($validator) use ($request) {
            $itinerarios = $request->input('itinerarios', []);
            $fechasFilas = $request->input('itinerarios_fecha', []);
            $cantidades = $request->input('itinerarios_cantidad', []);
            if (count($itinerarios) !== count($fechasFilas) || count($itinerarios) !== count($cantidades)) {
                $validator->errors()->add('itinerarios', 'La fecha y la cantidad de pax deben indicarse para cada itinerario agregado.');
            }

            $hospedajes = $request->input('hospedajes', []);

            foreach ($hospedajes as $i => $data) {
                if (!empty($data['rooms']) && !empty($data['hotel_id'])) {
                    $roomFueraDeHotel = Room::whereIn('id', $data['rooms'])
                        ->where('hotel_id', '!=', $data['hotel_id'])
                        ->exists();
                    if ($roomFueraDeHotel) {
                        $validator->errors()->add(
                            "hospedajes.$i.rooms",
                            'Todas las habitaciones seleccionadas deben pertenecer al hotel elegido.'
                        );
                    }
                }
            }
        });

        return $validator->validate();
    }

    private function extractTourFields(array $validated): array
    {
        return [
            'idioma' => $validated['idioma'] ?? null,
            'moneda' => $validated['moneda'] ?? 'USD',
            'precio_adicional' => $validated['precio_adicional'] ?? 0,
            'descuento_especial' => $validated['descuento_especial'] ?? 0,
            'notas_adicionales' => $validated['notas_adicionales'] ?? null,
            'reserva_pct' => $validated['reserva_pct'] ?? 30,
            'agente' => $validated['agente'] ?? null,
            'nombre_pax' => $validated['nombre_pax'] ?? null,
            'edad_pax' => $validated['edad_pax'] ?? null,
            'contacto_pax' => $validated['contacto_pax'] ?? null,
            'canal' => $validated['canal'] ?? null,
            'fecha_cotizacion' => $validated['fecha_cotizacion'] ?? null,
            'pax_adultos' => $validated['pax_adultos'] ?? null,
            'pax_ninos' => $validated['pax_ninos'] ?? null,
            'pais' => $validated['pais'] ?? null,
            'codigo_pais' => $validated['codigo_pais'] ?? null,
            'departamento_estado' => $validated['departamento_estado'] ?? null,
            'fecha_llegada' => $validated['fecha_llegada'] ?? null,
            'hora_llegada' => $validated['hora_llegada'] ?? null,
            'fecha_salida_viaje' => $validated['fecha_salida_viaje'] ?? null,
            'hora_salida_viaje' => $validated['hora_salida_viaje'] ?? null,
        ];
    }

    private function syncItinerarios(Tour $tour, array $validated): void
    {
        $ids = $validated['itinerarios'] ?? [];
        $fechas = $validated['itinerarios_fecha'] ?? [];
        $cantidades = $validated['itinerarios_cantidad'] ?? [];
        $cantidadesNinos = $validated['itinerarios_cantidad_ninos'] ?? [];

        $pivotData = [];
        foreach ($ids as $i => $itinerarioId) {
            $pivotData[$itinerarioId] = [
                'fecha' => $fechas[$i],
                'cantidad_pax' => $cantidades[$i],
                'cantidad_pax_ninos' => $cantidadesNinos[$i] ?? 0,
                'orden' => $i,
            ];
        }

        $tour->itineraries()->sync($pivotData);
    }

    private function computeFechaRange(array $fechas): array
    {
        $fechasValidas = array_filter($fechas);

        return [
            'fecha_inicio' => $fechasValidas ? min($fechasValidas) : null,
            'fecha_fin' => $fechasValidas ? max($fechasValidas) : null,
        ];
    }

    private function generateCodigo($agencia, string $nombrePax, ?string $fechaReferencia): string
    {
        $agenciaWords = array_values(array_filter(preg_split('/\s+/', trim($agencia->name ?? ''))));
        if (count($agenciaWords) > 1) {
            $agenciaIniciales = implode('', array_map(fn($w) => mb_substr($w, 0, 1), $agenciaWords));
        } else {
            $agenciaIniciales = mb_substr($agenciaWords[0] ?? 'XX', 0, 2);
        }
        $agenciaIniciales = mb_strtoupper($agenciaIniciales);

        $paxWords = array_values(array_filter(preg_split('/\s+/', trim($nombrePax))));
        $paxIniciales = $paxWords
            ? mb_strtoupper(implode('', array_map(fn($w) => mb_substr($w, 0, 1), $paxWords)))
            : 'XX';

        $fecha = $fechaReferencia ? \Illuminate\Support\Carbon::parse($fechaReferencia) : now();
        $mes = $fecha->format('m');
        $anio = $fecha->format('y');

        $base = "{$agenciaIniciales}-{$mes}{$anio}-{$paxIniciales}";

        $codigo = $base;
        $sufijo = 1;
        while (Tour::where('codigo', $codigo)->exists()) {
            $sufijo++;
            $codigo = "{$base}-{$sufijo}";
        }

        return $codigo;
    }

    private function buildItinerariosSeleccionadosFromOld()
    {
        $ids = old('itinerarios', []);
        $fechas = old('itinerarios_fecha', []);
        $cantidades = old('itinerarios_cantidad', []);
        $cantidadesNinos = old('itinerarios_cantidad_ninos', []);
        $itinerariosById = Itinerary::whereIn('id', $ids)->get()->keyBy('id');

        return collect($ids)->map(function ($id, $i) use ($itinerariosById, $fechas, $cantidades, $cantidadesNinos) {
            $itinerario = $itinerariosById->get($id);
            if (!$itinerario) {
                return null;
            }
            $itinerario->pivot = (object) [
                'fecha' => $fechas[$i] ?? null,
                'cantidad_pax' => $cantidades[$i] ?? null,
                'cantidad_pax_ninos' => $cantidadesNinos[$i] ?? null,
            ];
            return $itinerario;
        })->filter()->values();
    }

    private function syncHospedajes(Tour $tour, array $hospedajesInput): void
    {
        $submittedIds = collect($hospedajesInput)->pluck('id')->filter()->all();

        $tour->hospedajes()->whereNotIn('id', $submittedIds ?: [0])->delete();

        foreach ($hospedajesInput as $hospedajeData) {
            $fields = [
                'hotel_id' => $hospedajeData['hotel_id'],
                'fecha_ingreso' => $hospedajeData['fecha_ingreso'],
                'fecha_salida' => $hospedajeData['fecha_salida'],
            ];

            if (!empty($hospedajeData['id'])) {
                $hospedaje = $tour->hospedajes()->whereKey($hospedajeData['id'])->first();
                $hospedaje->update($fields);
            } else {
                $hospedaje = $tour->hospedajes()->create($fields);
            }

            $hospedaje->rooms()->sync($hospedajeData['rooms'] ?? []);
        }
    }

    private function syncProveedores(Request $request, Tour $tour, array $proveedorIds): void
    {
        $tour->proveedores()->sync($proveedorIds);

        $tour->proveedorFechas()->delete();

        $fechasPorProveedor = $request->input('proveedores_fechas', []);
        $filas = [];
        foreach ($proveedorIds as $id) {
            foreach (array_filter($fechasPorProveedor[$id] ?? []) as $fecha) {
                $filas[] = [
                    'tour_id' => $tour->id,
                    'proveedor_id' => $id,
                    'fecha' => $fecha,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if ($filas) {
            ProveedorTourFecha::insertOrIgnore($filas);
        }
    }

    private function syncPasajeros(Request $request, Tour $tour, array $pasajerosInput): void
    {
        $submittedIds = collect($pasajerosInput)->pluck('id')->filter()->all();

        $tour->passengers()->whereNotIn('id', $submittedIds ?: [0])->get()
            ->each(function (Passenger $passenger) {
                if ($passenger->imagen_path) {
                    Storage::disk('public')->delete($passenger->imagen_path);
                }
                if ($passenger->pdf_path) {
                    Storage::disk('public')->delete($passenger->pdf_path);
                }
                $passenger->delete();
            });

        foreach ($pasajerosInput as $i => $pasajeroData) {
            $fields = [
                'nombre' => $pasajeroData['nombre'],
                'fecha_nacimiento' => $pasajeroData['fecha_nacimiento'] ?? null,
                'edad' => $pasajeroData['edad'] ?? null,
                'correo' => $pasajeroData['correo'] ?? null,
            ];

            $archivoFile = $request->file("pasajeros.$i.archivo");

            if (!empty($pasajeroData['id'])) {
                $passenger = $tour->passengers()->findOrFail($pasajeroData['id']);
                if ($archivoFile) {
                    if ($passenger->imagen_path) Storage::disk('public')->delete($passenger->imagen_path);
                    if ($passenger->pdf_path) Storage::disk('public')->delete($passenger->pdf_path);
                    $fields = array_merge($fields, $this->storeArchivoUnico($archivoFile, 'passengers'));
                }
                $passenger->update($fields);
            } else {
                if ($archivoFile) {
                    $fields = array_merge($fields, $this->storeArchivoUnico($archivoFile, 'passengers'));
                }
                $tour->passengers()->create($fields);
            }
        }
    }

    private function storeArchivoUnico(\Illuminate\Http\UploadedFile $file, string $folder): array
    {
        if (str_starts_with($file->getMimeType(), 'image/')) {
            return [
                'imagen_path' => $file->store("$folder/images", 'public'),
                'pdf_path' => null,
            ];
        }

        return [
            'imagen_path' => null,
            'pdf_path' => $file->store("$folder/pdfs", 'public'),
        ];
    }
}
