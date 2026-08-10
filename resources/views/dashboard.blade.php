@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="space-y-6">
    <div class="rounded-3xl border border-slate-200 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-700 p-6 text-white shadow-xl">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm uppercase tracking-[0.3em] text-cyan-300">Panel principal</p>
                <h1 class="mt-2 text-3xl font-semibold">Bienvenido, {{ $user->name }}</h1>
                <p class="mt-2 max-w-2xl text-sm text-slate-300">Aquí tienes una vista rápida de tus operaciones: tours, itinerarios, hoteles, habitaciones, pasajeros y usuarios, con un acceso más claro a cada módulo.</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/10 px-4 py-3 backdrop-blur">
                <p class="text-sm text-slate-300">Rol activo</p>
                <p class="font-semibold">{{ $user->getRoleNames()->first() }}</p>
            </div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @php($cards = collect([
            ['icon' => 'fa-map-marked-alt', 'color' => 'from-cyan-500 to-blue-500', 'value' => $stats['tours'], 'label' => 'Tours', 'route' => 'tours.index'],
            ['icon' => 'fa-route', 'color' => 'from-emerald-500 to-green-500', 'value' => $stats['itinerarios'], 'label' => 'Itinerarios', 'route' => 'itineraries.index'],
            ['icon' => 'fa-hotel', 'color' => 'from-violet-500 to-purple-500', 'value' => $stats['hoteles'], 'label' => 'Hoteles', 'route' => 'hotels.index'],
            ['icon' => 'fa-bed', 'color' => 'from-amber-500 to-orange-500', 'value' => $stats['habitaciones'], 'label' => 'Habitaciones', 'route' => 'hotels.index'],
            ['icon' => 'fa-user-friends', 'color' => 'from-pink-500 to-rose-500', 'value' => $stats['pasajeros'], 'label' => 'Pasajeros', 'route' => 'passengers.index'],
            ['icon' => 'fa-users', 'color' => 'from-slate-500 to-slate-700', 'value' => $stats['usuarios'], 'label' => 'Usuarios', 'route' => 'users.index'],
        ])->filter(fn($card) => $card['value'] !== null))
        @foreach($cards as $card)
            <a href="{{ route($card['route']) }}" class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-slate-700 dark:bg-slate-900">
                <div class="flex items-center justify-between">
                    <div class="rounded-2xl bg-gradient-to-br {{ $card['color'] }} p-3 text-white">
                        <i class="fa-solid {{ $card['icon'] }}"></i>
                    </div>
                    <span class="text-3xl font-semibold text-slate-800 dark:text-slate-100">{{ $card['value'] }}</span>
                </div>
                <p class="mt-4 text-sm font-medium text-slate-500 dark:text-slate-400">{{ $card['label'] }}</p>
            </a>
        @endforeach
    </div>

    <div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">Tours recientes</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Accede rápidamente a los paquetes más recientes.</p>
                </div>
                <a href="{{ route('tours.index') }}" class="text-sm font-semibold text-cyan-600">Ver todos</a>
            </div>
            <div class="space-y-3">
                @forelse($recentTours as $tour)
                    <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-800">
                        <div>
                            <p class="font-medium text-slate-800 dark:text-slate-100">{{ $tour->nombre }}</p>
                            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $tour->codigo }}</p>
                        </div>
                        <a href="{{ route('tours.index') }}" class="rounded-full bg-cyan-500/10 px-3 py-1 text-sm text-cyan-600">Abrir</a>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 dark:text-slate-400">No hay tours recientes.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <div class="mb-4">
                <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">Pasajeros recientes</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Seguimiento rápido de los registros más recientes.</p>
            </div>
            <div class="space-y-3">
                @forelse($recentPassengers as $passenger)
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-800">
                        <p class="font-medium text-slate-800 dark:text-slate-100">{{ $passenger->nombre }}</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $passenger->correo ?? 'Sin correo' }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 dark:text-slate-400">No hay pasajeros recientes.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
