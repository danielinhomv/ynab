<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title ?? 'SobrePeso' }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-screen bg-mint font-sans text-slate-800 antialiased">
        <div class="flex min-h-screen flex-col">
            <header class="flex items-center justify-between px-5 py-4 sm:px-8 lg:px-12">
                <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-2.5">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-forest text-sm font-bold text-white">S</span>
                    <span class="text-lg font-semibold tracking-tight text-forest-dark">SobrePeso</span>
                </a>
                <div class="flex items-center gap-5 text-xs font-medium text-slate-400 sm:text-sm">
                    <span>EN</span>
                    <span>Help &amp; Support</span>
                </div>
            </header>

            <div class="mx-auto grid w-full max-w-6xl flex-1 items-start gap-8 px-5 pb-10 sm:px-8 lg:grid-cols-2 lg:gap-12 lg:px-12 lg:pt-4">
                <section class="pt-2">
                    <div class="mb-5 hidden flex-wrap items-center gap-3 text-[11px] font-semibold tracking-wide lg:flex">
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/80 px-3 py-1 text-forest shadow-sm">
                            <span class="h-1.5 w-1.5 rounded-full bg-forest"></span>
                            MÉTODO DE PRESUPUESTO EN SOBRES
                        </span>
                        <span class="rounded-full bg-amber-100 px-3 py-1 text-amber-800">100% Cero Basado</span>
                    </div>

                    <h1 class="max-w-lg text-3xl font-bold leading-[1.15] text-slate-900 sm:text-4xl xl:text-5xl">
                        Dale un trabajo a cada <span class="text-forest">peso</span>.
                    </h1>
                    <p class="mt-4 max-w-md text-sm leading-relaxed text-slate-600">
                        Asigna tus ingresos en sobres virtuales antes de gastar. Recupera el control total de tus finanzas personales sin culpa ni estrés.
                    </p>

                    <div class="relative mx-auto mt-10 hidden w-full max-w-md lg:block">
                        <div class="absolute -left-3 top-10 flex h-9 w-9 items-center justify-center rounded-full bg-amber-400 text-lg font-bold text-white shadow-lg">$</div>
                        <div class="absolute -right-1 top-3 flex h-8 w-8 items-center justify-center rounded-full bg-emerald-500 text-sm font-bold text-white shadow-lg">$</div>
                        <article class="rounded-[1.75rem] bg-linear-to-br from-forest-dark via-forest to-emerald-800 p-6 text-white shadow-xl">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="text-xs tracking-wide text-emerald-100/90">Sobre Activo</p>
                                    <p class="mt-8 text-[11px] uppercase tracking-wider text-emerald-200/90">Ahorro emergencias</p>
                                    <p class="mt-1 text-3xl font-semibold tracking-tight">$15,400.00</p>
                                    <p class="mt-1 text-xs text-emerald-200/80">Meta: $15,400</p>
                                </div>
                                <span class="rounded-full bg-white/15 px-3 py-1 text-[11px] font-medium">100% financiado</span>
                            </div>
                            <div class="mt-10 flex items-end justify-between text-xs text-emerald-100/80">
                                <span>Asignación segura</span>
                                <span>Octubre 2024</span>
                            </div>
                        </article>
                    </div>

                    <div class="mt-8 hidden grid-cols-3 gap-3 lg:grid">
                        <article class="rounded-2xl bg-white p-4 shadow-sm">
                            <p class="text-sm font-semibold text-slate-900">Sobres ilimitados</p>
                            <p class="mt-2 text-xs leading-relaxed text-slate-500">Divide cada centavo para tus metas y gastos fijos con libertad.</p>
                        </article>
                        <article class="rounded-2xl bg-white p-4 shadow-sm">
                            <p class="text-sm font-semibold text-slate-900">Cero deudas</p>
                            <p class="mt-2 text-xs leading-relaxed text-slate-500">Ahorro inteligente y programado contra imprevistos del día a día.</p>
                        </article>
                        <article class="rounded-2xl bg-white p-4 shadow-sm">
                            <p class="text-sm font-semibold text-slate-900">Cifrado militar</p>
                            <p class="mt-2 text-xs leading-relaxed text-slate-500">Máxima privacidad y seguridad bancaria de 256 bits garantizada.</p>
                        </article>
                    </div>

                    <blockquote class="mt-6 hidden rounded-2xl bg-white/70 px-4 py-3 text-sm leading-relaxed text-slate-600 lg:block">
                        “SobrePeso cambió por completo mi forma de ver el dinero a fin de mes. Adiós a la ansiedad.”
                        <footer class="mt-1 text-xs text-slate-400">Sofía Morales, usuaria desde 2023</footer>
                    </blockquote>
                </section>

                <section class="flex justify-center pb-6 lg:justify-end lg:pt-2">
                    {{ $slot }}
                </section>
            </div>

            <footer class="mt-auto flex items-center justify-between gap-4 px-5 py-4 text-[11px] text-slate-400 sm:px-8 lg:px-12">
                <span>© {{ date('Y') }} SobrePeso. Every dollar assigned with calm confidence.</span>
                <span class="hidden gap-4 sm:flex">
                    <span>Privacy Policy</span>
                    <span>Terms of Service</span>
                    <span>Security</span>
                </span>
            </footer>
        </div>
        @livewireScripts
    </body>
</html>
