@extends('layouts.app')
@section('title', 'Contabilidad')
@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-slate-100">Contabilidad</h1>
    <p class="text-sm text-gray-500 mt-0.5 dark:text-slate-400">Extracto de ingresos cotizados por mes y por cotización.</p>
</div>

{{-- Filtros --}}
<form method="GET" action="{{ route('contabilidad.index') }}" class="flex flex-wrap items-end gap-3 mb-6 bg-white rounded-xl shadow p-4 dark:bg-slate-900 dark:shadow-slate-950/50">
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Año</label>
        <select name="anio" onchange="this.form.submit()" class="text-sm border border-gray-300 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
            @foreach($aniosDisponibles as $a)
                <option value="{{ $a }}" {{ $anio == $a ? 'selected' : '' }}>{{ $a }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Mes</label>
        <select name="mes" onchange="this.form.submit()" class="text-sm border border-gray-300 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
            <option value="">Todo el año</option>
            @foreach($meses as $num => $nombre)
                <option value="{{ $num }}" {{ (int) $mes === $num ? 'selected' : '' }}>{{ $nombre }}</option>
            @endforeach
        </select>
    </div>
    @if($agencias->isNotEmpty())
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-slate-400">Agencia</label>
        <select name="agencia_id" onchange="this.form.submit()" class="text-sm border border-gray-300 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-blue-500 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
            <option value="">Todas las agencias</option>
            @foreach($agencias as $agencia)
                <option value="{{ $agencia->id }}" {{ (string) $agenciaId === (string) $agencia->id ? 'selected' : '' }}>{{ $agencia->name }}</option>
            @endforeach
        </select>
    </div>
    @endif
    @if($mes || $agenciaId)
    <a href="{{ route('contabilidad.index', ['anio' => $anio]) }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-slate-400 dark:hover:text-slate-200 mb-1.5">
        <i class="fas fa-filter-circle-xmark mr-1"></i> Limpiar filtros
    </a>
    @endif
</form>

{{-- Totales del período, por moneda --}}
<div class="grid grid-cols-1 sm:grid-cols-{{ max(1, count($totalesPorMoneda)) }} gap-3 mb-6">
    @forelse($totalesPorMoneda as $moneda => $total)
    <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900">
        <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-slate-400">Total cotizado ({{ $moneda }})</p>
        <p class="text-2xl font-bold text-gray-800 mt-1 dark:text-slate-100">
            {{ $moneda === 'PEN' ? 'S/ ' : '$ ' }}{{ number_format($total, 2) }}
        </p>
        <p class="text-xs text-gray-400 mt-0.5 dark:text-slate-500">{{ $tours->where('moneda', $moneda)->count() }} cotizaciones</p>
    </div>
    @empty
    <div class="rounded-xl border border-gray-200 bg-white p-6 text-center text-gray-500 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400">
        No hay cotizaciones registradas en este período.
    </div>
    @endforelse
</div>

{{-- Extracto mensual --}}
@if($porMes->isNotEmpty())
<div class="bg-white rounded-xl shadow overflow-hidden dark:bg-slate-900 dark:shadow-slate-950/50 mb-6">
    <div class="px-5 py-3.5 border-b border-gray-100 dark:border-slate-800">
        <h2 class="font-semibold text-gray-800 dark:text-slate-100">Extracto mensual {{ $anio }}</h2>
    </div>
    <table class="w-full">
        <thead class="bg-gray-50 dark:bg-slate-800">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Mes</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Moneda</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Cotizaciones</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Total cotizado</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Total en reservas (depósitos)</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
            @foreach($porMes as $fila)
            <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/60">
                <td class="px-6 py-3 text-sm text-gray-800 dark:text-slate-100">{{ $fila['mes_nombre'] }}</td>
                <td class="px-6 py-3 text-sm text-gray-600 dark:text-slate-400">{{ $fila['moneda'] }}</td>
                <td class="px-6 py-3 text-sm text-right text-gray-600 dark:text-slate-400">{{ $fila['cantidad'] }}</td>
                <td class="px-6 py-3 text-sm text-right font-medium text-gray-800 dark:text-slate-100">
                    {{ $fila['moneda'] === 'PEN' ? 'S/ ' : '$ ' }}{{ number_format($fila['total'], 2) }}
                </td>
                <td class="px-6 py-3 text-sm text-right text-gray-600 dark:text-slate-400">
                    {{ $fila['moneda'] === 'PEN' ? 'S/ ' : '$ ' }}{{ number_format($fila['reservas'], 2) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- Detalle por cotización --}}
<div class="bg-white rounded-xl shadow overflow-hidden dark:bg-slate-900 dark:shadow-slate-950/50">
    <div class="px-5 py-3.5 border-b border-gray-100 dark:border-slate-800">
        <h2 class="font-semibold text-gray-800 dark:text-slate-100">Detalle por cotización</h2>
    </div>
    <table class="w-full">
        <thead class="bg-gray-50 dark:bg-slate-800">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Fecha</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Código</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Pasajero</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Agencia</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Total cotizado</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase dark:text-slate-400">Reserva</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase dark:text-slate-400"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
            @forelse($tours as $item)
            <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/60">
                <td class="px-6 py-3 text-sm text-gray-600 dark:text-slate-400">{{ $item->fecha->format('d/m/Y') }}</td>
                <td class="px-6 py-3 text-sm font-mono text-gray-600 dark:text-slate-400">{{ $item->tour->codigo }}</td>
                <td class="px-6 py-3 text-sm text-gray-800 font-medium dark:text-slate-100">{{ $item->tour->nombre_pax ?: '—' }}</td>
                <td class="px-6 py-3 text-sm text-gray-600 dark:text-slate-400">{{ $item->tour->agencia->name ?? '—' }}</td>
                <td class="px-6 py-3 text-sm text-right font-medium text-gray-800 dark:text-slate-100">
                    {{ $item->moneda === 'PEN' ? 'S/ ' : '$ ' }}{{ number_format($item->pv_final, 2) }}
                </td>
                <td class="px-6 py-3 text-sm text-right text-gray-600 dark:text-slate-400">
                    {{ $item->moneda === 'PEN' ? 'S/ ' : '$ ' }}{{ number_format($item->monto_reserva, 2) }}
                </td>
                <td class="px-6 py-3 text-right text-sm">
                    <a href="{{ route('tours.show', $item->tour) }}" class="text-emerald-600 hover:text-emerald-800 dark:text-emerald-400 dark:hover:text-emerald-300" title="Ver cotización"><i class="fas fa-eye"></i></a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-10 text-center text-gray-500 dark:text-slate-400">
                    No hay cotizaciones para este período.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
