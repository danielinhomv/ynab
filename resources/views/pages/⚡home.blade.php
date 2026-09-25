<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Presupuesto Mensual')] class extends Component
{
    //
};
?>

<div class="px-4 py-5 sm:px-6 lg:px-8">
    <div class="mb-5 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2 text-slate-800">
                <svg class="h-5 w-5 text-forest" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M5 11h14M6 5h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z" />
                </svg>
                <h1 class="text-xl font-semibold tracking-tight sm:text-2xl">Septiembre 2026</h1>
            </div>
            <button type="button" class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-forest shadow-sm ring-1 ring-emerald-100">Hoy</button>
            <p class="text-xs text-slate-500">Moneda del plan: Bolivianos (Bs.)</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" class="rounded-xl bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm ring-1 ring-emerald-100">Auto-asignar</button>
            <button type="button" class="rounded-xl bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm ring-1 ring-emerald-100">+ Nueva categoría</button>
            <button type="button" class="rounded-xl bg-forest px-3 py-2 text-sm font-semibold text-white shadow-sm">+ Nuevo sobre</button>
        </div>
    </div>

    <div class="grid gap-3 lg:grid-cols-[minmax(0,1.4fr)_repeat(3,minmax(0,1fr))]">
        <article class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-emerald-100">
            <div class="flex items-center justify-between gap-3">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-leaf">Listo para asignar</p>
                <span class="rounded-full bg-mint px-2.5 py-0.5 text-[11px] font-medium text-forest">Dinero sin trabajo</span>
            </div>
            <div class="mt-3 flex items-end gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-mint text-forest">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 8.5c1.5 0 3 1.2 3 3.2 0 3.3-3.4 6.3-7 8.3-3.6-2-7-5-7-8.3 0-2 1.5-3.2 3-3.2 1.2 0 2.2.6 3 1.5.8-.9 1.8-1.5 3-1.5Z" />
                    </svg>
                </span>
                <div>
                    <p class="text-3xl font-semibold tracking-tight text-slate-900">Bs 1.250,00</p>
                    <p class="text-sm font-medium text-forest">Dinero por asignar</p>
                </div>
            </div>
            <p class="mt-3 text-xs leading-relaxed text-slate-500">
                Presupuesto Base Cero. Asigna este saldo en tus sobres hasta llegar a Bs 0,00.
            </p>
        </article>
        <article class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-emerald-100">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Ingresos del Mes</p>
            <p class="mt-3 text-xl font-semibold text-slate-900">Bs 9.800,00</p>
        </article>
        <article class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-emerald-100">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Presupuestado</p>
            <p class="mt-3 text-xl font-semibold text-slate-900">Bs 8.550,00</p>
            <p class="mt-1 text-[11px] text-slate-400">3 depósitos recibidos · 12 sobres activos</p>
        </article>
        <article class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-emerald-100">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Gastado hasta hoy</p>
            <p class="mt-3 text-xl font-semibold text-slate-900">Bs 4.120,50</p>
            <p class="mt-1 text-[11px] text-slate-400">48,2% del presupuesto</p>
        </article>
    </div>

    <div class="mt-4 flex flex-col gap-3 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-800 sm:flex-row sm:items-center sm:justify-between">
        <p>
            <span class="font-semibold">Atención:</span>
            Tienes 1 sobre sobregirado (Supermercado -Bs 150,00). Recomendamos cubrirlo inmediatamente con fondos de otro sobre.
        </p>
        <button type="button" class="shrink-0 rounded-xl bg-white px-3 py-1.5 text-sm font-semibold text-red-700 shadow-sm ring-1 ring-red-100">
            Resolver ahora
        </button>
    </div>

    <section class="relative mt-5 overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-emerald-100">
        <div class="grid grid-cols-[minmax(0,2fr)_repeat(3,minmax(0,1fr))] gap-2 border-b border-slate-100 px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 sm:px-5">
            <p>Categoría / Sobre</p>
            <p class="text-right">Asignado</p>
            <p class="text-right">Actividad</p>
            <p class="text-right">Disponible</p>
        </div>

        <div class="border-b border-slate-100 bg-mint/50 px-4 py-2.5 sm:px-5">
            <div class="grid grid-cols-[minmax(0,2fr)_repeat(3,minmax(0,1fr))] items-center gap-2">
                <div>
                    <p class="text-sm font-semibold text-slate-800">Servicios básicos</p>
                    <p class="text-[11px] text-slate-400">3 sobres</p>
                </div>
                <p class="text-right text-sm text-slate-600">Bs 630,00</p>
                <p class="text-right text-sm text-slate-600">Bs 585,00</p>
                <p class="text-right text-sm font-medium text-leaf">Bs 45,00</p>
            </div>
        </div>
        <div class="divide-y divide-slate-50">
            <div class="grid grid-cols-[minmax(0,2fr)_repeat(3,minmax(0,1fr))] items-center gap-2 px-4 py-3 sm:px-5">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-50 text-amber-600">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 3 6 14h6l-1 7 7-11h-6l1-7Z" />
                        </svg>
                    </span>
                    <div>
                        <p class="text-sm font-medium text-slate-800">Luz (CRE / Delapaz)</p>
                        <p class="text-[11px] text-slate-400">Factura mensual fija</p>
                    </div>
                </div>
                <p class="text-right text-sm text-slate-600">Bs 280,00</p>
                <p class="text-right text-sm text-slate-600">Bs 240,00</p>
                <p class="text-right text-sm font-medium text-leaf">Bs 40,00</p>
            </div>
            <div class="grid grid-cols-[minmax(0,2fr)_repeat(3,minmax(0,1fr))] items-center gap-2 px-4 py-3 sm:px-5">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-sky-50 text-sky-600">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c4 4 6 7.2 6 10.2A6 6 0 1 1 6 13.2C6 10.2 8 7 12 3Z" />
                        </svg>
                    </span>
                    <div>
                        <p class="text-sm font-medium text-slate-800">Agua Potable</p>
                        <p class="text-[11px] text-slate-400">Pago por medidor</p>
                    </div>
                </div>
                <p class="text-right text-sm text-slate-600">Bs 120,00</p>
                <p class="text-right text-sm text-slate-600">Bs 115,00</p>
                <p class="text-right text-sm font-medium text-leaf">Bs 5,00</p>
            </div>
            <div class="grid grid-cols-[minmax(0,2fr)_repeat(3,minmax(0,1fr))] items-center gap-2 px-4 py-3 sm:px-5">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-violet-50 text-violet-600">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12.5c2.2-2.4 4.6-3.6 7-3.6s4.8 1.2 7 3.6M8.2 15.2A6.4 6.4 0 0 1 12 14c1.4 0 2.7.4 3.8 1.2M12 19h.01" />
                        </svg>
                    </span>
                    <div>
                        <p class="text-sm font-medium text-slate-800">Internet Fibra</p>
                        <p class="text-[11px] text-slate-400">Plan 100 Mbps</p>
                    </div>
                </div>
                <p class="text-right text-sm text-slate-600">Bs 230,00</p>
                <p class="text-right text-sm text-slate-600">Bs 230,00</p>
                <p class="text-right text-sm font-medium text-slate-400">Bs 0,00</p>
            </div>
        </div>

        <div class="border-y border-slate-100 bg-mint/50 px-4 py-2.5 sm:px-5">
            <div class="grid grid-cols-[minmax(0,2fr)_repeat(3,minmax(0,1fr))] items-center gap-2">
                <div>
                    <p class="text-sm font-semibold text-slate-800">Alimentación</p>
                    <p class="text-[11px] text-slate-400">2 sobres</p>
                </div>
                <p class="text-right text-sm text-slate-600">Bs 2.400,00</p>
                <p class="text-right text-sm text-slate-600">Bs 2.330,00</p>
                <p class="text-right text-sm font-medium text-leaf">Bs 70,00</p>
            </div>
        </div>
        <div class="relative divide-y divide-slate-50">
            <div class="grid grid-cols-[minmax(0,2fr)_repeat(3,minmax(0,1fr))] items-center gap-2 bg-red-50/70 px-4 py-3 sm:px-5">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-red-100 text-red-600">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16l-1.2 9.2a2 2 0 0 1-2 1.8H7.2a2 2 0 0 1-2-1.8L4 6Zm4 13h.01M16 19h.01M8 6 9.2 3h5.6L16 6" />
                        </svg>
                    </span>
                    <div>
                        <p class="text-sm font-medium text-slate-800">Supermercado <span class="ml-1 rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-semibold uppercase text-red-700">Sobregiro</span></p>
                        <p class="text-[11px] text-slate-400">Escudos por compras de fin de semana</p>
                    </div>
                </div>
                <p class="text-right text-sm text-slate-600">Bs 1.800,00</p>
                <p class="text-right text-sm text-slate-600">Bs 1.950,00</p>
                <p class="text-right text-sm font-semibold text-red-600">-Bs 150,00</p>
            </div>
            <div class="pointer-events-none absolute right-4 top-16 z-10 hidden w-72 rounded-2xl border border-red-100 bg-white p-4 shadow-xl sm:block">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-slate-900">Cubrir sobregiro</p>
                        <p class="mt-0.5 text-xs text-slate-500">Sobre: Supermercado</p>
                    </div>
                    <span class="text-slate-300">×</span>
                </div>
                <p class="mt-3 text-xs leading-relaxed text-slate-500">
                    Este sobre ha gastado más de lo asignado. Mueve fondos de otro sobre y tu dinero listo.
                </p>
            </div>
            <div class="grid grid-cols-[minmax(0,2fr)_repeat(3,minmax(0,1fr))] items-center gap-2 px-4 py-3 sm:px-5">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-orange-50 text-orange-600">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 10h16v7a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-7Zm2-4h12l2 4H4l2-4Z" />
                        </svg>
                    </span>
                    <div>
                        <p class="text-sm font-medium text-slate-800">Restaurantes y Salidas</p>
                        <p class="text-[11px] text-slate-400">Almuerzos laborales y cenas</p>
                    </div>
                </div>
                <p class="text-right text-sm text-slate-600">Bs 600,00</p>
                <p class="text-right text-sm text-slate-600">Bs 380,00</p>
                <p class="text-right text-sm font-medium text-leaf">Bs 220,00</p>
            </div>
        </div>

        <div class="border-y border-slate-100 bg-mint/50 px-4 py-2.5 sm:px-5">
            <div class="grid grid-cols-[minmax(0,2fr)_repeat(3,minmax(0,1fr))] items-center gap-2">
                <div>
                    <p class="text-sm font-semibold text-slate-800">Transporte</p>
                    <p class="text-[11px] text-slate-400">2 sobres</p>
                </div>
                <p class="text-right text-sm text-slate-600">Bs 450,00</p>
                <p class="text-right text-sm text-slate-600">Bs 380,00</p>
                <p class="text-right text-sm font-medium text-leaf">Bs 70,00</p>
            </div>
        </div>
        <div class="divide-y divide-slate-50">
            <div class="grid grid-cols-[minmax(0,2fr)_repeat(3,minmax(0,1fr))] items-center gap-2 px-4 py-3 sm:px-5">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-50 text-emerald-700">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 15h16l-1.5-6H8L4 15Zm3 0v3m10-3v3M8 9V6h5" />
                        </svg>
                    </span>
                    <div>
                        <p class="text-sm font-medium text-slate-800">Transporte público</p>
                        <p class="text-[11px] text-slate-400">Pasajes diarios</p>
                    </div>
                </div>
                <p class="text-right text-sm text-slate-600">Bs 200,00</p>
                <p class="text-right text-sm text-slate-600">Bs 180,00</p>
                <p class="text-right text-sm font-medium text-leaf">Bs 20,00</p>
            </div>
            <div class="grid grid-cols-[minmax(0,2fr)_repeat(3,minmax(0,1fr))] items-center gap-2 px-4 py-3 sm:px-5">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-lime-50 text-lime-700">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 15.5c0 1.4 1.3 2.5 3 2.5h2c1.7 0 3-1.1 3-2.5V8H8v7.5ZM10 8V5h6l2 3" />
                        </svg>
                    </span>
                    <div>
                        <p class="text-sm font-medium text-slate-800">Combustible</p>
                        <p class="text-[11px] text-slate-400">Movilidad propia</p>
                    </div>
                </div>
                <p class="text-right text-sm text-slate-600">Bs 250,00</p>
                <p class="text-right text-sm text-slate-600">Bs 200,00</p>
                <p class="text-right text-sm font-medium text-leaf">Bs 50,00</p>
            </div>
        </div>
    </section>
</div>
