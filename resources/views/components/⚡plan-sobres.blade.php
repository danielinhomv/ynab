<?php

use App\Models\Plan;
use App\Models\Sobre;
use App\Models\SobreMes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public ?int $editingId = null;

    public string $name = '';

    public string $assigned = '';

    public string $activity = '';

    public string $available = '';

    public bool $creating = false;

    public string $createKind = 'category';

    public string $draftName = '';

    public ?int $categoryId = null;

    public bool $resumeSobre = false;

    public ?int $planId = null;

    public int $year = 0;

    public int $month = 0;

    public ?int $coverSobreId = null;

    public ?int $sourceSobreId = null;

    public string $coverMessage = '';

    public function mount(): void
    {
        $user = Auth::user();

        if ($user === null) {
            return;
        }

        $this->year = (int) now()->year;
        $this->month = (int) now()->month;

        if ($user->plans()->count() === 1) {
            $this->planId = (int) $user->plans()->value('id');
        }
    }

    #[Computed]
    public function hasPlans(): bool
    {
        return Auth::user()?->plans()->exists() ?? false;
    }

    #[Computed]
    public function plan(): ?Plan
    {
        $user = Auth::user();

        if ($user === null || $this->planId === null) {
            return null;
        }

        return $user->plans()
            ->with(['categories.sobres.meses'])
            ->whereKey($this->planId)
            ->first();
    }

    #[Computed]
    public function monthTitle(): string
    {
        $names = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre',
        ];

        return ($names[$this->month] ?? '').' '.$this->year;
    }

    #[On('plan-abierto')]
    public function abrirPlan(int $planId): void
    {
        $userId = Auth::id();

        if ($userId === null) {
            return;
        }

        $plan = Plan::query()->where('user_id', $userId)->whereKey($planId)->first();

        if ($plan === null) {
            return;
        }

        $this->planId = $plan->id;
        $this->editingId = null;
        unset($this->plan);
    }

    public function previousMonth(): void
    {
        $this->shiftMonth(-1);
    }

    public function nextMonth(): void
    {
        $this->shiftMonth(1);
    }

    public function edit(int $sobreId): void
    {
        $sobre = $this->ownedSobre($sobreId);
        $plan = $this->plan;

        if ($sobre === null || $plan === null) {
            return;
        }

        $row = $this->monthRow($sobre);

        $this->editingId = $sobre->id;
        $this->name = $sobre->name;
        $this->assigned = $plan->amountForInput($row->assigned ?? '0.00');
        $this->activity = $plan->amountForInput($row->activity ?? '0.00');
        $this->available = $plan->amountForInput($row->available ?? '0.00');
        $this->resetValidation();
    }

    public function save(): void
    {
        $sobre = $this->editingId === null ? null : $this->ownedSobre($this->editingId);

        if ($sobre === null) {
            return;
        }

        $this->name = trim($this->name);
        $this->assigned = trim($this->assigned);
        $this->activity = trim($this->activity);
        $this->available = trim($this->available);

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'assigned' => ['required', 'string', 'max:255'],
            'activity' => ['required', 'string', 'max:255'],
            'available' => ['required', 'string', 'max:255'],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'assigned.required' => 'El valor no es válido.',
            'activity.required' => 'El valor no es válido.',
            'available.required' => 'El valor no es válido.',
        ]);

        $assigned = $this->normalizedAmount($this->assigned);
        $activity = $this->normalizedAmount($this->activity);
        $available = $this->normalizedAmount($this->available);

        if ($assigned === null) {
            $this->addError('assigned', 'El valor no es válido.');
        }

        if ($activity === null) {
            $this->addError('activity', 'El valor no es válido.');
        }

        if ($available === null) {
            $this->addError('available', 'El valor no es válido.');
        }

        if ($assigned === null || $activity === null || $available === null) {
            return;
        }

        $sobre->update([
            'name' => $this->name,
        ]);

        $sobre->meses()->updateOrCreate(
            ['anio' => $this->year, 'mes' => $this->month],
            [
                'assigned' => $assigned,
                'activity' => $activity,
                'available' => $available,
            ],
        );

        $this->editingId = null;
        $this->resetValidation();
        unset($this->plan);
    }

    #[On('open-category')]
    public function openCategory(): void
    {
        if (Auth::user() === null || ($this->plan === null && $this->hasPlans)) {
            return;
        }

        $this->creating = true;
        $this->createKind = 'category';
        $this->draftName = '';
        $this->resumeSobre = false;
        $this->resetValidation();
    }

    #[On('open-sobre')]
    public function openSobre(): void
    {
        if (Auth::user() === null || ($this->plan === null && $this->hasPlans)) {
            return;
        }

        $this->creating = true;
        $this->createKind = 'sobre';
        $this->draftName = '';
        $this->resumeSobre = false;
        $this->categoryId = $this->latestCategoryId();
        $this->resetValidation();
    }

    public function openCategoryFromSobre(): void
    {
        if (Auth::user() === null || $this->plan === null) {
            return;
        }

        $this->creating = true;
        $this->createKind = 'category';
        $this->draftName = '';
        $this->resumeSobre = true;
        $this->resetValidation();
    }

    public function closeCreate(): void
    {
        $this->creating = false;
        $this->draftName = '';
        $this->categoryId = null;
        $this->resumeSobre = false;
        $this->resetValidation();
    }

    public function store(): void
    {
        if (Auth::user() === null) {
            return;
        }

        $plan = $this->plan;

        if ($plan === null) {
            return;
        }

        $this->draftName = trim($this->draftName);

        $this->validate([
            'draftName' => ['required', 'string', 'max:255'],
        ], [
            'draftName.required' => 'El nombre es obligatorio.',
        ]);

        if ($this->createKind === 'category') {
            $plan->categories()->create([
                'name' => $this->draftName,
            ]);

            unset($this->plan);

            if ($this->resumeSobre) {
                $this->resumeSobre = false;
                $this->createKind = 'sobre';
                $this->draftName = '';
                $this->categoryId = $this->latestCategoryId();
                $this->resetValidation();

                return;
            }

            $this->closeCreate();

            return;
        }

        if ($this->createKind !== 'sobre') {
            return;
        }

        $category = $plan->categories()->whereKey($this->categoryId)->first();

        if ($category === null) {
            $this->addError('categoryId', 'El valor no es válido.');

            return;
        }

        $category->sobres()->create([
            'name' => $this->draftName,
            'assigned' => '0.00',
            'activity' => '0.00',
            'available' => '0.00',
        ]);

        unset($this->plan);
        $this->closeCreate();
    }

    private function latestCategoryId(): ?int
    {
        $id = $this->plan?->categories->sortByDesc('id')->first()?->id;

        return $id === null ? null : (int) $id;
    }

    private function ownedSobre(int $sobreId): ?Sobre
    {
        $userId = Auth::id();

        if ($userId === null) {
            return null;
        }

        if ($this->planId === null) {
            return null;
        }

        return Sobre::query()
            ->whereKey($sobreId)
            ->whereHas('category', fn ($query) => $query->where('plan_id', $this->planId))
            ->whereHas('category.plan', fn ($query) => $query->where('user_id', $userId))
            ->first();
    }

    private function shiftMonth(int $delta): void
    {
        if (Auth::user() === null || $this->month < 1) {
            return;
        }

        $date = Carbon::create($this->year, $this->month, 1)->addMonths($delta);
        $this->year = (int) $date->year;
        $this->month = (int) $date->month;
        $this->editingId = null;
        $this->coverSobreId = null;
        $this->sourceSobreId = null;
        $this->coverMessage = '';
        unset($this->plan);
    }

    public function rowIsOverspent(Sobre $sobre): bool
    {
        $amounts = $this->monthValues($sobre);

        return $this->toCents($amounts['activity']) > $this->toCents($amounts['assigned']);
    }

    public function shortfallLabel(Sobre $sobre): string
    {
        $amounts = $this->monthValues($sobre);
        $cents = $this->toCents($amounts['activity']) - $this->toCents($amounts['assigned']);

        return $this->plan?->formatMoney($this->fromCents(max($cents, 0))) ?? '';
    }

    public function openCover(int $sobreId): void
    {
        $sobre = $this->ownedSobre($sobreId);

        if ($sobre === null || $this->plan === null || ! $this->rowIsOverspent($sobre)) {
            return;
        }

        $this->coverSobreId = $sobre->id;
        $this->sourceSobreId = null;
        $this->editingId = null;
        $this->coverMessage = $this->coverSources()->isEmpty() ? 'No hay otro sobre.' : '';
    }

    public function closeCover(): void
    {
        $this->coverSobreId = null;
        $this->sourceSobreId = null;
        $this->coverMessage = '';
    }

    public function chooseSource(int $sobreId): void
    {
        if ($this->coverSobreId === null || $sobreId === $this->coverSobreId) {
            return;
        }

        $source = $this->ownedSobre($sobreId);

        if ($source === null) {
            return;
        }

        $this->sourceSobreId = $source->id;
    }

    public function confirmCover(): void
    {
        $problem = $this->coverSobreId === null ? null : $this->ownedSobre($this->coverSobreId);
        $source = $this->sourceSobreId === null ? null : $this->ownedSobre($this->sourceSobreId);

        if ($problem === null || $source === null || $problem->id === $source->id) {
            return;
        }

        $problemRow = $this->monthRow($problem);
        $sourceRow = $this->monthRow($source);
        $shortfall = $problemRow === null
            ? 0
            : $this->toCents($problemRow->activity) - $this->toCents($problemRow->assigned);
        $sourceAssigned = $sourceRow === null ? 0 : $this->toCents($sourceRow->assigned);

        if ($shortfall <= 0 || $sourceRow === null || $sourceAssigned < $shortfall) {
            $this->coverMessage = 'No alcanza para cubrir.';

            return;
        }

        DB::transaction(function () use ($problemRow, $sourceRow, $shortfall): void {
            $problemRow->update([
                'assigned' => $this->fromCents($this->toCents($problemRow->assigned) + $shortfall),
            ]);
            $sourceRow->update([
                'assigned' => $this->fromCents($this->toCents($sourceRow->assigned) - $shortfall),
            ]);
        });

        $this->coverSobreId = null;
        $this->sourceSobreId = null;
        $this->coverMessage = 'sobre cubierto exitosamente';
        unset($this->plan);
    }

    public function coveredSobre(): ?Sobre
    {
        if ($this->coverSobreId === null) {
            return null;
        }

        foreach ($this->plan?->categories ?? [] as $category) {
            $sobre = $category->sobres->firstWhere('id', $this->coverSobreId);

            if ($sobre !== null) {
                return $sobre;
            }
        }

        return null;
    }

    /**
     * @return \Illuminate\Support\Collection<int, Sobre>
     */
    public function coverSources(): \Illuminate\Support\Collection
    {
        $sobres = collect();

        foreach ($this->plan?->categories ?? [] as $category) {
            foreach ($category->sobres as $sobre) {
                if ($sobre->id !== $this->coverSobreId) {
                    $sobres->push($sobre);
                }
            }
        }

        return $sobres;
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

    /**
     * @return array{assigned: string, activity: string, available: string}
     */
    private function monthValues(Sobre $sobre): array
    {
        $row = $sobre->meses->first(
            fn (SobreMes $mes): bool => (int) $mes->anio === $this->year && (int) $mes->mes === $this->month,
        );

        return [
            'assigned' => $row->assigned ?? '0.00',
            'activity' => $row->activity ?? '0.00',
            'available' => $row->available ?? '0.00',
        ];
    }

    private function monthRow(Sobre $sobre): ?SobreMes
    {
        return $sobre->meses()
            ->where('anio', $this->year)
            ->where('mes', $this->month)
            ->first();
    }

    private function normalizedAmount(string $value): ?string
    {
        $negative = str_starts_with($value, '-');

        if ($negative) {
            $value = substr($value, 1);
        }

        if ($value === '' || str_contains($value, '-')) {
            return null;
        }

        if ($this->plan?->number_format === '1,234.56') {
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

        $normalized = $whole.'.'.str_pad($fraction, 2, '0');

        return $negative ? '-'.$normalized : $normalized;
    }
};
?>

<div>
    @if (auth()->check())
        @teleport('#mes-controles')
            <button type="button" wire:click="previousMonth" aria-label="Mes anterior" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white text-sm font-semibold text-forest shadow-sm ring-1 ring-emerald-100">‹</button>
            <h1 class="text-xl font-semibold tracking-tight sm:text-2xl">{{ $this->monthTitle }}</h1>
            <button type="button" wire:click="nextMonth" aria-label="Mes siguiente" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white text-sm font-semibold text-forest shadow-sm ring-1 ring-emerald-100">›</button>
        @endteleport

        <section class="relative mt-5 overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-emerald-100">
            <div class="grid grid-cols-[minmax(0,2fr)_repeat(3,minmax(0,1fr))] gap-2 border-b border-slate-100 px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 sm:px-5">
                <p>Categoría / Sobre</p>
                <p class="text-right">Asignado</p>
                <p class="text-right">Actividad</p>
                <p class="text-right">Disponible</p>
            </div>

            @forelse ($this->plan?->categories ?? [] as $category)
                <div class="border-b border-slate-100 bg-mint/50 px-4 py-2.5 sm:px-5">
                    <p class="text-sm font-semibold text-slate-800">{{ $category->name }}</p>
                </div>
                <div class="divide-y divide-slate-50">
                    @foreach ($category->sobres as $sobre)
                        @php
                            $amounts = $this->monthValues($sobre);
                            $overspent = $this->rowIsOverspent($sobre);
                        @endphp
                        @if ($editingId === $sobre->id)
                            <form wire:submit="save" @class([
                                'grid grid-cols-[minmax(0,2fr)_repeat(3,minmax(0,1fr))] items-start gap-2 px-4 py-3 sm:px-5',
                                'bg-red-50/70' => $overspent,
                            ])>
                                <div>
                                    <input type="text" wire:model="name" aria-label="Nombre" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none ring-forest/20 focus:border-forest focus:ring-4">
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <input type="text" wire:model="assigned" inputmode="decimal" aria-label="Asignado" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-right text-sm outline-none ring-forest/20 focus:border-forest focus:ring-4">
                                    @error('assigned')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <input type="text" wire:model="activity" inputmode="decimal" aria-label="Actividad" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-right text-sm outline-none ring-forest/20 focus:border-forest focus:ring-4">
                                    @error('activity')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="flex items-start justify-end gap-2">
                                    <div class="min-w-0 flex-1">
                                        <input type="text" wire:model="available" inputmode="decimal" aria-label="Disponible" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-right text-sm outline-none ring-forest/20 focus:border-forest focus:ring-4">
                                        @error('available')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <button type="submit" class="rounded-xl bg-forest px-3 py-2 text-sm font-semibold text-white">Guardar</button>
                                </div>
                            </form>
                        @elseif ($overspent)
                            <div class="grid grid-cols-[minmax(0,2fr)_repeat(3,minmax(0,1fr))] items-center gap-2 bg-red-50/70 px-4 py-3 sm:px-5">
                                <div class="flex flex-wrap items-center gap-2">
                                    <button type="button" wire:click="edit({{ $sobre->id }})" class="text-left text-sm font-medium text-slate-800">{{ $sobre->name }}</button>
                                    <span class="rounded-full bg-red-100 px-2 py-0.5 text-[11px] font-semibold text-red-600">Sobregiro</span>
                                    <button type="button" wire:click="openCover({{ $sobre->id }})" class="rounded-lg bg-white px-2 py-1 text-xs font-semibold text-red-600 ring-1 ring-red-200">Cubrir</button>
                                </div>
                                <span class="text-right text-sm text-slate-600">{{ $this->plan->formatMoney($amounts['assigned']) }}</span>
                                <span class="text-right text-sm text-slate-600">{{ $this->plan->formatMoney($amounts['activity']) }}</span>
                                <span class="text-right text-sm font-medium text-red-600">{{ $this->plan->formatMoney($amounts['available']) }}</span>
                            </div>
                        @else
                            <button
                                type="button"
                                wire:click="edit({{ $sobre->id }})"
                                class="grid w-full grid-cols-[minmax(0,2fr)_repeat(3,minmax(0,1fr))] items-center gap-2 px-4 py-3 text-left sm:px-5"
                            >
                                <span class="text-sm font-medium text-slate-800">{{ $sobre->name }}</span>
                                <span class="text-right text-sm text-slate-600">{{ $this->plan->formatMoney($amounts['assigned']) }}</span>
                                <span class="text-right text-sm text-slate-600">{{ $this->plan->formatMoney($amounts['activity']) }}</span>
                                <span class="text-right text-sm font-medium text-leaf">{{ $this->plan->formatMoney($amounts['available']) }}</span>
                            </button>
                        @endif
                    @endforeach
                </div>
            @empty
                @if ($this->plan === null && ! $this->hasPlans)
                    <p class="px-4 py-6 text-sm text-slate-600 sm:px-5">Aún no tienes planes.</p>
                @endif
            @endforelse
        </section>

        @if ($coverMessage === 'sobre cubierto exitosamente')
            <p class="mt-4 text-sm font-medium text-forest">{{ $coverMessage }}</p>
        @endif

        @if ($coverSobre = $this->coveredSobre())
            <section class="mt-4 rounded-2xl border border-red-100 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold tracking-tight text-slate-900">Cubrir sobregiro</h2>
                            <p class="mt-1 text-sm font-medium text-slate-800">{{ $coverSobre->name }}</p>
                            <p class="mt-2 text-sm text-slate-600">{{ $this->shortfallLabel($coverSobre) }}</p>
                        </div>
                        <button type="button" wire:click="closeCover" aria-label="Cerrar" class="text-xl leading-none text-slate-400">×</button>
                    </div>
                    @if ($coverMessage !== '')
                        <p class="mt-4 text-sm text-red-600">{{ $coverMessage }}</p>
                    @endif
                    @if ($this->coverSources()->isNotEmpty())
                        <ul class="mt-4 space-y-2">
                            @foreach ($this->coverSources() as $source)
                                <li wire:key="cover-source-{{ $source->id }}">
                                    <button
                                        type="button"
                                        wire:click="chooseSource({{ $source->id }})"
                                        @class([
                                            'w-full rounded-xl px-3 py-2 text-left text-sm',
                                            'bg-mint font-semibold text-forest' => $sourceSobreId === $source->id,
                                            'bg-white text-slate-700 ring-1 ring-emerald-100' => $sourceSobreId !== $source->id,
                                        ])
                                    >{{ $source->name }}</button>
                                </li>
                            @endforeach
                        </ul>
                        <button type="button" wire:click="confirmCover" class="mt-4 rounded-xl bg-forest px-4 py-2.5 text-sm font-semibold text-white">Confirmar</button>
                    @endif
            </section>
        @endif

        @if ($creating)
            @teleport('body')
                <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 px-4">
                    <section class="w-full max-w-md rounded-2xl bg-white p-6 shadow-sm ring-1 ring-emerald-100">
                        @if ($this->plan === null)
                            <p class="text-sm text-slate-600">Aún no tienes planes.</p>
                        @elseif ($createKind === 'category')
                            <h2 class="text-lg font-semibold tracking-tight text-slate-900">Nueva categoría</h2>
                            <form wire:submit="store" class="mt-5 space-y-4">
                                <div>
                                    <label for="category-name" class="mb-1.5 block text-sm font-medium text-slate-700">Nombre</label>
                                    <input id="category-name" type="text" wire:model="draftName" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm outline-none ring-forest/20 transition focus:border-forest focus:bg-white focus:ring-4">
                                    @error('draftName')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <button type="submit" class="rounded-xl bg-forest px-4 py-2.5 text-sm font-semibold text-white shadow-sm">Guardar</button>
                            </form>
                        @elseif ($this->plan->categories->isEmpty())
                            <h2 class="text-lg font-semibold tracking-tight text-slate-900">Nuevo sobre</h2>
                            <button type="button" wire:click="openCategoryFromSobre" class="mt-5 rounded-xl bg-forest px-4 py-2.5 text-sm font-semibold text-white shadow-sm">+ Nueva categoría</button>
                        @else
                            <h2 class="text-lg font-semibold tracking-tight text-slate-900">Nuevo sobre</h2>
                            <form wire:submit="store" class="mt-5 space-y-4">
                                <div>
                                    <label for="sobre-name" class="mb-1.5 block text-sm font-medium text-slate-700">Nombre</label>
                                    <input id="sobre-name" type="text" wire:model="draftName" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm outline-none ring-forest/20 transition focus:border-forest focus:bg-white focus:ring-4">
                                    @error('draftName')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="sobre-category" class="mb-1.5 block text-sm font-medium text-slate-700">Categoría</label>
                                    <select id="sobre-category" wire:model="categoryId" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm outline-none ring-forest/20 transition focus:border-forest focus:bg-white focus:ring-4">
                                        @foreach ($this->plan->categories as $category)
                                            <option wire:key="create-category-{{ $category->id }}" value="{{ $category->id }}" @selected($categoryId === $category->id)>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('categoryId')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <button type="submit" class="rounded-xl bg-forest px-4 py-2.5 text-sm font-semibold text-white shadow-sm">Guardar</button>
                            </form>
                        @endif
                        <button type="button" wire:click="closeCreate" class="mt-4 text-sm text-slate-500">Cerrar</button>
                    </section>
                </div>
            @endteleport
        @endif
    @endif
</div>
