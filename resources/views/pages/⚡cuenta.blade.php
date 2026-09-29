<?php

use App\Models\Cuenta;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Cuenta')] class extends Component
{
    public Cuenta $cuenta;

    public string $fecha = '';

    public string $origen = '';

    public string $descripcion = '';

    public string $monto = '';

    public bool $confirmed = false;

    public function mount(Cuenta $cuenta): void
    {
        $cuenta->load('plan');

        abort_unless((int) $cuenta->plan->user_id === (int) Auth::id(), 404);

        $this->cuenta = $cuenta;
        $this->fecha = now()->toDateString();
    }

    public function save(): void
    {
        $this->confirmed = false;

        $cuenta = $this->ownedCuenta();

        if ($cuenta === null) {
            return;
        }

        $this->fecha = trim($this->fecha);
        $this->origen = trim($this->origen);
        $this->descripcion = trim($this->descripcion);
        $this->monto = trim($this->monto);

        $this->validate([
            'fecha' => ['required', 'date'],
            'origen' => ['required', 'string', 'max:255'],
            'descripcion' => ['required', 'string', 'max:255'],
            'monto' => ['required', 'string', 'max:255'],
        ], [
            'fecha.required' => 'Este campo es obligatorio.',
            'fecha.date' => 'El valor no es válido.',
            'origen.required' => 'Este campo es obligatorio.',
            'descripcion.required' => 'Este campo es obligatorio.',
            'monto.required' => 'Este campo es obligatorio.',
        ]);

        $monto = $this->normalizedAmount($this->monto);

        if ($monto === null) {
            $this->addError('monto', 'El valor no es válido.');

            return;
        }

        DB::transaction(function () use ($cuenta, $monto): void {
            $cuenta->ingresos()->create([
                'fecha' => $this->fecha,
                'origen' => $this->origen,
                'descripcion' => $this->descripcion,
                'monto' => $monto,
            ]);

            $cuenta->update([
                'balance' => $this->addDecimal($cuenta->balance, $monto),
            ]);
        });

        $updated = $cuenta->fresh('plan');

        if ($updated instanceof Cuenta) {
            $this->cuenta = $updated;
        }
        $this->confirmed = true;
        $this->origen = '';
        $this->descripcion = '';
        $this->monto = '';
        $this->fecha = now()->toDateString();
        $this->dispatch('ingreso-guardado')->to('plan-accounts');
    }

    private function ownedCuenta(): ?Cuenta
    {
        $userId = Auth::id();

        if ($userId === null) {
            return null;
        }

        return Cuenta::query()
            ->whereKey($this->cuenta->id)
            ->whereHas('plan', fn ($query) => $query->where('user_id', $userId))
            ->first();
    }

    private function normalizedAmount(string $value): ?string
    {
        if ($value === '' || str_contains($value, '-')) {
            return null;
        }

        if ($this->cuenta->plan->number_format === '1,234.56') {
            if (! preg_match('/^(?:\d{1,3}(?:,\d{3})*|\d+)(?:\.\d{1,2})?$/', $value)) {
                return null;
            }

            $value = str_replace(',', '', $value);
        } elseif (! preg_match('/^(?:\d{1,3}(?:\.\d{3})*|\d+)(?:,\d{1,2})?$/', $value)) {
            return null;
        } else {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
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

    private function addDecimal(string $left, string $right): string
    {
        $cents = $this->toCents($left) + $this->toCents($right);
        $negative = $cents < 0;
        $cents = abs($cents);

        return ($negative ? '-' : '').intdiv($cents, 100).'.'.str_pad((string) ($cents % 100), 2, '0', STR_PAD_LEFT);
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
};
?>

<div class="px-4 py-6 sm:px-6 lg:px-8">
    <form wire:submit="save" class="mx-auto w-full max-w-lg rounded-2xl bg-white p-6 shadow-sm ring-1 ring-emerald-100 sm:p-8">
        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-forest">Cuenta</p>
        <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">{{ $cuenta->name }}</h1>
        <p class="mt-2 text-lg font-semibold text-forest">{{ $cuenta->plan->formatMoney($cuenta->balance) }}</p>

        @if ($confirmed)
            <p class="mt-4 text-sm font-medium text-forest">ingreso registrado exitosamente</p>
        @endif

        <div class="mt-6 space-y-4">
            <div>
                <label for="ingreso-fecha" class="mb-1.5 block text-sm font-medium text-slate-700">Fecha</label>
                <input id="ingreso-fecha" type="date" wire:model="fecha" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm outline-none ring-forest/20 transition focus:border-forest focus:bg-white focus:ring-4">
                @error('fecha')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="ingreso-origen" class="mb-1.5 block text-sm font-medium text-slate-700">De quién o cómo entra el dinero</label>
                <input id="ingreso-origen" type="text" wire:model="origen" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm outline-none ring-forest/20 transition focus:border-forest focus:bg-white focus:ring-4">
                @error('origen')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="ingreso-descripcion" class="mb-1.5 block text-sm font-medium text-slate-700">Descripción</label>
                <input id="ingreso-descripcion" type="text" wire:model="descripcion" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm outline-none ring-forest/20 transition focus:border-forest focus:bg-white focus:ring-4">
                @error('descripcion')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="ingreso-monto" class="mb-1.5 block text-sm font-medium text-slate-700">Monto</label>
                <input id="ingreso-monto" type="text" inputmode="decimal" wire:model="monto" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm outline-none ring-forest/20 transition focus:border-forest focus:bg-white focus:ring-4">
                @error('monto')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <button type="submit" class="mt-6 rounded-2xl bg-forest px-4 py-2.5 text-sm font-semibold text-white hover:bg-forest-dark">Guardar</button>
    </form>
</div>
