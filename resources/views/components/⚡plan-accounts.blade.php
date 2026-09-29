<?php

use App\Models\Cuenta;
use App\Models\Plan;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public bool $open = false;

    public string $step = 'type';

    public string $name = '';

    public string $moneySource = '';

    public string $balance = '';

    public ?string $type = null;

    public ?int $editingId = null;

    #[Computed]
    public function plan(): ?Plan
    {
        $user = Auth::user();

        if ($user === null) {
            return null;
        }

        return $user->plans()->latest('id')->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Cuenta>
     */
    #[Computed]
    public function cuentas()
    {
        $plan = $this->plan;

        if ($plan === null) {
            return new \Illuminate\Database\Eloquent\Collection;
        }

        return $plan->cuentas()->orderBy('id')->get();
    }

    #[On('ingreso-guardado')]
    public function refreshBalances(): void
    {
        unset($this->plan);
        unset($this->cuentas);
    }

    public function openPopup(): void
    {
        if (! Auth::check()) {
            return;
        }

        $this->resetForm();
        $this->open = true;
    }

    public function closePopup(): void
    {
        $this->open = false;
        $this->resetForm();
    }

    public function chooseType(string $type): void
    {
        if (! Auth::check() || ! in_array($type, ['tarjeta_credito', 'indefinida'], true)) {
            return;
        }

        $this->type = $type;
        $this->editingId = null;
        $this->name = '';
        $this->moneySource = '';
        $this->balance = '';
        $this->resetValidation();
        $this->step = 'fields';
    }

    public function edit(int $cuentaId): void
    {
        $cuenta = $this->ownedCuenta($cuentaId);

        if ($cuenta === null) {
            return;
        }

        $this->editingId = $cuenta->id;
        $this->type = $cuenta->type;
        $this->name = $cuenta->name;
        $this->moneySource = $cuenta->money_source;
        $this->balance = str_replace('.', ',', $cuenta->balance);
        $this->resetValidation();
        $this->open = true;
        $this->step = 'edit';
    }

    public function save(): void
    {
        if (! Auth::check()) {
            return;
        }

        $this->name = trim($this->name);
        $this->moneySource = trim($this->moneySource);
        $this->balance = trim($this->balance);

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'moneySource' => ['required', 'string', 'max:255'],
            'balance' => ['required', 'string', 'max:255'],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'moneySource.required' => 'El nombre es obligatorio.',
            'balance.required' => 'El nombre es obligatorio.',
        ]);

        $balance = $this->normalizedBalance($this->balance);

        if ($balance === null) {
            $this->addError('balance', 'El valor no es válido.');

            return;
        }

        if ($this->editingId !== null) {
            $cuenta = $this->ownedCuenta($this->editingId);

            if ($cuenta === null) {
                return;
            }

            $cuenta->update([
                'name' => $this->name,
                'money_source' => $this->moneySource,
                'balance' => $balance,
            ]);

            $this->closePopup();

            return;
        }

        if (! in_array($this->type, ['tarjeta_credito', 'indefinida'], true)) {
            return;
        }

        $plan = $this->plan;

        if ($plan === null) {
            return;
        }

        $plan->cuentas()->create([
            'type' => $this->type,
            'name' => $this->name,
            'money_source' => $this->moneySource,
            'balance' => $balance,
        ]);

        $this->step = 'success';
    }

    private function resetForm(): void
    {
        $this->step = 'type';
        $this->name = '';
        $this->moneySource = '';
        $this->balance = '';
        $this->type = null;
        $this->editingId = null;
        $this->resetValidation();
    }

    private function ownedCuenta(int $cuentaId): ?Cuenta
    {
        $userId = Auth::id();

        if ($userId === null) {
            return null;
        }

        return Cuenta::query()
            ->whereKey($cuentaId)
            ->whereHas('plan', fn ($query) => $query->where('user_id', $userId))
            ->first();
    }

    private function normalizedBalance(string $value): ?string
    {
        if ($value === '' || str_contains($value, '-')) {
            return null;
        }

        if (str_contains($value, ',')) {
            if (! preg_match('/^(?:\d{1,3}(?:\.\d{3})*|\d+),\d{1,2}$/', $value)) {
                return null;
            }

            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        } elseif (! preg_match('/^\d+(?:\.\d{1,2})?$/', $value)) {
            return null;
        }

        [$whole, $fraction] = array_pad(explode('.', $value, 2), 2, '00');

        if ($fraction === '') {
            $fraction = '00';
        }

        if (! ctype_digit($whole) || ! ctype_digit($fraction) || strlen($fraction) > 2) {
            return null;
        }

        return $whole.'.'.str_pad($fraction, 2, '0');
    }

    #[Computed]
    public function formattedTotal(): string
    {
        $cents = 0;

        foreach ($this->cuentas as $cuenta) {
            $cents += $this->toCents($cuenta->balance);
        }

        return $this->formatAmount($this->fromCents($cents));
    }

    public function formatAmount(string $amount): string
    {
        $plan = $this->plan;

        if ($plan === null) {
            return $this->formatWithoutPlan($amount);
        }

        return $plan->formatMoney($amount);
    }

    private function formatWithoutPlan(string $amount): string
    {
        $negative = str_starts_with($amount, '-');
        $amount = ltrim($amount, '-');
        [$whole, $fraction] = array_pad(explode('.', $amount, 2), 2, '00');
        $fraction = str_pad(substr($fraction, 0, 2), 2, '0');
        $formatted = $whole.','.$fraction;

        return $negative ? '-'.$formatted : $formatted;
    }

    private function toCents(string $amount): int
    {
        $negative = str_starts_with($amount, '-');
        $amount = ltrim($amount, '-');
        [$whole, $fraction] = array_pad(explode('.', $amount, 2), 2, '00');
        $fraction = str_pad(substr($fraction, 0, 2), 2, '0');
        $cents = ((int) $whole * 100) + (int) $fraction;

        return $negative ? -$cents : $cents;
    }

    private function fromCents(int $cents): string
    {
        $negative = $cents < 0;
        $cents = abs($cents);

        return ($negative ? '-' : '').intdiv($cents, 100).'.'.str_pad((string) ($cents % 100), 2, '0', STR_PAD_LEFT);
    }
};
?>

