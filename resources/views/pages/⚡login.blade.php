<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.guest')] #[Title('Iniciar sesión')] class extends Component
{
    public string $email = '';

    public string $password = '';

    public function login(): void
    {
        $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            $this->addError('email', 'Estas credenciales no coinciden.');

            return;
        }

        session()->regenerate();

        $this->redirect(route('home'), navigate: true);
    }
};
?>

<div class="w-full max-w-md">
    <form wire:submit="login" class="rounded-[1.75rem] bg-white p-6 shadow-xl shadow-emerald-900/5 sm:p-8">
        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-forest">Acceso</p>
        <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">Iniciar sesión</h1>
        <p class="mt-2 text-sm leading-relaxed text-slate-500">
            Entra para ver tu plan y tus sobres.
        </p>

        <div class="mt-6 space-y-4">
            <div>
                <label for="email" class="mb-1.5 block text-sm font-medium text-slate-700">Correo electrónico</label>
                <input
                    id="email"
                    type="email"
                    wire:model="email"
                    autocomplete="email"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm outline-none ring-forest/20 transition focus:border-forest focus:bg-white focus:ring-4"
                >
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div x-data="{ show: false }">
                <label for="password" class="mb-1.5 block text-sm font-medium text-slate-700">Contraseña</label>
                <div class="relative">
                    <input
                        id="password"
                        :type="show ? 'text' : 'password'"
                        wire:model="password"
                        autocomplete="current-password"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 pr-10 text-sm outline-none ring-forest/20 transition focus:border-forest focus:bg-white focus:ring-4"
                    >
                    <button type="button" class="absolute inset-y-0 right-3 text-slate-400" @click="show = !show" aria-label="Mostrar contraseña">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <button type="submit" class="mt-6 flex w-full items-center justify-center gap-2 rounded-2xl bg-forest px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-forest-dark">
            Iniciar sesión
            <span aria-hidden="true">→</span>
        </button>

        <p class="mt-5 text-center text-sm text-slate-500">
            ¿No tienes cuenta?
            <a href="{{ route('home') }}" wire:navigate class="font-semibold text-forest hover:underline">Crear cuenta</a>
        </p>
    </form>
</div>
