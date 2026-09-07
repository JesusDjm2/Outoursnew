<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>{{ $tour->codigo }}</title>
@php
    $contrastFor = function ($hex) {
        $hex = ltrim($hex ?? '', '#');
        if (strlen($hex) === 3) { $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2]; }
        if (strlen($hex) !== 6 || !ctype_xdigit($hex)) { return '#ffffff'; }
        $lum = (0.2126 * hexdec(substr($hex, 0, 2)) + 0.7152 * hexdec(substr($hex, 2, 2)) + 0.0722 * hexdec(substr($hex, 4, 2))) / 255;
        return $lum > 0.6 ? '#0f172a' : '#ffffff';
    };
    $headerText = $contrastFor($brand['color1']);

    $logoFullPath = $agencia?->logo_path && file_exists(public_path('storage/' . $agencia->logo_path))
        ? public_path('storage/' . $agencia->logo_path)
        : null;

    $labels = [
        'es' => [
            'titulo' => 'E-COTIZACIÓN', 'id' => 'ID', 'fecha_cot' => 'Fecha de cotización', 'agente' => 'Agente de reservas',
            'reserva' => 'Reserva tu paquete con el :pct% (:monto)',
            'llegada' => 'Llegada', 'salida' => 'Salida', 'sin_definir' => 'Por definir',
            'fecha' => 'Fecha', 'tour_actividad' => 'Tour / Actividad', 'cant' => 'Cant.', 'p_reg' => 'P. Reg.', 'p_promo' => 'P. Promo', 'total_linea' => 'Total línea', 'ninos_abrev' => 'niños',
            'check_in' => 'Check in', 'check_out' => 'Check out', 'alojamiento' => 'Alojamiento', 'n_hab' => 'N° hab.', 'n_noches' => 'N° noches', 'p_reg_noche' => 'P.reg x noche', 'p_promo_noche' => 'P.promo x noche',
            'cantidad_base' => 'Cantidad base total', 'descuento_total' => 'Descuento Total', 'precio_adicional' => 'Precio adicional', 'total_final' => 'Total final',
            'terminos_titulo' => 'Términos y Condiciones', 'notas_titulo' => 'Notas Adicionales',
            'seccion_tours' => 'Tours y Actividades', 'seccion_hoteles' => 'Hospedajes',
        ],
        'en' => [
            'titulo' => 'E-QUOTATION', 'id' => 'ID', 'fecha_cot' => 'Quotation date', 'agente' => 'Booking agent',
            'reserva' => 'Reserve your package with :pct% (:monto)',
            'llegada' => 'Arrival', 'salida' => 'Departure', 'sin_definir' => 'To be defined',
            'fecha' => 'Date', 'tour_actividad' => 'Tour / Activity', 'cant' => 'Qty.', 'p_reg' => 'Reg. price', 'p_promo' => 'Promo price', 'total_linea' => 'Line total', 'ninos_abrev' => 'children',
            'check_in' => 'Check in', 'check_out' => 'Check out', 'alojamiento' => 'Accommodation', 'n_hab' => 'Rooms', 'n_noches' => 'Nights', 'p_reg_noche' => 'Reg. price/night', 'p_promo_noche' => 'Promo price/night',
            'cantidad_base' => 'Base total amount', 'descuento_total' => 'Total discount', 'precio_adicional' => 'Additional price', 'total_final' => 'Final total',
            'terminos_titulo' => 'Terms and Conditions', 'notas_titulo' => 'Additional Notes',
            'seccion_tours' => 'Tours and Activities', 'seccion_hoteles' => 'Accommodation',
        ],
        'pt' => [
            'titulo' => 'E-COTAÇÃO', 'id' => 'ID', 'fecha_cot' => 'Data da cotação', 'agente' => 'Agente de reservas',
            'reserva' => 'Reserve seu pacote com :pct% (:monto)',
            'llegada' => 'Chegada', 'salida' => 'Saída', 'sin_definir' => 'A definir',
            'fecha' => 'Data', 'tour_actividad' => 'Tour / Atividade', 'cant' => 'Qtd.', 'p_reg' => 'Preço reg.', 'p_promo' => 'Preço promo', 'total_linea' => 'Total da linha', 'ninos_abrev' => 'crianças',
            'check_in' => 'Check in', 'check_out' => 'Check out', 'alojamiento' => 'Hospedagem', 'n_hab' => 'N° quartos', 'n_noches' => 'N° noites', 'p_reg_noche' => 'Preço reg./noite', 'p_promo_noche' => 'Preço promo/noite',
            'cantidad_base' => 'Valor base total', 'descuento_total' => 'Desconto total', 'precio_adicional' => 'Preço adicional', 'total_final' => 'Total final',
            'terminos_titulo' => 'Termos e Condições', 'notas_titulo' => 'Notas Adicionais',
            'seccion_tours' => 'Tours e Atividades', 'seccion_hoteles' => 'Hospedagem',
        ],
    ];
    $localeMap = ['espanol' => 'es', 'ingles' => 'en', 'portugues' => 'pt'];
    $t = $labels[$localeMap[$tour->idioma] ?? 'es'];

    $monedaSimbolo = $tour->moneda === 'PEN' ? 'S/ ' : '$ ';
    $formatMoney = fn ($valor) => $monedaSimbolo . number_format((float) $valor, 2);

    $formatFechaLarga = fn ($fecha) => $fecha ? \Illuminate\Support\Carbon::parse($fecha)->translatedFormat('d M Y') : $t['sin_definir'];

    $reservaTexto = str_replace(
        [':pct', ':monto'],
        [(int) ($tour->reserva_pct ?? 0), $formatMoney($resumen['monto_reserva'])],
        $t['reserva']
    );

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
    $notasAdicionales = $abrirEnlacesEnNuevaPestana($tour->notas_adicionales);
    $terminosHtml = $abrirEnlacesEnNuevaPestana($terminos);

    $filasHotel = [];
    foreach ($tour->hospedajes as $hospedaje) {
        if (!$hospedaje->fecha_ingreso || !$hospedaje->fecha_salida) {
            continue;
        }
        $noches = max(1, \Illuminate\Support\Carbon::parse($hospedaje->fecha_ingreso)->diffInDays(\Illuminate\Support\Carbon::parse($hospedaje->fecha_salida)));
        foreach ($hospedaje->rooms as $room) {
            $pReg = (float) ($room->precio_regular ?? 0);
            $pPromo = (float) ($room->precio_promo ?? $room->precio_regular ?? 0);
            $filasHotel[] = [
                'check_in' => $hospedaje->fecha_ingreso,
                'check_out' => $hospedaje->fecha_salida,
                'alojamiento' => trim($hospedaje->hotel->nombre . ' — ' . $room->nombre),
                'noches' => $noches,
                'p_reg' => $pReg,
                'p_promo' => $pPromo,
                'total' => $pPromo * $noches,
            ];
        }
    }
