<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.guest')] #[Title('Crear cuenta')] class extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed'],
        ], [
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'email.unique' => 'Este correo ya está registrado.',
        ]);

        $user = User::query()->create($validated);

        Auth::login($user);

        $this->redirect(route('home'), navigate: true);
    }
};
?>

<div
    class="w-full max-w-md"
    x-data="{
        name: '',
        password: '',
        passwordConfirmation: '',
        showPassword: false,
        showConfirmation: false,
    }"
>
    <form wire:submit="register" class="rounded-[1.75rem] bg-white p-6 shadow-xl shadow-emerald-900/5 sm:p-8">
        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-forest">Registro rápido</p>
        <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">Comienza tu viaje financiero</h1>
        <p class="mt-2 text-sm leading-relaxed text-slate-500">
            Crea tu cuenta gratis en menos de 2 minutos. Sin tarjetas de crédito requeridas.
        </p>

        <div class="mt-6 space-y-4">
            <div>
                <div class="mb-1.5 flex items-center justify-between">
                    <label for="name" class="block text-sm font-medium text-slate-700">Nombre completo</label>
                    <span class="text-xs font-medium text-leaf" x-show="name.trim().length >= 2" x-cloak>Válido</span>
                </div>
                <input
                    id="name"
                    type="text"
                    wire:model="name"
                    x-model="name"
                    autocomplete="name"
                    placeholder="Carlos Mendoza"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm outline-none ring-forest/20 transition focus:border-forest focus:bg-white focus:ring-4"
                >
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="mb-1.5 block text-sm font-medium text-slate-700">Correo electrónico</label>
                <input
                    id="email"
                    type="email"
                    wire:model="email"
                    autocomplete="email"
                    placeholder="carlos@ejemplo.com"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm outline-none ring-forest/20 transition focus:border-forest focus:bg-white focus:ring-4"
                >
                <p class="mt-1.5 text-xs text-slate-400">Te enviaremos la confirmación de tu primer sobre virtual.</p>
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <div class="mb-1.5 flex items-center justify-between">
                    <label for="password" class="block text-sm font-medium text-slate-700">Contraseña</label>
                    <span class="text-xs font-medium text-leaf" x-show="password.length >= 8" x-cloak>Segura</span>
                </div>
                <div class="relative">
                    <input
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        wire:model="password"
                        x-model="password"
                        autocomplete="new-password"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 pr-10 text-sm outline-none ring-forest/20 transition focus:border-forest focus:bg-white focus:ring-4"
                    >
                    <button type="button" class="absolute inset-y-0 right-3 text-slate-400" @click="showPassword = !showPassword" aria-label="Mostrar contraseña">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                    </button>
                </div>
                <ul class="mt-2 space-y-1 text-xs">
                    <li :class="password.length >= 8 ? 'text-leaf' : 'text-slate-400'">Más de 8 caracteres</li>
                    <li :class="/\d/.test(password) ? 'text-leaf' : 'text-slate-400'">Un número</li>
                    <li :class="/[A-Z]|[^A-Za-z0-9]/.test(password) ? 'text-leaf' : 'text-slate-400'">Mayúscula/Símbolo</li>
                </ul>
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <div class="mb-1.5 flex items-center justify-between">
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Confirmar contraseña</label>
                    <span
                        class="text-xs font-medium text-leaf"
                        x-show="passwordConfirmation !== '' && password === passwordConfirmation"
                        x-cloak
                    >Las contraseñas coinciden</span>
                </div>
                <div class="relative">
                    <input
                        id="password_confirmation"
                        :type="showConfirmation ? 'text' : 'password'"
                        wire:model="password_confirmation"
                        x-model="passwordConfirmation"
                        autocomplete="new-password"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 pr-10 text-sm outline-none ring-forest/20 transition focus:border-forest focus:bg-white focus:ring-4"
                    >
                    <button type="button" class="absolute inset-y-0 right-3 text-slate-400" @click="showConfirmation = !showConfirmation" aria-label="Mostrar confirmación">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <label class="mt-5 flex items-start gap-2.5 text-xs leading-relaxed text-slate-500">
            <input type="checkbox" class="mt-0.5 rounded border-slate-300 text-forest focus:ring-forest">
            <span>Acepto los Términos de Servicio y la Política de Privacidad de SobrePeso.</span>
        </label>

        <button type="submit" class="mt-5 flex w-full items-center justify-center gap-2 rounded-2xl bg-forest px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-forest-dark">
            Crear cuenta gratuita
            <span aria-hidden="true">→</span>
        </button>

        <p class="mt-5 text-center text-sm text-slate-500">
            ¿Ya tienes una cuenta?
            <a href="{{ route('login') }}" wire:navigate class="font-semibold text-forest hover:underline">Inicia sesión aquí</a>
        </p>
    </form>
</div>
