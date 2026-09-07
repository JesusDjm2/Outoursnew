<!DOCTYPE html>
<html lang="es" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Tours')</title>
    <link rel="icon" type="image/png" href="{{ asset('img/favicon-outoors.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('partials._tailwind')
    @stack('styles')
    <style>
        :root {
            --dash-color: #de0007;
            --dash-color-dark: #991519;
        }

        aside.bg-slate-900 {
            background-color: var(--dash-color) !important;
        }

        .dark aside.bg-slate-900 {
            background-color: #212e42 !important;
        }

        aside a.bg-cyan-500\/15 {
            background-color: rgba(255, 255, 255, 0.2) !important;
            color: #ffffff !important;
        }

        aside p {
            color: #ffffff !important;
            letter-spacing: .1em !important;
        }

        button[type="submit"].bg-blue-600,
        a.bg-blue-600 {
            background-color: var(--dash-color) !important;
        }

        button[type="submit"].bg-blue-600:hover,
        a.bg-blue-600:hover {
            background-color: var(--dash-color-dark) !important;
        }
    </style>
    <script>
        const savedTheme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ?
            'dark' : 'light');
        document.documentElement.classList.toggle('dark', savedTheme === 'dark');
        document.documentElement.style.colorScheme = savedTheme;
    </script>
</head>

