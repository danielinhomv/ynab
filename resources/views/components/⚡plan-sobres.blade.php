<?php

use App\Models\Cuenta;
use App\Models\Gasto;
use App\Models\Ingreso;
use App\Models\Plan;
use App\Models\Sobre;
use App\Models\SobreMes;
use App\Services\AmountExpression;
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

    public string $gasto = '';

    public ?int $gastoCuentaId = null;

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

    /** @var array<string, string> */
    public array $coverTakes = [];

    public string $coverMessage = '';

    public function mount(): void
    {
        $user = Auth::user();

        if ($user === null) {
            return;
        }

        $this->year = (int) now()->year;
        $this->month = (int) now()->month;
        $this->planId = $user->plans()->latest('id')->value('id');
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
            ->with(['categories.sobres.meses', 'cuentas'])
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

    #[Computed]
    public function moneyToAssign(): string
    {
        $plan = $this->plan;

        if ($plan === null || $this->month < 1) {
            return '0.00';
        }

        $visibleKey = sprintf('%04d-%02d', $this->year, $this->month);
        $income = [];
        $budgeted = [];

        $ingresos = Ingreso::query()
            ->whereHas('cuenta', fn ($query) => $query->where('plan_id', $plan->id))
            ->where('fecha', '<', Carbon::create($this->year, $this->month, 1)->addMonth()->toDateString())
            ->get(['fecha', 'monto']);

        foreach ($ingresos as $ingreso) {
            $key = $ingreso->fecha->format('Y-m');
            $income[$key] = bcadd($income[$key] ?? '0.00', $ingreso->monto, 2);
        }

        $meses = SobreMes::query()
            ->whereHas('sobre.category', fn ($query) => $query->where('plan_id', $plan->id))
            ->where(fn ($query) => $query
                ->where('anio', '<', $this->year)
                ->orWhere(fn ($query) => $query->where('anio', $this->year)->where('mes', '<=', $this->month)))
            ->get(['anio', 'mes', 'assigned']);

        foreach ($meses as $mes) {
            $key = sprintf('%04d-%02d', $mes->anio, $mes->mes);
            $budgeted[$key] = bcadd($budgeted[$key] ?? '0.00', $mes->assigned, 2);
        }

        $carriedDeficit = '0.00';

        foreach (array_unique([...array_keys($income), ...array_keys($budgeted)]) as $key) {
            if ($key >= $visibleKey) {
                continue;
            }

            $deficit = bcsub($budgeted[$key] ?? '0.00', $income[$key] ?? '0.00', 2);

            if (bccomp($deficit, '0.00', 2) > 0) {
                $carriedDeficit = bcadd($carriedDeficit, $deficit, 2);
            }
        }

        $ownTotal = bcsub($income[$visibleKey] ?? '0.00', $budgeted[$visibleKey] ?? '0.00', 2);

        return bcsub($ownTotal, $carriedDeficit, 2);
    }

    /**
     * @return array{income: string, budgeted: string, spent: string, incomeCount: int, sobreCount: int}
     */
    #[Computed]
    public function monthTotals(): array
    {
        $empty = [
            'income' => '0.00',
            'budgeted' => '0.00',
            'spent' => '0.00',
            'incomeCount' => 0,
            'sobreCount' => 0,
        ];
        $plan = $this->plan;

        if ($plan === null || $this->month < 1) {
            return $empty;
        }

        $start = Carbon::create($this->year, $this->month, 1);
        $ingresos = Ingreso::query()
            ->whereHas('cuenta', fn ($query) => $query->where('plan_id', $plan->id))
            ->where('fecha', '>=', $start->toDateString())
            ->where('fecha', '<', $start->copy()->addMonth()->toDateString())
            ->get(['monto']);

        $income = '0.00';

        foreach ($ingresos as $ingreso) {
            $income = bcadd($income, $ingreso->monto, 2);
        }

        $rows = SobreMes::query()
            ->whereHas('sobre.category', fn ($query) => $query->where('plan_id', $plan->id))
            ->where('anio', $this->year)
            ->where('mes', $this->month)
            ->get(['assigned', 'activity']);

        $budgeted = '0.00';
        $spent = '0.00';

        foreach ($rows as $row) {
            $budgeted = bcadd($budgeted, $row->assigned, 2);
            $spent = bcadd($spent, $row->activity, 2);
        }

        return [
            'income' => $income,
            'budgeted' => $budgeted,
            'spent' => $spent,
            'incomeCount' => $ingresos->count(),
            'sobreCount' => Sobre::query()
                ->whereHas('category', fn ($query) => $query->where('plan_id', $plan->id))
                ->count(),
        ];
    }

    public function spentPercentLabel(): string
    {
        $totals = $this->monthTotals;

        if (bccomp($totals['budgeted'], '0.00', 2) <= 0) {
            return '0,0% del presupuesto';
        }

        $percent = bcmul(bcdiv($totals['spent'], $totals['budgeted'], 4), '100', 1);

        return str_replace('.', ',', $percent).'% del presupuesto';
    }

    public function budgetMetaLabel(): string
    {
        $totals = $this->monthTotals;
        $deposits = $totals['incomeCount'] === 1 ? 'depósito recibido' : 'depósitos recibidos';
        $sobres = $totals['sobreCount'] === 1 ? 'sobre activo' : 'sobres activos';

        return $totals['incomeCount'].' '.$deposits.' · '.$totals['sobreCount'].' '.$sobres;
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

    #[On('saldos-cambiaron')]
    public function refreshTotals(): void
    {
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
        $this->gasto = '';
        $this->gastoCuentaId = $plan->cuentas->first()?->id;
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
        $this->available = trim($this->available);
        $this->gasto = trim($this->gasto);

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'assigned' => ['required', 'string', 'max:255'],
            'available' => ['required', 'string', 'max:255'],
            'gasto' => ['nullable', 'string', 'max:255'],
            'gastoCuentaId' => ['nullable', 'integer'],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'assigned.required' => 'El valor no es válido.',
            'available.required' => 'El valor no es válido.',
        ]);

        $assigned = $this->normalizedAmount($this->assigned);
        $available = $this->normalizedAmount($this->available);
        $gasto = $this->gasto === '' ? '0.00' : $this->normalizedAmount($this->gasto);

        if ($assigned === null) {
            $this->addError('assigned', 'El valor no es válido.');
        }

        if ($available === null) {
            $this->addError('available', 'El valor no es válido.');
        }

        if ($this->gasto !== '' && ($gasto === null || str_starts_with($gasto, '-'))) {
            $this->addError('gasto', 'El valor no es válido.');
            $gasto = null;
        }

        $cuenta = null;

        if ($this->gasto !== '' && $gasto !== null) {
            $cuenta = $this->ownedPlanCuenta($this->gastoCuentaId);

            if ($cuenta === null) {
                $this->addError('gastoCuentaId', 'El valor no es válido.');
            } elseif (bccomp($cuenta->balance, $gasto, 2) < 0) {
                $this->addError('gastoCuentaId', 'No alcanza en la cuenta.');
                $cuenta = null;
            }
        }

        if ($assigned === null || $available === null || ($this->gasto !== '' && ($gasto === null || $cuenta === null))) {
            return;
        }

        $currentActivity = $this->monthRow($sobre)?->activity ?? '0.00';
        $activity = bcadd($currentActivity, $gasto, 2);

        if ($this->gasto !== '') {
            $available = bcsub($assigned, $activity, 2);
        }

        try {
            DB::transaction(function () use ($sobre, $assigned, $activity, $available, $cuenta, $gasto): void {
                if ($cuenta !== null && $this->gasto !== '') {
                    $locked = Cuenta::query()->whereKey($cuenta->id)->lockForUpdate()->first();

                    if ($locked === null || bccomp($locked->balance, $gasto, 2) < 0) {
                        throw new \RuntimeException('insufficient');
                    }

                    Gasto::query()->create([
                        'cuenta_id' => $locked->id,
                        'sobre_id' => $sobre->id,
                        'anio' => $this->year,
                        'mes' => $this->month,
                        'monto' => $gasto,
                    ]);
                    $locked->update([
                        'balance' => bcsub($locked->balance, $gasto, 2),
                    ]);
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
            });
        } catch (\RuntimeException $exception) {
            if ($exception->getMessage() !== 'insufficient') {
                throw $exception;
            }

            $this->addError('gastoCuentaId', 'No alcanza en la cuenta.');

            return;
        }

        $this->editingId = null;
        $this->gastoCuentaId = null;
        $this->resetValidation();
        unset($this->plan);
        $this->dispatch('ingreso-guardado')->to('plan-accounts');
    }

    private function ownedPlanCuenta(?int $cuentaId): ?Cuenta
    {
        if ($cuentaId === null || $this->planId === null) {
            return null;
        }

        $userId = Auth::id();

        if ($userId === null) {
            return null;
        }

        return Cuenta::query()
            ->whereKey($cuentaId)
            ->where('plan_id', $this->planId)
            ->whereHas('plan', fn ($query) => $query->where('user_id', $userId))
            ->first();
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
        $this->coverTakes = [];
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

    public function shortfallAmount(Sobre $sobre): string
    {
        $amounts = $this->monthValues($sobre);
        $diff = bcsub($amounts['activity'], $amounts['assigned'], 2);

        return bccomp($diff, '0.00', 2) > 0 ? $diff : '0.00';
    }

    public function availableOf(Sobre $sobre): string
    {
        $amounts = $this->monthValues($sobre);

        return $this->positiveLeftover($amounts['assigned'], $amounts['activity']);
    }

    private function leftoverOfRow(SobreMes $row): string
    {
        return $this->positiveLeftover($row->assigned, $row->activity);
    }

    private function positiveLeftover(string $assigned, string $activity): string
    {
        $leftover = bcsub($assigned, $activity, 2);

        return bccomp($leftover, '0.00', 2) > 0 ? $leftover : '0.00';
    }

    public function canCover(Sobre $sobre): bool
    {
        if ($this->plan === null || ! $this->rowIsOverspent($sobre)) {
            return false;
        }

        $need = $this->shortfallAmount($sobre);
        $pool = '0.00';

        foreach ($this->otherSobres($sobre->id) as $source) {
            $pool = bcadd($pool, $this->availableOf($source), 2);
        }

        return bccomp($pool, $need, 2) >= 0;
    }

    public function openCover(int $sobreId): void
    {
        $sobre = $this->ownedSobre($sobreId);

        if ($sobre === null || $this->plan === null || ! $this->rowIsOverspent($sobre) || ! $this->canCover($sobre)) {
            return;
        }

        $this->coverSobreId = $sobre->id;
        $this->editingId = null;
        $this->coverMessage = '';
        $this->fillSuggestedTakes($sobre);
    }

    public function closeCover(): void
    {
        $this->coverSobreId = null;
        $this->coverTakes = [];
        $this->coverMessage = '';
    }

    public function confirmCover(): void
    {
        $problem = $this->coverSobreId === null ? null : $this->ownedSobre($this->coverSobreId);

        if ($problem === null || $this->plan === null) {
            return;
        }

        $problemRow = $this->monthRow($problem);
        $shortfall = $this->shortfallAmount($problem);
        $parsed = $this->parsedCoverTakes();

        if ($problemRow === null || bccomp($shortfall, '0.00', 2) <= 0 || $parsed === null) {
            $this->coverMessage = $this->coverTakesMessage() ?? 'El valor no es válido.';

            return;
        }

        $taken = '0.00';
        $sourceRows = [];

        foreach ($parsed as $sobreId => $amount) {
            $source = $this->ownedSobre((int) $sobreId);
            $row = $source === null ? null : $this->monthRow($source);

            if ($row === null || bccomp($amount, $this->leftoverOfRow($row), 2) > 0) {
                $this->coverMessage = 'El valor no es válido.';

                return;
            }

            $taken = bcadd($taken, $amount, 2);
            $sourceRows[] = [$row, $amount];
        }

        if (bccomp($taken, $shortfall, 2) !== 0) {
            $this->coverMessage = bccomp($taken, $shortfall, 2) > 0
                ? 'Te estás pasando del monto a cubrir.'
                : 'Falta por cubrir.';

            return;
        }

        DB::transaction(function () use ($problemRow, $sourceRows, $taken): void {
            $problemAssigned = bcadd($problemRow->assigned, $taken, 2);
            $problemRow->update([
                'assigned' => $problemAssigned,
                'available' => bcsub($problemAssigned, $problemRow->activity, 2),
            ]);

            foreach ($sourceRows as [$row, $amount]) {
                $sourceAssigned = bcsub($row->assigned, $amount, 2);
                $row->update([
                    'assigned' => $sourceAssigned,
                    'available' => bcsub($sourceAssigned, $row->activity, 2),
                ]);
            }
        });

        $this->coverSobreId = null;
        $this->coverTakes = [];
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
        if ($this->coverSobreId === null) {
            return collect();
        }

        return $this->otherSobres($this->coverSobreId)
            ->filter(fn (Sobre $sobre): bool => bccomp($this->availableOf($sobre), '0.00', 2) > 0)
            ->values();
    }

    public function coverTakenTotal(): string
    {
        $total = '0.00';

        foreach ($this->coverSources() as $source) {
            $raw = $this->rawTake($source);

            if ($raw === '') {
                continue;
            }

            $take = $this->normalizedAmount($raw);

            if ($take === null || str_starts_with($take, '-')) {
                continue;
            }

            $total = bcadd($total, $take, 2);
        }

        return $total;
    }

    public function coverMissing(): string
    {
        $cover = $this->coveredSobre();

        if ($cover === null) {
            return '0.00';
        }

        $missing = bcsub($this->shortfallAmount($cover), $this->coverTakenTotal(), 2);

        return bccomp($missing, '0.00', 2) > 0 ? $missing : '0.00';
    }

    public function coverIsOver(): bool
    {
        $cover = $this->coveredSobre();

        if ($cover === null) {
            return false;
        }

        return bccomp($this->coverTakenTotal(), $this->shortfallAmount($cover), 2) > 0;
    }

    public function emptiesSource(Sobre $source): bool
    {
        $raw = $this->rawTake($source);

        if ($raw === '') {
            return false;
        }

        $take = $this->normalizedAmount($raw);

        return $take !== null && bccomp($take, $this->availableOf($source), 2) === 0;
    }

    public function coverLineError(Sobre $source): ?string
    {
        $raw = $this->rawTake($source);

        if ($raw === '') {
            return null;
        }

        $take = $this->normalizedAmount($raw);

        if ($take === null || str_starts_with($take, '-') || bccomp($take, '0.00', 2) <= 0) {
            return 'El valor no es válido.';
        }

        if (bccomp($take, $this->availableOf($source), 2) > 0) {
            return 'El valor no es válido.';
        }

        return null;
    }

    private function rawTake(Sobre $source): string
    {
        $id = $source->id;

        return trim((string) ($this->coverTakes[$id] ?? $this->coverTakes[(string) $id] ?? ''));
    }

    private function fillSuggestedTakes(Sobre $problem): void
    {
        $remaining = $this->shortfallAmount($problem);
        $takes = [];

        foreach ($this->coverSources()->sort(fn (Sobre $a, Sobre $b): int => bccomp($this->availableOf($b), $this->availableOf($a), 2)) as $source) {
            if (bccomp($remaining, '0.00', 2) <= 0) {
                $takes[$source->id] = '';

                continue;
            }

            $avail = $this->availableOf($source);
            $take = bccomp($remaining, $avail, 2) >= 0 ? $avail : $remaining;
            $takes[$source->id] = $this->plan->amountForInput($take);
            $remaining = bcsub($remaining, $take, 2);
        }

        $this->coverTakes = $takes;
    }

    /**
     * @return array<string, string>|null
     */
    private function parsedCoverTakes(): ?array
    {
        $parsed = [];

        foreach ($this->coverSources() as $source) {
            $raw = $this->rawTake($source);

            if ($raw === '') {
                continue;
            }

            if ($this->coverLineError($source) !== null) {
                return null;
            }

            $parsed[(string) $source->id] = $this->normalizedAmount($raw);
        }

        return $parsed;
    }

    private function coverTakesMessage(): ?string
    {
        if ($this->coverIsOver()) {
            return 'Te estás pasando del monto a cubrir.';
        }

        foreach ($this->coverSources() as $source) {
            if ($this->coverLineError($source) !== null) {
                return 'El valor no es válido.';
            }
        }

        if (bccomp($this->coverMissing(), '0.00', 2) > 0) {
            return 'Falta por cubrir.';
        }

        return null;
    }

    /**
     * @return \Illuminate\Support\Collection<int, Sobre>
     */
    private function otherSobres(int $exceptId): \Illuminate\Support\Collection
    {
        $sobres = collect();

        foreach ($this->plan?->categories ?? [] as $category) {
            foreach ($category->sobres as $sobre) {
                if ($sobre->id !== $exceptId) {
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
        return (new AmountExpression)->evaluate($value, $this->plan?->number_format ?? '1.234,56');
    }
};
?>

<div>
    @if (auth()->check())
        @teleport('#mes-controles')
            <div class="flex items-center gap-2">
                <button type="button" wire:click="previousMonth" aria-label="Mes anterior" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white text-sm font-semibold text-forest shadow-sm ring-1 ring-emerald-100">‹</button>
                <h1 class="text-xl font-semibold tracking-tight sm:text-2xl">{{ $this->monthTitle }}</h1>
                <button type="button" wire:click="nextMonth" aria-label="Mes siguiente" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white text-sm font-semibold text-forest shadow-sm ring-1 ring-emerald-100">›</button>
            </div>
        @endteleport

        @teleport('#dinero-por-asignar')
            <div>
                @if ($this->plan !== null)
                    <p @class([
                        'text-3xl font-semibold tracking-tight',
                        'text-red-600' => str_starts_with($this->moneyToAssign, '-'),
                        'text-slate-900' => ! str_starts_with($this->moneyToAssign, '-'),
                    ])>{{ $this->plan->formatMoney($this->moneyToAssign) }}</p>
                @endif
            </div>
        @endteleport

        @teleport('#ingresos-del-mes')
            <div>
                @if ($this->plan !== null)
                    <p class="text-xl font-semibold text-slate-900">{{ $this->plan->formatMoney($this->monthTotals['income']) }}</p>
                @endif
            </div>
        @endteleport

        @teleport('#presupuestado-del-mes')
            <div>
                @if ($this->plan !== null)
                    <p class="text-xl font-semibold text-slate-900">{{ $this->plan->formatMoney($this->monthTotals['budgeted']) }}</p>
                    <p class="mt-1 text-[11px] text-slate-400">{{ $this->budgetMetaLabel() }}</p>
                @endif
            </div>
        @endteleport

        @teleport('#gastado-del-mes')
            <div>
                @if ($this->plan !== null)
                    <p class="text-xl font-semibold text-slate-900">{{ $this->plan->formatMoney($this->monthTotals['spent']) }}</p>
                    <p class="mt-1 text-[11px] text-slate-400">{{ $this->spentPercentLabel() }}</p>
                @endif
            </div>
        @endteleport

        @teleport('#moneda-plan')
            <div>
                @if ($this->plan !== null)
                    <a href="{{ route('plan.configuracion', $this->plan) }}" class="text-xs text-slate-500 underline-offset-2 hover:text-forest hover:underline">Moneda del plan: Bolivianos (Bs.)</a>
                @else
                    <p class="text-xs text-slate-500">Moneda del plan: Bolivianos (Bs.)</p>
                @endif
            </div>
        @endteleport

        <section class="relative mt-5 overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-emerald-100">
            <div class="grid grid-cols-[minmax(0,2fr)_repeat(3,minmax(0,1fr))] gap-2 border-b border-emerald-100 bg-mint/40 px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-forest sm:px-5">
                <p>Categoría / Sobre</p>
                <p class="text-right">Asignado</p>
                <p class="text-right">Actividad</p>
                <p class="text-right">Disponible</p>
            </div>

            @forelse ($this->plan?->categories ?? [] as $category)
                <div class="border-b border-emerald-100 bg-mint/80 px-4 py-2.5 sm:px-5">
                    <p class="text-sm font-semibold text-forest">{{ $category->name }}</p>
                </div>
                <div class="divide-y divide-emerald-50">
                    @foreach ($category->sobres as $sobre)
                        @php
                            $amounts = $this->monthValues($sobre);
                            $overspent = $this->rowIsOverspent($sobre);
                        @endphp
                        @if ($editingId === $sobre->id)
                            <form wire:submit="save" @class(['bg-red-50/70' => $overspent])>
                                <div class="grid grid-cols-[minmax(0,2fr)_repeat(3,minmax(0,1fr))] items-start gap-2 px-4 py-3 sm:px-5">
                                <div>
                                    <input type="text" wire:model="name" aria-label="Nombre" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none ring-forest/20 focus:border-forest focus:ring-4">
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <input type="text" wire:model="assigned" inputmode="text" aria-label="Asignado" placeholder="10+5" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-right text-sm outline-none ring-forest/20 focus:border-forest focus:ring-4">
                                    @error('assigned')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <p class="text-right text-xs text-slate-400">{{ $this->activity }}</p>
                                    <input type="text" wire:model="gasto" inputmode="text" aria-label="Nuevo gasto" placeholder="Nuevo gasto 10+5" class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-right text-sm outline-none ring-forest/20 focus:border-forest focus:ring-4">
                                    @error('gasto')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="flex items-start justify-end gap-2">
                                    <div class="min-w-0 flex-1">
                                        <input type="text" wire:model="available" inputmode="text" aria-label="Disponible" placeholder="10+5" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-right text-sm outline-none ring-forest/20 focus:border-forest focus:ring-4">
                                        @error('available')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <button type="submit" class="rounded-xl bg-forest px-3 py-2 text-sm font-semibold text-white">Guardar</button>
                                </div>
                                </div>
                                <div class="border-t border-emerald-100 bg-mint/30 px-4 py-3 sm:px-5">
                                    <label for="gasto-cuenta-{{ $sobre->id }}" class="mb-1.5 block text-xs font-medium text-slate-600">Sale de la cuenta</label>
                                    <select id="gasto-cuenta-{{ $sobre->id }}" wire:model="gastoCuentaId" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none ring-forest/20 focus:border-forest focus:ring-4">
                                        <option value="">Elegir cuenta</option>
                                        @foreach ($this->plan->cuentas as $cuenta)
                                            <option value="{{ $cuenta->id }}">{{ $cuenta->name }} ({{ $this->plan->formatMoney($cuenta->balance) }})</option>
                                        @endforeach
                                    </select>
                                    @error('gastoCuentaId')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </form>
                        @elseif ($overspent)
                            <div class="grid grid-cols-[minmax(0,2fr)_repeat(3,minmax(0,1fr))] items-center gap-2 bg-red-50/70 px-4 py-3 sm:px-5">
                                <div class="flex flex-wrap items-center gap-2">
                                    <button type="button" wire:click="edit({{ $sobre->id }})" class="text-left text-sm font-medium text-slate-800">{{ $sobre->name }}</button>
                                    <span class="rounded-full bg-red-100 px-2 py-0.5 text-[11px] font-semibold text-red-600">Sobregiro</span>
                                    @if ($this->canCover($sobre))
                                        <button type="button" wire:click="openCover({{ $sobre->id }})" class="rounded-lg bg-white px-2 py-1 text-xs font-semibold text-red-600 ring-1 ring-red-200">Cubrir</button>
                                    @endif
                                </div>
                                <span class="text-right text-sm text-slate-600">{{ $this->plan->formatMoney($amounts['assigned']) }}</span>
                                <span class="text-right text-sm text-slate-600">{{ $this->plan->formatMoney($amounts['activity']) }}</span>
                                <span class="text-right text-sm font-medium text-red-600">{{ $this->plan->formatMoney($amounts['available']) }}</span>
                            </div>
                        @else
                            <button
                                type="button"
                                wire:click="edit({{ $sobre->id }})"
                                class="grid w-full grid-cols-[minmax(0,2fr)_repeat(3,minmax(0,1fr))] items-center gap-2 px-4 py-3 text-left hover:bg-mint/40 sm:px-5"
                            >
                                <span class="text-sm font-medium text-slate-800">{{ $sobre->name }}</span>
                                <span class="text-right text-sm text-slate-600">{{ $this->plan->formatMoney($amounts['assigned']) }}</span>
                                <span class="text-right text-sm text-slate-600">{{ $this->plan->formatMoney($amounts['activity']) }}</span>
                                <span class="text-right text-sm font-semibold text-leaf">{{ $this->plan->formatMoney($amounts['available']) }}</span>
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
                        <p class="mt-2 text-sm text-slate-600">A cubrir {{ $this->shortfallLabel($coverSobre) }}</p>
                    </div>
                    <button type="button" wire:click="closeCover" aria-label="Cerrar" class="text-xl leading-none text-slate-400">×</button>
                </div>
                <div class="mt-4 rounded-xl bg-mint/60 px-3 py-2 text-sm text-slate-700">
                    <p>Cubierto: {{ $this->plan->formatMoney($this->coverTakenTotal()) }}</p>
                    <p>Falta: {{ $this->plan->formatMoney($this->coverMissing()) }}</p>
                </div>
                @if ($this->coverIsOver())
                    <p class="mt-3 text-sm text-red-600">Te estás pasando del monto a cubrir.</p>
                @elseif ($coverMessage !== '')
                    <p class="mt-3 text-sm text-red-600">{{ $coverMessage }}</p>
                @endif
                <ul class="mt-4 space-y-3">
                    @foreach ($this->coverSources() as $source)
                        <li wire:key="cover-source-{{ $source->id }}" class="rounded-xl bg-white px-3 py-2 ring-1 ring-emerald-100">
                            <div class="flex items-center justify-between gap-2 text-sm">
                                <span class="font-medium text-slate-800">{{ $source->name }}</span>
                                <span class="text-slate-500">tiene {{ $this->plan->formatMoney($this->availableOf($source)) }}</span>
                            </div>
                            <input
                                type="text"
                                wire:model.live="coverTakes.{{ $source->id }}"
                                inputmode="decimal"
                                aria-label="Tomar de {{ $source->name }}"
                                @class([
                                    'mt-2 w-full rounded-xl border bg-slate-50 px-3 py-2 text-right text-sm outline-none ring-forest/20 focus:border-forest focus:bg-white focus:ring-4',
                                    'border-red-400 text-red-600' => $this->emptiesSource($source) || $this->coverLineError($source) !== null,
                                    'border-slate-200' => ! $this->emptiesSource($source) && $this->coverLineError($source) === null,
                                ])
                            >
                            @if ($this->coverLineError($source) !== null)
                                <p class="mt-1 text-sm text-red-600">{{ $this->coverLineError($source) }}</p>
                            @elseif ($this->emptiesSource($source))
                                <p class="mt-1 text-sm text-red-600">Dejas este sobre en 0.</p>
                            @endif
                        </li>
                    @endforeach
                </ul>
                <button type="button" wire:click="confirmCover" class="mt-4 rounded-xl bg-forest px-4 py-2.5 text-sm font-semibold text-white">Confirmar</button>
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
