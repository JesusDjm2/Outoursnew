@php
    $tour = $tour ?? null;
    $paxFields = [
        ['name' => 'agente', 'label' => 'Agente', 'type' => 'text'],
        ['name' => 'nombre_pax', 'label' => 'Nombre Pax', 'type' => 'text'],
        ['name' => 'edad_pax', 'label' => 'Edad', 'type' => 'number', 'extra' => ['min' => 0, 'max' => 120]],
        ['name' => 'contacto_pax', 'label' => 'Contacto', 'type' => 'text'],
        ['name' => 'canal', 'label' => 'Canal de contacto', 'type' => 'text'],
        ['name' => 'fecha_cotizacion', 'label' => 'Fecha cotización', 'type' => 'date'],
        ['name' => 'pax_adultos', 'label' => 'Pax adultos', 'type' => 'number', 'extra' => ['min' => 0]],
        ['name' => 'pax_ninos', 'label' => 'Pax niños', 'type' => 'number', 'extra' => ['min' => 0]],
        ['name' => 'pais', 'label' => 'País', 'type' => 'text'],
        ['name' => 'codigo_pais', 'label' => 'Cód. país', 'type' => 'text'],
        ['name' => 'departamento_estado', 'label' => 'Depto/Estado', 'type' => 'text'],
        ['name' => 'fecha_llegada', 'label' => 'Fecha llegada', 'type' => 'date'],
        ['name' => 'hora_llegada', 'label' => 'Hora llegada', 'type' => 'time'],
        ['name' => 'fecha_salida_viaje', 'label' => 'Fecha salida', 'type' => 'date'],
        ['name' => 'hora_salida_viaje', 'label' => 'Hora salida', 'type' => 'time'],
    ];
@endphp
<div class="space-y-3">
    @foreach($paxFields as $field)
        @include('tours._pax_field', [
            'name' => $field['name'],
            'label' => $field['label'],
            'type' => $field['type'],
            'value' => old($field['name'], $tour?->{$field['name']}),
            'extra' => $field['extra'] ?? [],
        ])
    @endforeach
</div>