@endphp
<style>
    @page {
        margin: 34px 34px 34px 34px;
    }

    * { box-sizing: border-box; }
    body {
        font-family: 'DejaVu Sans', sans-serif;
        color: #1e293b;
        font-size: 11px;
        line-height: 1.3;
    }

    table { border-collapse: collapse; width: 100%; }

    /* ---- Header ---- */
    .header-table td { vertical-align: top; }
    .agencia-logo { width: 110px; border-radius: 6px; }
    .agencia-nombre { font-size: 15px; font-weight: bold; color: #0f172a; margin: 10px 0 2px 0; line-height: 1.2; }
    .agencia-datos { font-size: 10px; color: #475569; line-height: 1.25; }
    .cotizacion-meta { text-align: right; }
    .cotizacion-titulo { font-size: 20px; font-weight: bold; color: {{ $brand['color1'] }}; letter-spacing: 1px; line-height: 1.2; }
    .cotizacion-meta p { margin: 2px 0 0 0; font-size: 10px; color: #475569; line-height: 1.25; }

    hr.divider { border: none; border-top: 1px solid #e2e8f0; margin: 14px 0; }

    /* ---- Cliente ---- */
    .cliente-nombre { font-size: 15px; font-weight: bold; color: {{ $brand['color2'] }}; margin-bottom: 2px; }
    .cliente-contacto { font-size: 11px; color: #475569; margin-bottom: 6px; }
    .reserva-aviso { font-size: 11px; font-weight: bold; color: {{ $brand['color3'] }}; }

    /* ---- Fechas box ---- */
    .fechas-box { width: 100%; margin: 14px 0; background: #f1f5f9; border-radius: 8px; padding: 12px 16px; }
    .fechas-box td { width: 50%; vertical-align: top; }
    .fecha-label { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #64748b; margin-bottom: 2px; }
    .fecha-valor { font-size: 13px; font-weight: bold; color: #0f172a; }

    /* ---- Items table ---- */
    table.items { width: 100%; table-layout: fixed; margin-top: 16px; }
    table.items.items-hoteles { margin-top: 10px; }
    table.items thead td, table.items thead th {
        background: {{ $brand['color1'] }};
        color: {{ $headerText }};
        font-size: 9px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 7px 8px;
        text-align: left;
    }
    table.items .section-label td {
        background: #ffffff;
        padding: 12px 8px 5px 8px;
        font-size: 10px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: {{ $brand['color1'] }};
        border-bottom: 2px solid {{ $brand['color1'] }};
    }
    table.items thead tr.section-label td { padding-top: 0; }
    table.items tbody td {
        border-bottom: 1px solid #e2e8f0;
        padding: 6px 8px;
        font-size: 10px;
        color: #1e293b;
    }
    table.items tbody tr.hotel-row td { background: #f5f8ff; }
    table.items td.num { text-align: right; }

    /* ---- Footer ---- */
    .footer-table { margin-top: 18px; }
    .footer-table td { vertical-align: top; }
    .notas-col { width: 55%; padding-right: 20px; }
    .notas-titulo { font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; color: {{ $brand['color1'] }}; margin-bottom: 8px; }
    .notas-box { border-left: 3px solid {{ $brand['color1'] }}; background: #f8fafc; border-radius: 0 8px 8px 0; padding: 10px 14px; font-size: 10px; color: #475569; }
    .totales-col { width: 45%; }
    .totales-box { background: #f1f5f9; border-radius: 8px; padding: 14px 18px; }
    .totales-box table td { padding: 4px 0; font-size: 11px; }
    .totales-box .valor { text-align: right; }
    .totales-box .descuento { color: #dc2626; border-bottom: 1px solid #dc2626; padding-bottom: 6px; }
    .totales-box .final-row td { padding-top: 10px; font-size: 14px; font-weight: bold; color: {{ $brand['color1'] }}; }

    /* ---- Terms page ---- */
    .terminos-page { page-break-before: always; }
    .terminos-titulo { font-size: 18px; font-weight: bold; color: {{ $brand['color1'] }}; border-bottom: 2px solid {{ $brand['color1'] }}; padding-bottom: 8px; margin-bottom: 16px; }
    .terminos-cuerpo { font-size: 10.5px; color: #334155; line-height: 1.5; }
    .terminos-cuerpo p, .notas-col p { margin: 0 0 8px 0; }
    .terminos-cuerpo ul, .terminos-cuerpo ol, .notas-col ul, .notas-col ol { margin: 0 0 8px 0; padding-left: 18px; }
    .terminos-cuerpo a, .notas-col a { color: {{ $brand['color1'] }}; text-decoration: underline; }
</style>
</head>
<body>

<table class="header-table">
    <tr>
        <td style="width: 50%; vertical-align: middle;">
            @if($logoFullPath)
                <img src="{{ $logoFullPath }}" class="agencia-logo">
            @endif
        </td>
        <td style="width: 50%; vertical-align: middle;" class="cotizacion-meta">
            <div class="cotizacion-titulo">{{ $t['titulo'] }}</div>
            <p>{{ $t['id'] }}: {{ $tour->codigo }}</p>
            <p>{{ $t['fecha_cot'] }}: {{ $formatFechaLarga($tour->fecha_cotizacion) }}</p>
            <p>{{ $t['agente'] }}: {{ $tour->agente ?: '—' }}</p>
        </td>
    </tr>
</table>

<div class="agencia-nombre">{{ $agencia?->name }}</div>
<div class="agencia-datos">
    @if($agencia?->ruc) RUC {{ $agencia->ruc }}<br>@endif
    @if($agencia?->direccion) {{ $agencia->direccion }}<br>@endif
    @if($agencia?->telefono) Tel. {{ $agencia->telefono }}<br>@endif
    @if($agencia?->celulares) {{ $agencia->celulares }}<br>@endif
    @if($agencia?->whatsapp) WhatsApp {{ $agencia->whatsapp }}@endif
</div>

<hr class="divider">

<div class="cliente-nombre">{{ $tour->nombre_pax ?: '—' }}</div>
@if($tour->contacto_pax)
<div class="cliente-contacto">{{ $tour->contacto_pax }}</div>
@endif
<div class="reserva-aviso">{{ $reservaTexto }}</div>

<table class="fechas-box">
    <tr>
        <td>
            <div class="fecha-label">{{ $t['llegada'] }}</div>
            <div class="fecha-valor">{{ $formatFechaLarga($tour->fecha_llegada) }}</div>
        </td>
        <td>
            <div class="fecha-label">{{ $t['salida'] }}</div>
            <div class="fecha-valor">{{ $formatFechaLarga($tour->fecha_salida_viaje) }}</div>
        </td>
    </tr>
</table>

<table class="items">
    <colgroup>
        <col style="width: 14%;">
        <col style="width: 18%;">
        <col style="width: 18%;">
        <col style="width: 10%;">
        <col style="width: 13%;">
        <col style="width: 13%;">
        <col style="width: 14%;">
    </colgroup>
    <thead>
        <tr class="section-label">
            <td colspan="7">{{ $t['seccion_tours'] }}</td>
        </tr>
        <tr>
            <td>{{ $t['fecha'] }}</td>
            <td colspan="2">{{ $t['tour_actividad'] }}</td>
            <td>{{ $t['cant'] }}</td>
            <td>{{ $t['p_reg'] }}</td>
            <td>{{ $t['p_promo'] }}</td>
            <td>{{ $t['total_linea'] }}</td>
        </tr>
    </thead>
    <tbody>
        @foreach($tour->itineraries as $itinerario)
        @php
            $cant = (float) ($itinerario->pivot->cantidad_pax ?? 0);
            $cantNinos = (float) ($itinerario->pivot->cantidad_pax_ninos ?? 0);
            $pReg = (float) ($itinerario->costo ?? 0);
            $pPromo = (float) ($itinerario->costo_promo ?? $itinerario->costo ?? 0);
            $pPromoNino = (float) ($itinerario->costo_promo_nino ?? $itinerario->costo_nino ?? 0);
        @endphp
        <tr>
            <td>{{ $itinerario->pivot->fecha ? \Illuminate\Support\Carbon::parse($itinerario->pivot->fecha)->format('d/m/Y') : '' }}</td>
            <td colspan="2">{{ $itinerario->nombre }}</td>
            <td>{{ $itinerario->pivot->cantidad_pax }}{{ $cantNinos > 0 ? ' + ' . $cantNinos . ' ' . $t['ninos_abrev'] : '' }}</td>
            <td class="num">{{ $formatMoney($pReg) }}</td>
            <td class="num">{{ $formatMoney($pPromo) }}</td>
            <td class="num">{{ $formatMoney(($pPromo * $cant) + ($pPromoNino * $cantNinos)) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

@if(!empty($filasHotel))
<table class="items items-hoteles">
    <colgroup>
        <col style="width: 12%;">
        <col style="width: 12%;">
        <col style="width: 24%;">
        <col style="width: 10%;">
        <col style="width: 10%;">
        <col style="width: 11%;">
        <col style="width: 11%;">
        <col style="width: 10%;">
    </colgroup>
    <thead>
        <tr class="section-label">
            <td colspan="8">{{ $t['seccion_hoteles'] }}</td>
        </tr>
        <tr>
            <td>{{ $t['check_in'] }}</td>
            <td>{{ $t['check_out'] }}</td>
            <td>{{ $t['alojamiento'] }}</td>
            <td>{{ $t['n_hab'] }}</td>
            <td>{{ $t['n_noches'] }}</td>
            <td>{{ $t['p_reg_noche'] }}</td>
            <td>{{ $t['p_promo_noche'] }}</td>
            <td>{{ $t['total_linea'] }}</td>
        </tr>
    </thead>
    <tbody>
        @foreach($filasHotel as $fila)
        <tr class="hotel-row">
            <td>{{ \Illuminate\Support\Carbon::parse($fila['check_in'])->format('d/m/Y') }}</td>
            <td>{{ \Illuminate\Support\Carbon::parse($fila['check_out'])->format('d/m/Y') }}</td>
            <td>{{ $fila['alojamiento'] }}</td>
            <td>1</td>
            <td>{{ $fila['noches'] }}</td>
            <td class="num">{{ $formatMoney($fila['p_reg']) }}</td>
            <td class="num">{{ $formatMoney($fila['p_promo']) }}</td>
            <td class="num">{{ $formatMoney($fila['total']) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<table class="footer-table">
    <tr>
        <td class="notas-col">
            @if($notasAdicionales)
                <div class="notas-titulo">{{ $t['notas_titulo'] }}</div>
                <div class="notas-box">{!! $notasAdicionales !!}</div>
            @endif
        </td>
        <td class="totales-col">
            <div class="totales-box">
                <table>
                    <tr>
                        <td>{{ $t['cantidad_base'] }}</td>
                        <td class="valor">{{ $formatMoney($resumen['pv_regular']) }}</td>
                    </tr>
                    <tr>
                        <td class="descuento">(-) {{ $t['descuento_total'] }}</td>
                        <td class="valor descuento">{{ $formatMoney($resumen['total_descuento'] + (float) ($tour->descuento_especial ?? 0)) }}</td>
                    </tr>
                    <tr>
                        <td>{{ $t['precio_adicional'] }}</td>
                        <td class="valor">{{ $formatMoney($tour->precio_adicional ?? 0) }}</td>
                    </tr>
                    <tr class="final-row">
                        <td>{{ $t['total_final'] }}</td>
                        <td class="valor">{{ $formatMoney($resumen['pv_final']) }}</td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
</table>

@if($terminosHtml)
<div class="terminos-page">
    <div class="terminos-titulo">{{ $t['terminos_titulo'] }}</div>
    <div class="terminos-cuerpo">{!! $terminosHtml !!}</div>
</div>
@endif

</body>
</html>
