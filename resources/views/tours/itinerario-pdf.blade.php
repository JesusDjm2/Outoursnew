<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Itinerario {{ $tour->codigo }}</title>
<style>
    /* Margen real de página: así, cuando un bloque largo salta a la siguiente
       hoja de forma automática, esa hoja también arranca con un margen
       prudente (dompdf no reaplica el padding de un div en los saltos). */
    @page { margin: 16mm 15mm 20mm 15mm; }
    * { box-sizing: border-box; }
    /* OJO: el selector combinado "html, body { margin:0 }" hace que dompdf
       calcule mal el ancho de los paneles con margen negativo (.bleed) más
       abajo en este archivo — recorta ~15mm a la derecha. Un solo selector
       "body" evita el bug. No unir estas reglas de nuevo. */
    body { margin: 0; padding: 0; font-family: 'DejaVu Sans', sans-serif; color: #1e293b; font-size: 10pt; }
    table { border-collapse: collapse; }
    .page-break { page-break-before: always; }
    .content-page { }
    /* Anula el margen de @page para las hojas de foto a sangre completa. */
    .bleed { margin: -16mm -15mm -20mm -15mm; width: 210mm; height: 297mm; }
    .eyebrow { font-size: 8pt; letter-spacing: 2px; text-transform: uppercase; }
    .section-title { font-size: 13pt; font-weight: bold; margin: 0 0 6mm 0; }
    .avoid-break { page-break-inside: avoid; }
    .terminos-titulo { font-size: 13pt; font-weight: bold; padding-bottom: 3mm; margin: 0 0 6mm 0; border-bottom: 0.5mm solid; }
    .terminos-cuerpo { font-size: 9pt; color: #334155; line-height: 1.5; text-align: justify; }
    .terminos-cuerpo p { margin: 0 0 3mm 0; }
    .terminos-cuerpo ul, .terminos-cuerpo ol { margin: 0 0 3mm 0; padding-left: 6mm; }
    .rich-text { font-size: 8.5pt; color: #475569; line-height: 1.5; text-align: justify; }
    .rich-text p { margin: 0 0 2mm 0; }
    .rich-text ul, .rich-text ol { margin: 0 0 2mm 0; padding-left: 5mm; }
    .rich-text strong { color: #0f172a; }
    .rich-text h1, .rich-text h2, .rich-text h3, .rich-text h4, .rich-text h5, .rich-text h6 { margin: 0 0 1.5mm 0; font-size: 9.5pt; font-weight: bold; color: #0f172a; line-height: 1.3; text-align: left; }
</style>
</head>
<body>

@php
    $contrastFor = function ($hex) {
        $hex = ltrim($hex ?? '', '#');
        if (strlen($hex) === 3) { $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2]; }
        if (strlen($hex) !== 6 || !ctype_xdigit($hex)) { return '#ffffff'; }
        [$r, $g, $b] = [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))];
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
        return $luminance > 0.6 ? '#0f172a' : '#ffffff';
    };
    $onColor1 = $contrastFor($brand['color1']);

    $logoFullPath = $agencia?->logo_path && file_exists(public_path('storage/' . $agencia->logo_path))
        ? public_path('storage/' . $agencia->logo_path)
        : null;

    $localeMap = ['espanol' => 'es', 'ingles' => 'en', 'portugues' => 'pt'];
    $locale = $localeMap[$tour->idioma] ?? 'es';

    $formatoFechaLarga = $locale === 'en' ? 'F d, Y' : 'd \d\e F, Y';
    $formatoDiaLargo = $locale === 'en' ? 'l, F d' : 'l, d \d\e F';

    $formatFecha = fn ($fecha) => $fecha ? \Carbon\Carbon::parse($fecha)->translatedFormat($formatoFechaLarga) : null;
    $formatFechaCorta = fn ($fecha) => $fecha ? \Carbon\Carbon::parse($fecha)->translatedFormat('d/m/Y') : '—';
    $formatDiaLargo = fn ($fecha) => \Illuminate\Support\Str::ucfirst($fecha->translatedFormat($formatoDiaLargo));

    $totalPax = (int) ($tour->pax_adultos ?? 0) + (int) ($tour->pax_ninos ?? 0);
    $destinosRuta = $tour->itineraries->pluck('destino.nombre')->filter()->unique()->values();

    $totalDias = ($tour->fecha_inicio && $tour->fecha_fin)
        ? \Carbon\Carbon::parse($tour->fecha_inicio)->diffInDays(\Carbon\Carbon::parse($tour->fecha_fin)) + 1
        : null;

    // Calcula el tamaño/posición (en mm) para que una foto cubra la página A4 completa
    // sin deformarse, recortando el sobrante, sin importar su orientación u origen.
    $coverImageStyle = function (string $path, float $pageWmm = 210, float $pageHmm = 297) {
        $size = @getimagesize($path);
        if (!$size) {
            return "top: 0; left: 0; width: {$pageWmm}mm; height: {$pageHmm}mm;";
        }
        [$imgW, $imgH] = $size;
        $imgRatio = $imgW / $imgH;
        $pageRatio = $pageWmm / $pageHmm;

        if ($imgRatio > $pageRatio) {
            $heightMm = $pageHmm;
            $widthMm = $heightMm * $imgRatio;
            $left = -($widthMm - $pageWmm) / 2;
            return sprintf('top: 0; left: %.2fmm; width: %.2fmm; height: %.2fmm;', $left, $widthMm, $heightMm);
        }

        $widthMm = $pageWmm;
        $heightMm = $widthMm / $imgRatio;
        $top = -($heightMm - $pageHmm) / 2;
        return sprintf('top: %.2fmm; left: 0; width: %.2fmm; height: %.2fmm;', $top, $widthMm, $heightMm);
    };
    $portadaImgPath = public_path('img/itinerarios/fondo-inicial.jpg');
    $cierreImgPath = public_path('img/itinerarios/machu-picchu-cusco.jpg');

    $labels = [
        'es' => [
            'itinerario_de_viaje' => 'Itinerario de viaje &middot; Perú',
            'preparado_para' => 'Preparado especialmente para',
            'pasajero_generico' => 'Estimado(a) pasajero(a)',
            'inicio' => 'Inicio', 'fin' => 'Fin', 'pax' => 'Pax', 'dias' => 'Días',
            'detalle_reserva' => 'Detalle de la reserva',
            'datos_pasajero' => 'Datos del pasajero',
            'nombre' => 'Nombre', 'sin_datos' => 'Sin datos', 'contacto' => 'Contacto',
            'pais_origen' => 'País de origen',
            'pax_adultos_ninos' => 'Pax adultos / niños',
            'adultos_ninos_valor' => ':a adultos, :n niños',
            'fecha_llegada' => 'Fecha de llegada',
            'duracion_viaje' => 'Duración del viaje',
            'pasajeros_grupo' => 'Pasajeros del grupo',
            'edad' => 'Edad', 'correo' => 'Correo',
            'ruta_viaje' => 'Ruta del viaje',
            'programa_viaje' => 'Programa del viaje',
            'resumen_viaje' => 'Resumen del viaje',
            'fecha' => 'Fecha', 'actividad' => 'Actividad',
            'itinerario_dia_a_dia' => 'Itinerario día a día',
            'dia' => 'Día',
            'alojamiento' => 'Alojamiento', 'hotel_por_confirmar' => 'Hotel por confirmar',
            'sin_actividades' => 'Aún no se han programado actividades con fecha para este viaje.',
            'actividades_adicionales' => 'Actividades adicionales',
            'incluye' => 'Incluye', 'no_incluye' => 'No incluye',
            'gracias' => '¡Gracias por elegirnos!',
            'acompaniar' => 'Estamos para acompañarte en cada paso de tu viaje por Perú.',
            'telefono_whatsapp' => 'Teléfono / WhatsApp',
            'ruc' => 'RUC',
            'agencia_default' => 'Agencia de Viajes',
            'terminos_titulo' => 'Términos y Condiciones',
        ],
        'en' => [
            'itinerario_de_viaje' => 'Travel itinerary &middot; Peru',
            'preparado_para' => 'Specially prepared for',
            'pasajero_generico' => 'Dear passenger',
            'inicio' => 'Start', 'fin' => 'End', 'pax' => 'Pax', 'dias' => 'Days',
            'detalle_reserva' => 'Booking details',
            'datos_pasajero' => 'Passenger details',
            'nombre' => 'Name', 'sin_datos' => 'No data', 'contacto' => 'Contact',
            'pais_origen' => 'Country of origin',
            'pax_adultos_ninos' => 'Adult / children pax',
            'adultos_ninos_valor' => ':a adults, :n children',
            'fecha_llegada' => 'Arrival date',
            'duracion_viaje' => 'Trip duration',
            'pasajeros_grupo' => 'Group passengers',
            'edad' => 'Age', 'correo' => 'Email',
            'ruta_viaje' => 'Trip route',
            'programa_viaje' => 'Trip program',
            'resumen_viaje' => 'Trip summary',
            'fecha' => 'Date', 'actividad' => 'Activity',
            'itinerario_dia_a_dia' => 'Day-by-day itinerary',
            'dia' => 'Day',
            'alojamiento' => 'Accommodation', 'hotel_por_confirmar' => 'Hotel to be confirmed',
            'sin_actividades' => 'No dated activities have been scheduled for this trip yet.',
            'actividades_adicionales' => 'Additional activities',
            'incluye' => 'Included', 'no_incluye' => 'Not included',
            'gracias' => 'Thank you for choosing us!',
            'acompaniar' => 'We are here to support you every step of your trip through Peru.',
            'telefono_whatsapp' => 'Phone / WhatsApp',
            'ruc' => 'Tax ID',
            'agencia_default' => 'Travel Agency',
            'terminos_titulo' => 'Terms and Conditions',
        ],
        'pt' => [
            'itinerario_de_viaje' => 'Roteiro de viagem &middot; Peru',
            'preparado_para' => 'Preparado especialmente para',
            'pasajero_generico' => 'Prezado(a) passageiro(a)',
            'inicio' => 'Início', 'fin' => 'Fim', 'pax' => 'Pax', 'dias' => 'Dias',
            'detalle_reserva' => 'Detalhes da reserva',
            'datos_pasajero' => 'Dados do passageiro',
            'nombre' => 'Nome', 'sin_datos' => 'Sem dados', 'contacto' => 'Contato',
            'pais_origen' => 'País de origem',
            'pax_adultos_ninos' => 'Pax adultos / crianças',
            'adultos_ninos_valor' => ':a adultos, :n crianças',
            'fecha_llegada' => 'Data de chegada',
            'duracion_viaje' => 'Duração da viagem',
            'pasajeros_grupo' => 'Passageiros do grupo',
            'edad' => 'Idade', 'correo' => 'E-mail',
            'ruta_viaje' => 'Rota da viagem',
            'programa_viaje' => 'Programa da viagem',
            'resumen_viaje' => 'Resumo da viagem',
            'fecha' => 'Data', 'actividad' => 'Atividade',
            'itinerario_dia_a_dia' => 'Roteiro dia a dia',
            'dia' => 'Dia',
            'alojamiento' => 'Hospedagem', 'hotel_por_confirmar' => 'Hotel a confirmar',
            'sin_actividades' => 'Ainda não há atividades com data programadas para esta viagem.',
            'actividades_adicionales' => 'Atividades adicionais',
            'incluye' => 'Inclui', 'no_incluye' => 'Não inclui',
            'gracias' => 'Obrigado por nos escolher!',
            'acompaniar' => 'Estamos aqui para te acompanhar em cada etapa da sua viagem pelo Peru.',
            'telefono_whatsapp' => 'Telefone / WhatsApp',
            'ruc' => 'CNPJ/RUC',
            'agencia_default' => 'Agência de Viagens',
            'terminos_titulo' => 'Termos e Condições',
        ],
    ];
    $t = $labels[$locale];

    $abrirEnlacesEnNuevaPestana = function (?string $html): ?string {
        if (!$html) {
            return $html;
        }
        return preg_replace_callback('/<a\s+([^>]*)>/i', function ($m) {
            $attrs = $m[1];
            if (!preg_match('/\btarget\s*=/i', $attrs)) {
                $attrs .= ' target="_blank" rel="noopener"';
            }
            return '<a ' . $attrs . '>';
        }, $html);
    };
    $terminosHtml = $abrirEnlacesEnNuevaPestana($terminos ?? null);

    // El editor (Quill) guarda TODAS las listas como <ol> y distingue el tipo
    // con el atributo data-list="bullet|ordered" más CSS propio del editor que
    // dompdf no carga. Sin esto, las listas con viñetas salen numeradas en el PDF.
    $normalizarListasQuill = function (?string $html): ?string {
        if (!$html || !str_contains($html, 'data-list')) {
            return $html;
        }

        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="utf-8" ?><div>' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xpath = new \DOMXPath($doc);
        foreach (iterator_to_array($xpath->query('//ol[.//li[@data-list]]')) as $ol) {
            $grupos = [];
            foreach (iterator_to_array($ol->childNodes) as $li) {
                if (!($li instanceof \DOMElement) || strtolower($li->tagName) !== 'li') {
                    continue;
                }
                foreach (iterator_to_array($xpath->query('.//span[contains(@class,"ql-ui")]', $li)) as $span) {
                    $li->removeChild($span);
                }
                $tipo = $li->getAttribute('data-list') === 'bullet' ? 'ul' : 'ol';
                $li->removeAttribute('data-list');
                $ultimo = end($grupos);
                if ($ultimo && $ultimo['tipo'] === $tipo) {
                    $grupos[count($grupos) - 1]['items'][] = $li;
                } else {
                    $grupos[] = ['tipo' => $tipo, 'items' => [$li]];
                }
            }

            $fragmento = $doc->createDocumentFragment();
            foreach ($grupos as $grupo) {
                $lista = $doc->createElement($grupo['tipo']);
                foreach ($grupo['items'] as $li) {
                    $lista->appendChild($li);
                }
                $fragmento->appendChild($lista);
            }
            $ol->parentNode->replaceChild($fragmento, $ol);
        }

        $wrapper = $doc->getElementsByTagName('div')->item(0);
        $resultado = '';
        foreach ($wrapper->childNodes as $child) {
            $resultado .= $doc->saveHTML($child);
        }
        return $resultado;
    };
@endphp

{{-- ============================= PORTADA ============================= --}}
<div class="bleed" style="position: relative; background-color: {{ $brand['color1'] }}; overflow: hidden;">

    {{-- Foto de fondo --}}
    <img src="{{ $portadaImgPath }}" style="position: absolute; {{ $coverImageStyle($portadaImgPath) }}">

    {{-- Logo --}}
    <div style="position: absolute; top: 18mm; left: 0; width: 210mm; text-align: center;">
        @if($logoFullPath)
            <img src="{{ $logoFullPath }}" style="display: inline-block; max-width: 68mm; max-height: 26mm; box-shadow: 0 2mm 5mm rgba(0,0,0,0.6);">
        @else
            <span style="display: inline-block; padding: 4mm 8mm; background-color: #ffffff; border-radius: 4mm; font-size: 13mm; font-weight: bold; color: {{ $brand['color1'] }}; box-shadow: 0 2mm 5mm rgba(0,0,0,0.6);">{{ mb_strtoupper(mb_substr($agencia->name ?? 'A', 0, 1)) }}</span>
        @endif
    </div>

    {{-- Título del viaje: capa de sombra (dompdf no soporta text-shadow, se simula duplicando el texto) --}}
    <div style="position: absolute; top: 90.7mm; left: 15.6mm; width: 180mm; text-align: center;">
        <p class="eyebrow" style="margin: 0; padding: 2mm 8mm; visibility: hidden;">{!! $t['itinerario_de_viaje'] !!}</p>
        <h1 style="margin: 6mm 0 0 0; color: #000000; opacity: 0.55; font-size: 34pt; font-weight: bold; line-height: 1.2;">{{ $tour->nombre }}</h1>
        <p class="eyebrow" style="margin: 8mm 0 0 0; color: #000000; opacity: 0.55;">{{ $t['preparado_para'] }}</p>
        <p style="margin: 2mm 0 0 0; color: #000000; opacity: 0.45; font-size: 24pt; font-weight: bold;">{{ $tour->nombre_pax ?: $t['pasajero_generico'] }}</p>
    </div>

    {{-- Título del viaje --}}
    <div style="position: absolute; top: 90mm; left: 15mm; width: 180mm; text-align: center;">
        <p class="eyebrow" style="display: inline-block; margin: 0; padding: 2mm 8mm; background-color: {{ $brand['color3'] }}; border-radius: 20mm; color: {{ $contrastFor($brand['color3']) }}; box-shadow: 0 1.5mm 3mm rgba(0,0,0,0.4);">{!! $t['itinerario_de_viaje'] !!}</p>
        <h1 style="margin: 6mm 0 0 0; color: #ffffff; font-size: 34pt; font-weight: bold; line-height: 1.2;">{{ $tour->nombre }}</h1>
        <p class="eyebrow" style="margin: 8mm 0 0 0; color: #ffffff;">{{ $t['preparado_para'] }}</p>
        <p style="margin: 2mm 0 0 0; color: #ffffff; font-size: 24pt; font-weight: bold;">{{ $tour->nombre_pax ?: $t['pasajero_generico'] }}</p>
        <span style="display: inline-block; max-width: 120mm; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin-top: 5mm; padding: 2mm 6mm; background-color: #ffffff; border-radius: 20mm; color: {{ $brand['color1'] }}; font-size: 8pt; font-weight: bold; letter-spacing: 1px; box-shadow: 0 1.5mm 3mm rgba(0,0,0,0.4);">
            {{ $tour->codigo }}
        </span>
    </div>

    {{-- Panel inferior: fechas + contacto (color sólido de marca) --}}
    <div style="position: absolute; bottom: 0; left: 0; width: 210mm; background-color: {{ $brand['color1'] }};">
      <div style="padding: 11mm 15mm 9mm 15mm;">
        <table style="width: 100%; color: {{ $onColor1 }}; font-size: 11pt;">
            <tr>
                <td style="width: 25%; text-align: center; border-left: 0.3mm solid {{ $onColor1 }};">
                    <p style="margin: 0; font-size: 8pt; text-transform: uppercase;">{{ $t['inicio'] }}</p>
                    <p style="margin: 1.5mm 0 0 0; font-weight: bold;">{{ $formatFechaCorta($tour->fecha_inicio) }}</p>
                </td>
                <td style="width: 25%; text-align: center; border-left: 0.3mm solid {{ $onColor1 }};">
                    <p style="margin: 0; font-size: 8pt; text-transform: uppercase;">{{ $t['fin'] }}</p>
                    <p style="margin: 1.5mm 0 0 0; font-weight: bold;">{{ $formatFechaCorta($tour->fecha_fin) }}</p>
                </td>
                <td style="width: 25%; text-align: center; border-left: 0.3mm solid {{ $onColor1 }};">
                    <p style="margin: 0; font-size: 8pt; text-transform: uppercase;">{{ $t['dias'] }}</p>
                    <p style="margin: 1.5mm 0 0 0; font-weight: bold;">{{ $totalDias ?: '—' }}</p>
                </td>
                <td style="width: 25%; text-align: center; border-left: 0.3mm solid {{ $onColor1 }}; border-right: 0.3mm solid {{ $onColor1 }};">
                    <p style="margin: 0; font-size: 8pt; text-transform: uppercase;">{{ $t['pax'] }}</p>
                    <p style="margin: 1.5mm 0 0 0; font-weight: bold;">{{ $totalPax ?: '—' }}</p>
                </td>
            </tr>
        </table>
        @if($destinosRuta->isNotEmpty())
        <p style="margin: 6mm 0 0 0; text-align: center; color: {{ $onColor1 }}; font-size: 10pt; font-weight: bold;">{{ $destinosRuta->implode('  •  ') }}</p>
        @endif

        <div style="margin-top: 8mm; padding-top: 6mm; border-top: 0.3mm solid {{ $onColor1 }};">
            <table style="width: 100%; color: {{ $onColor1 }}; font-size: 10pt;">
                <tr>
                    @if($agencia?->telefono || $agencia?->whatsapp)
                    <td style="text-align: center; font-weight: bold;">{{ $agencia->whatsapp ?: $agencia->telefono }}</td>
                    @endif
                    @if($agencia?->email)
                    <td style="text-align: center; font-weight: bold;">{{ $agencia->email }}</td>
                    @endif
                    @if($agencia?->direccion)
                    <td style="text-align: center; font-weight: bold;">{{ $agencia->direccion }}</td>
                    @endif
                </tr>
            </table>
        </div>
      </div>
    </div>
</div>

{{-- ============================= DATOS DEL VIAJE ============================= --}}
<div class="content-page page-break">
    <table style="width: 100%; margin-bottom: 10mm;">
        <tr>
            <td style="width: 12mm; height: 12mm; background-color: {{ $brand['color1'] }}; border-radius: 50%;"></td>
            <td style="padding-left: 4mm;">
                <p class="eyebrow" style="margin: 0; color: {{ $brand['color2'] }};">{{ $agencia->name ?? $t['agencia_default'] }}</p>
                <p style="margin: 0; font-size: 9pt; color: #64748b;">{{ $t['detalle_reserva'] }}</p>
            </td>
        </tr>
    </table>

    <p class="section-title" style="color: {{ $brand['color1'] }};">{{ $t['datos_pasajero'] }}</p>
    <table style="width: 100%; margin-bottom: 8mm; font-size: 9.5pt;">
        <tr>
            <td style="width: 50%; padding: 2mm 0; border-bottom: 0.2mm solid #e2e8f0;">
                <span style="color: #94a3b8; font-size: 7.5pt; text-transform: uppercase;">{{ $t['nombre'] }}</span><br>
                <span style="font-weight: bold;">{{ $tour->nombre_pax ?: $t['sin_datos'] }}</span>
            </td>
            <td style="width: 50%; padding: 2mm 0 2mm 6mm; border-bottom: 0.2mm solid #e2e8f0;">
                <span style="color: #94a3b8; font-size: 7.5pt; text-transform: uppercase;">{{ $t['contacto'] }}</span><br>
                <span style="font-weight: bold;">{{ trim(($tour->codigo_pais ?? '') . ' ' . ($tour->contacto_pax ?? '')) ?: $t['sin_datos'] }}</span>
            </td>
        </tr>
        <tr>
            <td style="width: 50%; padding: 2mm 0; border-bottom: 0.2mm solid #e2e8f0;">
                <span style="color: #94a3b8; font-size: 7.5pt; text-transform: uppercase;">{{ $t['pais_origen'] }}</span><br>
                <span style="font-weight: bold;">{{ $tour->pais ?: $t['sin_datos'] }}</span>
            </td>
            <td style="width: 50%; padding: 2mm 0 2mm 6mm; border-bottom: 0.2mm solid #e2e8f0;">
                <span style="color: #94a3b8; font-size: 7.5pt; text-transform: uppercase;">{{ $t['pax_adultos_ninos'] }}</span><br>
                <span style="font-weight: bold;">{{ str_replace([':a', ':n'], [$tour->pax_adultos ?? 0, $tour->pax_ninos ?? 0], $t['adultos_ninos_valor']) }}</span>
            </td>
        </tr>
        <tr>
            <td style="width: 50%; padding: 2mm 0;">
                <span style="color: #94a3b8; font-size: 7.5pt; text-transform: uppercase;">{{ $t['fecha_llegada'] }}</span><br>
                <span style="font-weight: bold;">{{ $formatFechaCorta($tour->fecha_llegada) }}{{ $tour->hora_llegada ? ' — ' . $tour->hora_llegada : '' }}</span>
            </td>
            <td style="width: 50%; padding: 2mm 0 2mm 6mm;">
                <span style="color: #94a3b8; font-size: 7.5pt; text-transform: uppercase;">{{ $t['duracion_viaje'] }}</span><br>
                <span style="font-weight: bold;">{{ $formatFecha($tour->fecha_inicio) }} &mdash; {{ $formatFecha($tour->fecha_fin) }}</span>
            </td>
        </tr>
    </table>

    @if($tour->passengers->isNotEmpty())
    <p class="section-title" style="color: {{ $brand['color1'] }};">{{ $t['pasajeros_grupo'] }}</p>
    <table style="width: 100%; margin-bottom: 8mm; font-size: 9pt;">
        <tr style="background-color: {{ $brand['color1'] }}; color: {{ $onColor1 }};">
            <td style="padding: 2.5mm 3mm; font-size: 8pt; text-transform: uppercase;">{{ $t['nombre'] }}</td>
            <td style="padding: 2.5mm 3mm; font-size: 8pt; text-transform: uppercase;">{{ $t['edad'] }}</td>
            <td style="padding: 2.5mm 3mm; font-size: 8pt; text-transform: uppercase;">{{ $t['correo'] }}</td>
        </tr>
        @foreach($tour->passengers as $i => $passenger)
        <tr style="background-color: {{ $i % 2 === 0 ? '#ffffff' : '#f8fafc' }};">
            <td style="padding: 2.5mm 3mm; border-bottom: 0.2mm solid #e2e8f0;">{{ $passenger->nombre }}</td>
            <td style="padding: 2.5mm 3mm; border-bottom: 0.2mm solid #e2e8f0;">{{ $passenger->edad ?? '—' }}</td>
            <td style="padding: 2.5mm 3mm; border-bottom: 0.2mm solid #e2e8f0;">{{ $passenger->correo ?: '—' }}</td>
        </tr>
        @endforeach
    </table>
    @endif

    @if($destinosRuta->isNotEmpty())
    <p class="section-title" style="color: {{ $brand['color1'] }};">{{ $t['ruta_viaje'] }}</p>
    <table style="width: 100%; font-size: 9.5pt;">
        <tr>
            @foreach($destinosRuta as $i => $destino)
            <td style="text-align: center; padding: 3mm 1mm;">
                <div style="width: 6mm; height: 6mm; margin: 0 auto 2mm auto; border-radius: 50%; background-color: {{ $brand['color2'] }};"></div>
                <span style="font-weight: bold;">{{ $destino }}</span>
            </td>
            @if(!$loop->last)
            <td style="width: 8mm; text-align: center; color: {{ $brand['color2'] }};">&rarr;</td>
            @endif
            @endforeach
        </tr>
    </table>
    @endif
</div>

{{-- ============================= DÍA A DÍA ============================= --}}
<div class="content-page page-break">
    <p class="eyebrow" style="color: {{ $brand['color2'] }}; margin: 0;">{{ $t['programa_viaje'] }}</p>
    <p class="section-title" style="color: {{ $brand['color1'] }}; margin-top: 1mm;">{{ $t['resumen_viaje'] }}</p>

    @if(collect($dias)->pluck('actividades')->flatten()->isNotEmpty())
    <table class="avoid-break" style="width: 100%; margin-bottom: 10mm; font-size: 9pt;">
        <tr style="background-color: {{ $brand['color1'] }}; color: {{ $onColor1 }};">
            <td style="padding: 2.5mm 3mm; font-size: 8pt; text-transform: uppercase; width: 16mm;">{{ $t['dia'] }}</td>
            <td style="padding: 2.5mm 3mm; font-size: 8pt; text-transform: uppercase; width: 30mm;">{{ $t['fecha'] }}</td>
            <td style="padding: 2.5mm 3mm; font-size: 8pt; text-transform: uppercase;">{{ $t['actividad'] }}</td>
        </tr>
        @php($fila = 0)
        @foreach($dias as $dia)
            @foreach($dia['actividades'] as $actividad)
            <tr style="background-color: {{ $fila % 2 === 0 ? '#ffffff' : '#f8fafc' }};">
                <td style="padding: 2mm 3mm; border-bottom: 0.2mm solid #e2e8f0; font-weight: bold;">{{ $dia['numero'] }}</td>
                <td style="padding: 2mm 3mm; border-bottom: 0.2mm solid #e2e8f0;">{{ $formatFechaCorta($dia['fecha']) }}</td>
                <td style="padding: 2mm 3mm; border-bottom: 0.2mm solid #e2e8f0;">{{ $actividad->nombre }}</td>
            </tr>
            @php($fila++)
            @endforeach
        @endforeach
    </table>
    @endif

    <p class="section-title" style="color: {{ $brand['color1'] }};">{{ $t['itinerario_dia_a_dia'] }}</p>

    @forelse($dias as $dia)
    <table class="avoid-break" style="width: 100%; margin-bottom: 7mm;">
        <tr>
            <td style="width: 15mm; vertical-align: top;">
                <table style="width: 14mm; height: 14mm; background-color: {{ $brand['color1'] }}; border-radius: 50%;">
                    <tr style="height: 14mm;"><td style="height: 14mm; text-align: center; vertical-align: middle; color: {{ $onColor1 }};">
                        <div style="font-size: 6.5pt; font-weight: bold; letter-spacing: 0.5px; text-transform: uppercase; line-height: 1.3;">{{ $t['dia'] }}</div>
                        <div style="font-size: 13pt; font-weight: bold; line-height: 1.1;">{{ $dia['numero'] }}</div>
                    </td></tr>
                </table>
            </td>
            <td style="padding-left: 4mm; border-left: 0.3mm solid #e2e8f0;">
                <p style="margin: 0 0 3mm 2mm; font-size: 10.5pt; font-weight: bold; color: #0f172a;">
                    {{ $formatDiaLargo($dia['fecha']) }}
                </p>

                @foreach($dia['actividades'] as $actividad)
                <div style="margin: 0 0 3mm 2mm; padding: 3mm 4mm; background-color: #f8fafc; border-left: 0.8mm solid {{ $brand['color2'] }}; border-radius: 1mm;">
                    <p style="margin: 0; font-weight: bold; font-size: 9.5pt; color: #0f172a;">{{ $actividad->nombre }}</p>
                    @if($actividad->categoria || $actividad->destino)
                        <p style="margin: 0.5mm 0 0 0; font-size: 8pt; color: #94a3b8;">
                            {{ implode(' · ', array_filter([$actividad->destino->nombre ?? null, $actividad->categoria->nombre ?? null])) }}
                        </p>
                    @endif
                    @if($actividad->descripcion)
                        <div class="rich-text" style="margin-top: 1.5mm;">{!! $normalizarListasQuill($actividad->descripcion) !!}</div>
                    @endif
                    @if($actividad->incluye || $actividad->no_incluye)
                    <table style="width: 100%; margin-top: 2mm;">
                        <tr>
                            @if($actividad->incluye)
                            <td style="width: {{ $actividad->no_incluye ? '50%' : '100%' }}; vertical-align: top; padding-right: 3mm;">
                                <p style="margin: 0 0 1mm 0; font-size: 7.5pt; font-weight: bold; text-transform: uppercase; color: #16a34a;">{{ $t['incluye'] }}</p>
                                <div class="rich-text">{!! $normalizarListasQuill($actividad->incluye) !!}</div>
                            </td>
                            @endif
                            @if($actividad->no_incluye)
                            <td style="width: {{ $actividad->incluye ? '50%' : '100%' }}; vertical-align: top; @if($actividad->incluye) padding-left: 3mm; border-left: 0.2mm solid #e2e8f0; @endif">
                                <p style="margin: 0 0 1mm 0; font-size: 7.5pt; font-weight: bold; text-transform: uppercase; color: #dc2626;">{{ $t['no_incluye'] }}</p>
                                <div class="rich-text">{!! $normalizarListasQuill($actividad->no_incluye) !!}</div>
                            </td>
                            @endif
                        </tr>
                    </table>
                    @endif
                </div>
                @endforeach

                @if($dia['hospedaje'])
                <div style="margin: 1mm 0 0 2mm; padding: 3mm 4mm; background-color: {{ $brand['color3'] }}; opacity: 0.9; border-radius: 1mm;">
                    <p style="margin: 0; font-size: 8.5pt; color: {{ $contrastFor($brand['color3']) }};">
                        <strong>{{ $t['alojamiento'] }}:</strong> {{ $dia['hospedaje']->hotel->nombre ?? $t['hotel_por_confirmar'] }}
                    </p>
                </div>
                @endif
            </td>
        </tr>
    </table>
    @empty
    <p style="color: #94a3b8; font-size: 9.5pt;">{{ $t['sin_actividades'] }}</p>
    @endforelse

    @if($sinFecha->isNotEmpty())
    <p class="section-title" style="color: {{ $brand['color1'] }}; margin-top: 4mm;">{{ $t['actividades_adicionales'] }}</p>
    @foreach($sinFecha as $actividad)
    <div class="avoid-break" style="margin: 0 0 3mm 0; padding: 3mm 4mm; background-color: #f8fafc; border-left: 0.8mm solid {{ $brand['color2'] }}; border-radius: 1mm;">
        <p style="margin: 0; font-weight: bold; font-size: 9.5pt; color: #0f172a;">{{ $actividad->nombre }}</p>
        @if($actividad->descripcion)
            <p style="margin: 1.5mm 0 0 0; font-size: 8.5pt; color: #475569;">{{ \Illuminate\Support\Str::limit(strip_tags($actividad->descripcion), 220) }}</p>
        @endif
    </div>
    @endforeach
    @endif
</div>

@if($terminosHtml)
{{-- ============================= TÉRMINOS Y CONDICIONES ============================= --}}
<div class="content-page page-break">
    <p class="terminos-titulo" style="color: {{ $brand['color1'] }}; border-color: {{ $brand['color1'] }};">{{ $t['terminos_titulo'] }}</p>
    <div class="terminos-cuerpo">{!! $normalizarListasQuill($terminosHtml) !!}</div>
</div>
@endif

{{-- ============================= CIERRE (última hoja) ============================= --}}
<div class="page-break bleed" style="position: relative; background-color: {{ $brand['color1'] }}; overflow: hidden;">

    {{-- Foto de fondo --}}
    <img src="{{ $cierreImgPath }}" style="position: absolute; {{ $coverImageStyle($cierreImgPath) }}">

    {{-- Logo --}}
    <div style="position: absolute; top: 40mm; left: 0; width: 210mm; text-align: center;">
        @if($logoFullPath)
            <img src="{{ $logoFullPath }}" style="display: inline-block; max-width: 60mm; max-height: 22mm; box-shadow: 0 2mm 5mm rgba(0,0,0,0.6);">
        @else
            <span style="display: inline-block; padding: 4mm 8mm; background-color: #ffffff; border-radius: 4mm; font-size: 13mm; font-weight: bold; color: {{ $brand['color1'] }}; box-shadow: 0 2mm 5mm rgba(0,0,0,0.6);">{{ mb_strtoupper(mb_substr($agencia->name ?? 'A', 0, 1)) }}</span>
        @endif
    </div>

    {{-- Panel inferior sólido: agradecimiento + contacto --}}
    <div style="position: absolute; bottom: 0; left: 0; width: 210mm; background-color: {{ $brand['color1'] }};">
      <div style="padding: 12mm 15mm 14mm 15mm; text-align: center;">
        <p style="margin: 0; color: {{ $onColor1 }}; font-size: 20pt; font-weight: bold;">{{ $t['gracias'] }}</p>
        <p style="margin: 3mm 0 0 0; color: {{ $onColor1 }}; font-size: 10pt;">{{ $t['acompaniar'] }}</p>

        <table style="width: 100%; margin-top: 9mm; color: {{ $onColor1 }}; font-size: 10pt;">
            <tr>
                @if($agencia?->telefono || $agencia?->whatsapp)
                <td style="text-align: center; padding: 0 4mm; border-left: 0.3mm solid {{ $onColor1 }};">
                    <p style="margin: 0; font-size: 7.5pt; text-transform: uppercase;">{{ $t['telefono_whatsapp'] }}</p>
                    <p style="margin: 1mm 0 0 0; font-weight: bold;">{{ $agencia->whatsapp ?: $agencia->telefono }}</p>
                </td>
                @endif
                @if($agencia?->email)
                <td style="text-align: center; padding: 0 4mm; border-left: 0.3mm solid {{ $onColor1 }};">
                    <p style="margin: 0; font-size: 7.5pt; text-transform: uppercase;">{{ $t['correo'] }}</p>
                    <p style="margin: 1mm 0 0 0; font-weight: bold;">{{ $agencia->email }}</p>
                </td>
                @endif
                @if($agencia?->ruc)
                <td style="text-align: center; padding: 0 4mm; border-left: 0.3mm solid {{ $onColor1 }}; border-right: 0.3mm solid {{ $onColor1 }};">
                    <p style="margin: 0; font-size: 7.5pt; text-transform: uppercase;">{{ $t['ruc'] }}</p>
                    <p style="margin: 1mm 0 0 0; font-weight: bold;">{{ $agencia->ruc }}</p>
                </td>
                @endif
            </tr>
        </table>
        @if($agencia?->direccion)
        <p style="margin: 6mm 0 0 0; color: {{ $onColor1 }}; font-size: 8.5pt;">{{ $agencia->direccion }}</p>
        @endif
      </div>
    </div>
</div>

</body>
</html>