<body
    class="min-h-screen bg-slate-100 text-slate-800 transition-colors duration-300 dark:bg-slate-950 dark:text-slate-100">
    <div class="flex min-h-screen flex-col lg:flex-row">
        <aside
            class="w-full border-b border-slate-200 bg-slate-900 text-white lg:sticky lg:top-0 lg:h-screen lg:w-72 lg:border-b-0 lg:border-r lg:border-slate-800">
            <div class="flex items-center border-b border-black/20 px-5 py-5">
                <div class="rounded-2xl bg-white px-3 py-2">
                    <img src="{{ asset('img/logoout.png') }}" alt="Admin Tours" class="h-9 w-auto">
                </div>
            </div>
            <nav class="flex-1 space-y-1 overflow-y-auto p-4">
                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 rounded-2xl px-3 py-2.5 transition {{ request()->routeIs('dashboard') ? 'bg-cyan-500/15 text-cyan-300' : 'text-white/70 hover:bg-black/20 hover:text-white' }}">
                    <i class="fas fa-tachometer-alt w-5"></i> Dashboard
                </a>
                @if (auth()->user()->hasAnyRole(['Super Administrador', 'Administrador']))
                    <p class="mt-4 px-3 text-[11px] font-semibold uppercase tracking-[0.3em] text-slate-500">
                        Administración</p>
                    <a href="{{ route('users.index') }}"
                        class="flex items-center gap-3 rounded-2xl px-3 py-2.5 transition {{ request()->routeIs('users.*') ? 'bg-cyan-500/15 text-cyan-300' : 'text-white/70 hover:bg-black/20 hover:text-white' }}">
                        <i class="fas fa-users-cog w-5"></i> Usuarios
                    </a>
                    <a href="{{ route('agencias.index') }}"
                        class="flex items-center gap-3 rounded-2xl px-3 py-2.5 transition {{ request()->routeIs('agencias.*') ? 'bg-cyan-500/15 text-cyan-300' : 'text-white/70 hover:bg-black/20 hover:text-white' }}">
                        <i class="fas fa-building w-5"></i> Agencias
                    </a>
                @endif
                @if (auth()->user()->hasAnyRole(['Agencia', 'Administrador', 'Super Administrador']))
                    <p class="mt-4 px-3 text-[11px] font-semibold uppercase tracking-[0.3em] text-slate-500">Mi cuenta
                    </p>
                    <a href="{{ route('agencia.profile.edit') }}"
                        class="flex items-center gap-3 rounded-2xl px-3 py-2.5 transition {{ request()->routeIs('agencia.profile.*') ? 'bg-cyan-500/15 text-cyan-300' : 'text-white/70 hover:bg-black/20 hover:text-white' }}">
                        <i class="fas fa-building-user w-5"></i> Mi Agencia
                    </a>
                @endif
                <p class="mt-4 px-3 text-[11px] font-semibold uppercase tracking-[0.3em] text-slate-500">Gestión</p>
                <a href="{{ route('tours.index') }}"
                    class="flex items-center gap-3 rounded-2xl px-3 py-2.5 transition {{ request()->routeIs('tours.*', 'destinos.*', 'categorias.*', 'itineraries.*', 'itinerary-packages.*') ? 'bg-cyan-500/15 text-cyan-300' : 'text-white/70 hover:bg-black/20 hover:text-white' }}">
                    <i class="fas fa-map-marked-alt w-5"></i> Cotizador
                </a>
                <a href="{{ route('hotels.index') }}"
                    class="flex items-center gap-3 rounded-2xl px-3 py-2.5 transition {{ request()->routeIs('hotels.*', 'rooms.*') ? 'bg-cyan-500/15 text-cyan-300' : 'text-white/70 hover:bg-black/20 hover:text-white' }}">
                    <i class="fas fa-hotel w-5"></i> Hoteles
                </a>
                <a href="{{ route('proveedores.index') }}"
                    class="flex items-center gap-3 rounded-2xl px-3 py-2.5 transition {{ request()->routeIs('proveedores.*') ? 'bg-cyan-500/15 text-cyan-300' : 'text-white/70 hover:bg-black/20 hover:text-white' }}">
                    <i class="fas fa-truck-fast w-5"></i> Proveedores
                </a>
                @if (auth()->user()->hasAnyRole(['Reservas', 'Contabilidad', 'Administrador', 'Super Administrador']))
                    <p class="mt-4 px-3 text-[11px] font-semibold uppercase tracking-[0.3em] text-slate-500">Operaciones</p>
                @endif
                @if (auth()->user()->hasAnyRole(['Reservas', 'Administrador', 'Super Administrador']))
                    <a href="{{ route('reservas.index') }}"
                        class="flex items-center gap-3 rounded-2xl px-3 py-2.5 transition {{ request()->routeIs('reservas.*') ? 'bg-cyan-500/15 text-cyan-300' : 'text-white/70 hover:bg-black/20 hover:text-white' }}">
                        <i class="fas fa-clipboard-check w-5"></i> Reservas
                    </a>
                @endif
                @if (auth()->user()->hasAnyRole(['Contabilidad', 'Administrador', 'Super Administrador']))
                    <a href="{{ route('contabilidad.index') }}"
                        class="flex items-center gap-3 rounded-2xl px-3 py-2.5 transition {{ request()->routeIs('contabilidad.*') ? 'bg-cyan-500/15 text-cyan-300' : 'text-white/70 hover:bg-black/20 hover:text-white' }}">
                        <i class="fas fa-sack-dollar w-5"></i> Contabilidad
                    </a>
                @endif
            </nav>
            <div class="border-t border-black/20 p-4">
                <div class="rounded-2xl border border-black/20 bg-black/20 p-3">
                    <p class="text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-white/60">{{ auth()->user()->getRoleNames()->first() }}</p>
                </div>
            </div>
        </aside>
        <main class="flex-1 overflow-y-auto">
            <div
                class="sticky top-0 z-40 flex items-center justify-end gap-2 border-b border-slate-200 bg-slate-100/90 px-4 py-3 backdrop-blur sm:px-6 lg:px-8 dark:border-slate-800 dark:bg-slate-950/90">
                <button id="themeToggle" type="button"
                    class="flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                    <i id="themeIcon" class="fa-solid fa-moon"></i>
                </button>
                <form method="POST" action="{{ route('logout') }}"
                    onsubmit="return swalConfirmSubmit(event, '¿Seguro que deseas cerrar sesión?', { title: '¿Cerrar sesión?', icon: 'question', confirmText: 'Sí, salir' });">
                    @csrf
                    <button type="submit" title="Cerrar sesión"
                        class="flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-red-600 shadow-sm transition hover:bg-red-50 dark:border-slate-700 dark:bg-slate-800 dark:text-red-400 dark:hover:bg-red-950/40">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
            <div class="p-4 sm:p-6 lg:p-8">
                @if (session('success'))
                    <div
                        class="mb-4 flex items-start justify-between gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-950/40 dark:text-emerald-300">
                        <span>{{ session('success') }}</span>
                        <button type="button" onclick="this.closest('div.mb-4').remove()"
                            class="shrink-0 text-emerald-500 hover:text-emerald-700 dark:text-emerald-300 dark:hover:text-emerald-100" title="Cerrar">
                            <i class="fas fa-xmark"></i>
                        </button>
                    </div>
                @endif
                @if (session('error'))
                    <div
                        class="mb-4 flex items-start justify-between gap-3 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-900/50 dark:bg-rose-950/40 dark:text-rose-300">
                        <span>{{ session('error') }}</span>
                        <button type="button" onclick="this.closest('div.mb-4').remove()"
                            class="shrink-0 text-rose-500 hover:text-rose-700 dark:text-rose-300 dark:hover:text-rose-100" title="Cerrar">
                            <i class="fas fa-xmark"></i>
                        </button>
                    </div>
                @endif
                @if ($errors->any())
                    <div
                        class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-900/50 dark:bg-rose-950/40 dark:text-rose-300">
                        <p class="font-semibold mb-1">Revisa los siguientes errores:</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </div>
        </main>
    </div>
    <script>
        function swalConfirmSubmit(event, message, options = {}) {
            event.preventDefault();
            const form = event.target;
            const isDark = document.documentElement.classList.contains('dark');
            Swal.fire({
                title: options.title || '¿Estás seguro?',
                text: message,
                icon: options.icon || 'warning',
                showCancelButton: true,
                confirmButtonText: options.confirmText || 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#e80c13',
                cancelButtonColor: '#64748b',
                background: isDark ? '#1e293b' : '#ffffff',
                color: isDark ? '#f1f5f9' : '#0f172a',
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
            return false;
        }

        document.addEventListener('DOMContentLoaded', () => {
            const toggle = document.getElementById('themeToggle');
            const icon = document.getElementById('themeIcon');
            const applyTheme = (theme) => {
                document.documentElement.classList.toggle('dark', theme === 'dark');
                document.documentElement.style.colorScheme = theme;
                localStorage.setItem('theme', theme);
                if (icon) {
                    icon.className = theme === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
                }
            };
            const current = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
            applyTheme(current);
            toggle?.addEventListener('click', () => {
                const next = document.documentElement.classList.contains('dark') ? 'light' : 'dark';
                applyTheme(next);
            });

            gsap.from('.gsap-fade', {
                opacity: 0,
                y: 18,
                duration: 0.7,
                stagger: 0.08,
                ease: 'power3.out'
            });
        });
    </script>
    @stack('scripts')
</body>

</html>