<div>
    @if (auth()->check())
        @if ($this->cuentas->isNotEmpty())
            <ul class="mt-1 space-y-1">
                @foreach ($this->cuentas as $cuenta)
                    <li class="flex items-center gap-1 rounded-xl px-3 py-2 text-sm">
                        <a href="{{ route('cuentas.show', $cuenta) }}" wire:navigate class="flex min-w-0 flex-1 items-center gap-2.5 text-slate-700">
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-mint text-forest">
                                @if ($cuenta->type === 'tarjeta_credito')
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M5 6h14a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2Z" />
                                    </svg>
                                @else
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                    </svg>
                                @endif
                            </span>
                            <span class="truncate">{{ $cuenta->name }}</span>
                        </a>
                        <span class="shrink-0 text-xs font-medium text-slate-600">{{ $this->formatAmount($cuenta->balance) }}</span>
                        <button type="button" wire:click="edit({{ $cuenta->id }})" aria-label="Editar" class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-lg text-slate-400">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.25 2.25 0 1 1 3.182 3.182L8.25 18.463 4.5 19.5l1.037-3.75 11.325-12.263Z" />
                            </svg>
                        </button>
                    </li>
                @endforeach
            </ul>
        @endif

        @teleport('#cuentas-total')
            <p class="text-[11px] uppercase tracking-wider text-slate-400">Total en Cuentas</p>
            <p class="mt-1 text-lg font-semibold text-forest-dark">{{ $this->formattedTotal }}</p>
        @endteleport

        <button type="button" wire:click="openPopup" class="mt-2 flex w-full items-center gap-2 rounded-xl px-3 py-2 text-sm text-forest hover:bg-mint">
            <span class="text-lg leading-none">+</span>
            Agregar cuenta
        </button>

        @if ($open)
            @teleport('body')
                <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 px-4">
                    <section class="w-full max-w-md rounded-2xl bg-white p-6 shadow-sm ring-1 ring-emerald-100">
                        @if ($step === 'success')
                            <p class="text-sm font-medium text-forest">cuenta creada exitosamente</p>
                            <button type="button" wire:click="closePopup" class="mt-4 text-sm text-slate-500">Cerrar</button>
                        @elseif ($step === 'type')
                            <h2 class="text-lg font-semibold tracking-tight text-slate-900">Agregar cuenta</h2>
                            <p class="mt-1 text-sm text-slate-500">Elige el tipo de cuenta.</p>
                            <div class="mt-5 space-y-2">
                                <button type="button" wire:click="chooseType('tarjeta_credito')" class="w-full rounded-xl bg-forest px-3 py-2.5 text-sm font-semibold text-white shadow-sm">Tarjeta de crédito</button>
                                <button type="button" wire:click="chooseType('indefinida')" class="w-full rounded-xl bg-white px-3 py-2.5 text-sm font-semibold text-forest shadow-sm ring-1 ring-emerald-100">Cuenta indefinida (efectivo)</button>
                            </div>
                            <button type="button" wire:click="closePopup" class="mt-4 text-sm text-slate-500">Cerrar</button>
                        @elseif ($this->plan === null && $step === 'fields')
                            <p class="text-sm text-slate-600">Aún no tienes planes.</p>
                            <a href="{{ route('planes.crear') }}" wire:navigate class="mt-4 inline-flex rounded-xl bg-forest px-3 py-2 text-sm font-semibold text-white">Crear plan</a>
                        @else
                            <h2 class="text-lg font-semibold tracking-tight text-slate-900">{{ $step === 'edit' ? 'Editar cuenta' : 'Agregar cuenta' }}</h2>
                            <form wire:submit="save" class="mt-5 space-y-4">
                                <div>
                                    <label for="cuenta-name" class="mb-1.5 block text-sm font-medium text-slate-700">Nombre</label>
                                    <input id="cuenta-name" type="text" wire:model="name" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm outline-none ring-forest/20 transition focus:border-forest focus:bg-white focus:ring-4">
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="cuenta-source" class="mb-1.5 block text-sm font-medium text-slate-700">De dónde entra el dinero</label>
                                    <input id="cuenta-source" type="text" wire:model="moneySource" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm outline-none ring-forest/20 transition focus:border-forest focus:bg-white focus:ring-4">
                                    @error('moneySource')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="cuenta-balance" class="mb-1.5 block text-sm font-medium text-slate-700">Balance actual</label>
                                    <input id="cuenta-balance" type="text" inputmode="decimal" wire:model="balance" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm outline-none ring-forest/20 transition focus:border-forest focus:bg-white focus:ring-4">
                                    @error('balance')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <button type="submit" class="rounded-xl bg-forest px-4 py-2.5 text-sm font-semibold text-white shadow-sm">Guardar</button>
                            </form>
                            <button type="button" wire:click="closePopup" class="mt-4 text-sm text-slate-500">Cerrar</button>
                        @endif
                    </section>
                </div>
            @endteleport
        @endif
    @endif
</div>
