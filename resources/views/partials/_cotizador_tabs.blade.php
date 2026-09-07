@php
    $cotizadorActive = $cotizadorActive ?? null;
    $cotizadorTabs = [
        'registros' => ['route' => 'tours.index', 'label' => 'Registro de Cotizaciones', 'icon' => 'fa-file-invoice-dollar'],
        'paquetes' => ['route' => 'itinerary-packages.index', 'label' => 'Paquetes de Actividades', 'icon' => 'fa-layer-group'],
        'destinos' => ['route' => 'destinos.index', 'label' => 'Destinos y Actividades', 'icon' => 'fa-route'],
    ];
@endphp
<div class="mb-3 overflow-x-auto">
    <div class="inline-flex gap-1 rounded-2xl bg-white p-1.5 shadow-sm border border-gray-200 dark:bg-slate-900 dark:border-slate-800">
        @foreach($cotizadorTabs as $key => $tab)
            <a href="{{ route($tab['route']) }}"
               class="flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-medium whitespace-nowrap transition
                      {{ $cotizadorActive === $key
                          ? 'bg-cyan-600 text-white shadow'
                          : 'text-gray-500 hover:text-gray-800 hover:bg-gray-100 dark:text-slate-400 dark:hover:text-slate-100 dark:hover:bg-slate-800' }}">
                <i class="fas {{ $tab['icon'] }} text-xs"></i>
                <span>{{ $tab['label'] }}</span>
            </a>
        @endforeach
    </div>
</div>
