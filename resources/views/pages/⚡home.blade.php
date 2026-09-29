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
                <div id="mes-controles" class="flex items-center gap-2"></div>
            </div>
            <button type="button" class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-forest shadow-sm ring-1 ring-emerald-100">Hoy</button>
            <p class="text-xs text-slate-500">Moneda del plan: Bolivianos (Bs.)</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" class="rounded-xl bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm ring-1 ring-emerald-100">Auto-asignar</button>
            <button type="button" wire:click="$dispatchTo('plan-sobres', 'open-category')" class="rounded-xl bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm ring-1 ring-emerald-100">+ Nueva categoría</button>
            <button type="button" wire:click="$dispatchTo('plan-sobres', 'open-sobre')" class="rounded-xl bg-forest px-3 py-2 text-sm font-semibold text-white shadow-sm">+ Nuevo sobre</button>
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

    <livewire:plan-sobres />
</div>
