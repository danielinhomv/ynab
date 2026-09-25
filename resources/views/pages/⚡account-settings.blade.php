<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Configuración')] class extends Component
{
    public string $name = '';

    public string $email = '';

    public function mount(): void
    {
        $user = Auth::user();

        $this->name = $user->name;
        $this->email = $user->email;
    }

    public function save(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ]);

        $user->update($validated);
    }
};
?>

<div class="px-4 py-6 sm:px-6 lg:px-8">
    <form wire:submit="save" class="mx-auto w-full max-w-lg rounded-2xl bg-white p-6 shadow-sm ring-1 ring-emerald-100 sm:p-8">
        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-forest">Cuenta de usuario</p>
        <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">Configuración de cuenta de usuario</h1>
        <p class="mt-2 text-sm text-slate-500">Actualiza el nombre y el correo con los que entras a tu plan.</p>

        <div class="mt-6 space-y-4">
            <div>
                <label for="name" class="mb-1.5 block text-sm font-medium text-slate-700">Nombre</label>
                <input id="name" type="text" wire:model="name" autocomplete="name" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm outline-none ring-forest/20 transition focus:border-forest focus:bg-white focus:ring-4">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="mb-1.5 block text-sm font-medium text-slate-700">Correo electrónico</label>
                <input id="email" type="email" wire:model="email" autocomplete="email" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm outline-none ring-forest/20 transition focus:border-forest focus:bg-white focus:ring-4">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <button type="submit" class="mt-6 rounded-2xl bg-forest px-4 py-2.5 text-sm font-semibold text-white hover:bg-forest-dark">
            Guardar
        </button>
    </form>
</div>
