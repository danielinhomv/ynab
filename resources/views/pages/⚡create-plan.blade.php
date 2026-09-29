<?php

use App\Models\Plan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Crear plan')] class extends Component
{
    public string $name = '';

    public string $currency = '';

    public string $fecha = '';

    public string $numberFormat = '';

    public string $currencyPlacement = '';

    public function mount(): void
    {
        $this->currency = 'BOB';
        $this->fecha = now()->toDateString();
        $this->numberFormat = '1.234,56';
        $this->currencyPlacement = 'antes';
    }

    /**
     * @return list<string>
     */
    public function currencyOptions(): array
    {
        return ['BOB', 'USD', 'EUR'];
    }

    /**
     * @return list<string>
     */
    public function numberFormatOptions(): array
    {
        return ['1.234,56', '1,234.56'];
    }

    /**
     * @return list<string>
     */
    public function currencyPlacementOptions(): array
    {
        return ['antes', 'después'];
    }

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'currency' => ['required', Rule::in($this->currencyOptions())],
            'fecha' => ['required', 'date'],
            'numberFormat' => ['required', Rule::in($this->numberFormatOptions())],
            'currencyPlacement' => ['required', Rule::in($this->currencyPlacementOptions())],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'currency.in' => 'El valor no es válido.',
            'fecha.date' => 'El valor no es válido.',
            'numberFormat.in' => 'El valor no es válido.',
            'currencyPlacement.in' => 'El valor no es válido.',
        ]);

        $userId = Auth::id();

        if ($userId === null) {
            return;
        }

        $plan = Plan::query()->create([
            'user_id' => $userId,
            'name' => $this->name,
            'currency' => $this->currency,
            'fecha' => $this->fecha,
            'number_format' => $this->numberFormat,
            'currency_placement' => $this->currencyPlacement,
        ]);

        session()->flash('status', 'Plan creado exitosamente');

        $this->redirect(route('planes.show', $plan));
    }
};
?>

<div class="px-4 py-6 sm:px-6 lg:px-8">
    <form wire:submit="save" class="mx-auto w-full max-w-lg rounded-2xl bg-white p-6 shadow-sm ring-1 ring-emerald-100 sm:p-8">
        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-forest">Plan</p>
        <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">Crear plan</h1>
        <p class="mt-2 text-sm text-slate-500">Un plan de una sola moneda. Otra moneda es otro plan.</p>

        <div class="mt-6 space-y-4">
            <div>
                <label for="name" class="mb-1.5 block text-sm font-medium text-slate-700">Nombre</label>
                <input id="name" type="text" wire:model="name" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm outline-none ring-forest/20 transition focus:border-forest focus:bg-white focus:ring-4">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="currency" class="mb-1.5 block text-sm font-medium text-slate-700">Multimoneda</label>
                <select id="currency" wire:model="currency" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm outline-none ring-forest/20 transition focus:border-forest focus:bg-white focus:ring-4">
                    @foreach ($this->currencyOptions() as $option)
                        <option value="{{ $option }}">{{ $option }}</option>
                    @endforeach
                </select>
                @error('currency')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="fecha" class="mb-1.5 block text-sm font-medium text-slate-700">Fecha</label>
                <input id="fecha" type="date" wire:model="fecha" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm outline-none ring-forest/20 transition focus:border-forest focus:bg-white focus:ring-4">
                @error('fecha')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="number-format" class="mb-1.5 block text-sm font-medium text-slate-700">Number format</label>
                <select id="number-format" wire:model="numberFormat" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm outline-none ring-forest/20 transition focus:border-forest focus:bg-white focus:ring-4">
                    @foreach ($this->numberFormatOptions() as $option)
                        <option value="{{ $option }}">{{ $option }}</option>
                    @endforeach
                </select>
                @error('numberFormat')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="currency-placement" class="mb-1.5 block text-sm font-medium text-slate-700">Currency placement</label>
                <select id="currency-placement" wire:model="currencyPlacement" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm outline-none ring-forest/20 transition focus:border-forest focus:bg-white focus:ring-4">
                    @foreach ($this->currencyPlacementOptions() as $option)
                        <option value="{{ $option }}">{{ $option }}</option>
                    @endforeach
                </select>
                @error('currencyPlacement')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <button type="submit" class="mt-6 rounded-2xl bg-forest px-4 py-2.5 text-sm font-semibold text-white hover:bg-forest-dark">
            Crear plan
        </button>
    </form>
</div>
