<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title ?? 'SobrePeso' }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-screen bg-mint font-sans text-slate-800 antialiased" x-data="{ sidebarOpen: false }">
        <div class="flex min-h-screen flex-col">
            <header class="relative z-30 flex h-14 shrink-0 items-center justify-between gap-4 bg-forest px-3 text-white sm:px-5">
                <div class="flex min-w-0 items-center gap-3">
                    <button
                        type="button"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-white/90 hover:bg-white/10 lg:hidden"
                        @click="sidebarOpen = !sidebarOpen"
                        aria-label="Abrir navegación"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16" />
                        </svg>
                    </button>
                    <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/15 text-sm font-bold">S</span>
                        <span class="text-base font-semibold tracking-tight">SobrePeso</span>
                    </a>
                    <span class="hidden h-5 w-px bg-white/20 sm:block"></span>
                    <span class="hidden truncate text-sm text-white/85 sm:inline">Presupuesto Personal</span>
                </div>

                <div class="flex items-center gap-2 sm:gap-4">
                    <div class="hidden text-right leading-tight md:block">
                        <p class="text-[10px] uppercase tracking-wide text-white/60">Plan activo</p>
                        <p class="text-xs font-medium">Presupuesto Personal</p>
                    </div>
                    <div class="hidden items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs text-white/85 lg:flex">
                        <span>Moneda: Bs (BOB)</span>
                    </div>
                    <div class="hidden items-center gap-1.5 rounded-full bg-emerald-400/20 px-2.5 py-1 text-xs font-medium text-emerald-100 sm:flex">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-300"></span>
                        Sincronizado
                    </div>

                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button
                            type="button"
                            @click="open = !open"
                            class="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-700 text-sm font-semibold ring-2 ring-white/20"
                            aria-label="Menú de usuario"
                        >
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </button>
                        <div
                            x-show="open"
                            x-cloak
                            x-transition.opacity
                            class="absolute right-0 mt-2 w-52 overflow-hidden rounded-xl border border-slate-200 bg-white py-1 text-slate-800 shadow-lg"
                        >
                            <div class="border-b border-slate-100 px-3 py-2">
                                <p class="truncate text-sm font-medium">{{ auth()->user()->name }}</p>
                                <p class="truncate text-xs text-slate-500">{{ auth()->user()->email }}</p>
                            </div>
                            <a href="{{ route('configuracion') }}" wire:navigate class="block px-3 py-2 text-sm hover:bg-mint">Configuración</a>
                            <livewire:logout-button />
                        </div>
                    </div>
                </div>
            </header>

            <div class="flex min-h-0 flex-1">
                <div
                    x-show="sidebarOpen"
                    x-cloak
                    @click="sidebarOpen = false"
                    class="fixed inset-0 z-20 bg-slate-900/40 lg:hidden"
                ></div>

                <aside
                    class="fixed inset-y-0 left-0 z-20 flex w-72 shrink-0 -translate-x-full flex-col border-r border-emerald-100/80 bg-white pt-14 transition-transform lg:static lg:!translate-x-0 lg:pt-0"
                    :class="sidebarOpen && '!translate-x-0'"
                >
                    <div class="flex-1 overflow-y-auto px-4 py-5">
                        <p class="px-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400">Navegación</p>
                        <nav class="mt-2 space-y-1">
                            <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-2.5 rounded-xl bg-mint px-3 py-2.5 text-sm font-semibold text-forest">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5 12 3l9 4.5M4.5 10v8.5A1.5 1.5 0 0 0 6 20h12a1.5 1.5 0 0 0 1.5-1.5V10" />
                                </svg>
                                Presupuesto Mensual
                            </a>
                            <span class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm text-slate-500">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 19V5m4 14V9m4 10V7m4 12v-6" />
                                </svg>
                                Informes / Reportes
                            </span>
                            <span class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm text-slate-500">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M5 6h14a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2Z" />
                                </svg>
                                Todas las Cuentas
                            </span>
                        </nav>

                        <p class="mt-6 px-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400">Cuentas</p>
                        <ul class="mt-2 space-y-1">
                            <li class="flex items-center justify-between rounded-xl px-3 py-2 text-sm">
                                <span class="flex items-center gap-2.5 text-slate-700">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-mint text-forest">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                        </svg>
                                    </span>
                                    Efectivo
                                </span>
                                <span class="text-xs font-medium text-slate-600">Bs 1.250,00</span>
                            </li>
                            <li class="flex items-center justify-between rounded-xl px-3 py-2 text-sm">
                                <span class="flex items-center gap-2.5 text-slate-700">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-mint text-forest">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M5 6h14a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2Z" />
                                        </svg>
                                    </span>
                                    Banco Sol
                                </span>
                                <span class="text-xs font-medium text-slate-600">Bs 8.400,00</span>
                            </li>
                            <li class="flex items-center justify-between rounded-xl px-3 py-2 text-sm">
                                <span class="flex items-center gap-2.5 text-slate-700">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-mint text-forest">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M5 6h14a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2Z" />
                                        </svg>
                                    </span>
                                    Banco Ganadero
                                </span>
                                <span class="text-xs font-medium text-slate-600">Bs 5.200,00</span>
                            </li>
                        </ul>
                        <button type="button" class="mt-2 flex w-full items-center gap-2 rounded-xl px-3 py-2 text-sm text-forest hover:bg-mint">
                            <span class="text-lg leading-none">+</span>
                            Agregar cuenta
                        </button>
                    </div>

                    <div class="border-t border-emerald-100 px-5 py-4">
                        <p class="text-[11px] uppercase tracking-wider text-slate-400">Total en Cuentas</p>
                        <p class="mt-1 text-lg font-semibold text-forest-dark">Bs 14.850,00</p>
                    </div>
                </aside>

                <main class="min-w-0 flex-1 overflow-y-auto">
                    {{ $slot }}
                </main>
            </div>
        </div>
        @livewireScripts
    </body>
</html>
