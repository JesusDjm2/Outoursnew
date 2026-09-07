<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Tours · Cotizaciones, itinerarios y tours vendidos</title>
    <link rel="icon" type="image/png" href="{{ asset('img/favicon-outoors.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    @include('partials._tailwind')
</head>
<body class="min-h-full bg-slate-950 text-slate-100">
    <div class="relative overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(232,12,19,0.22),_transparent_35%),radial-gradient(circle_at_bottom_right,_rgba(232,12,19,0.12),_transparent_30%)]"></div>
        <div class="relative mx-auto flex min-h-screen max-w-7xl flex-col px-4 py-6 sm:px-6 lg:px-8">
            <main class="grid flex-1 items-center gap-8 lg:grid-cols-[1.05fr_0.95fr]">
                <section class="gsap-fade space-y-6">
                    <div class="inline-flex items-center gap-2 rounded-full border border-brand/30 bg-brand/10 px-3 py-1 text-sm text-red-200">
                        <i class="fa-solid fa-bolt"></i>
                        Panel pensado para equipos ágiles
                    </div>
                    <div class="space-y-4">
                        <h1 class="max-w-2xl text-4xl font-semibold leading-tight sm:text-5xl">Cotiza, genera itinerarios en PDF y gestiona tus tours vendidos.</h1>
                        <p class="max-w-xl text-lg text-slate-300">Admin Tours es tu sistema de cotización y creación de itinerarios en PDF, con control completo de la gestión de tours vendidos, todo desde un panel claro y rápido.</p>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-3">
                        <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                            <i class="fa-solid fa-file-invoice-dollar mb-2 text-brand"></i>
                            <p class="text-sm text-slate-300">Cotizaciones</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                            <i class="fa-solid fa-file-pdf mb-2 text-brand"></i>
                            <p class="text-sm text-slate-300">Actividades en PDF</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                            <i class="fa-solid fa-route mb-2 text-brand"></i>
                            <p class="text-sm text-slate-300">Tours vendidos</p>
                        </div>
                    </div>
                </section>

                <section id="login" class="gsap-fade rounded-[2rem] border border-white/10 bg-slate-900/70 p-4 shadow-2xl shadow-black/40 backdrop-blur sm:p-8">
                    @auth
                        <div class="flex h-full flex-col items-center justify-center gap-4 rounded-[1.5rem] border border-white/10 bg-gradient-to-br from-slate-800 to-slate-950 p-8 text-center">
                            <div class="rounded-2xl bg-white px-4 py-3">
                                <img src="{{ asset('img/logoout.png') }}" alt="Admin Tours" class="h-12 w-auto sm:h-16">
                            </div>
                            <h2 class="text-2xl font-semibold">Ya tienes una sesión activa</h2>
                            <p class="text-sm text-slate-400">Continúa hacia tu panel de administración.</p>
                            <a href="{{ route('dashboard') }}" class="rounded-2xl bg-brand px-6 py-3 font-semibold text-white transition hover:bg-brand-dark hover:scale-[1.01]">Ir al panel</a>
                        </div>
                    @else
                        <div class="rounded-[1.5rem] border border-white/10 bg-gradient-to-br from-slate-800 to-slate-950 p-6 sm:p-8">
                            <div class="text-center">
                                <div class="mx-auto inline-block rounded-2xl bg-white px-4 py-3">
                                    <img src="{{ asset('img/logoout.png') }}" alt="Admin Tours" class="h-12 w-auto sm:h-16">
                                </div>
                                <h2 class="mt-4 text-2xl font-semibold">Iniciar sesión</h2>
                                <p class="mt-2 text-sm text-slate-400">Ingresa tus credenciales para continuar</p>
                            </div>

                            @if($errors->any())
                                <div class="mt-6 rounded-2xl border border-rose-400/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
                                    {{ $errors->first() }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
                                @csrf
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-slate-200">Correo electrónico</label>
                                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-2xl border border-white/10 bg-slate-800 px-4 py-3 text-sm outline-none transition focus:border-brand focus:ring-2 focus:ring-brand/20">
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-slate-200">Contraseña</label>
                                    <div class="relative">
                                        <input type="password" name="password" id="password" required class="w-full rounded-2xl border border-white/10 bg-slate-800 px-4 py-3 pr-11 text-sm outline-none transition focus:border-brand focus:ring-2 focus:ring-brand/20">
                                        <button type="button" id="togglePassword" tabindex="-1" class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-slate-200">
                                            <i class="fa-solid fa-eye" id="togglePasswordIcon"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" name="remember" id="remember" class="h-4 w-4 rounded border-white/20 bg-slate-800 text-brand focus:ring-2 focus:ring-brand/20">
                                    <label for="remember" class="text-sm text-slate-300">Recordar mis datos de ingreso</label>
                                </div>
                                <button type="submit" class="w-full rounded-2xl bg-brand px-4 py-3 font-semibold text-white transition hover:bg-brand-dark hover:scale-[1.01]">
                                    Ingresar al administrador
                                </button>
                            </form>
                        </div>
                    @endauth
                </section>
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            gsap.from('.gsap-fade', { opacity: 0, y: 28, duration: 0.8, stagger: 0.16, ease: 'power3.out' });

            const toggle = document.getElementById('togglePassword');
            toggle?.addEventListener('click', () => {
                const input = document.getElementById('password');
                const icon = document.getElementById('togglePasswordIcon');
                const isHidden = input.type === 'password';
                input.type = isHidden ? 'text' : 'password';
                icon.classList.toggle('fa-eye', !isHidden);
                icon.classList.toggle('fa-eye-slash', isHidden);
            });
        });
    </script>
</body>
</html>
