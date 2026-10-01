<?php

use App\Models\Plan;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public ?int $openPlanId = null;

    public function mount(): void
    {
        $user = Auth::user();

        if ($user === null) {
            return;
        }

        $latestId = $user->plans()->latest('id')->value('id');

        if ($latestId !== null) {
            $this->openPlanId = (int) $latestId;
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Plan>
     */
    #[Computed]
    public function plans()
    {
        $user = Auth::user();

        if ($user === null) {
            return new \Illuminate\Database\Eloquent\Collection;
        }

        return $user->plans()->orderBy('id')->get();
    }

    public function open(int $planId): void
    {
        $plan = Plan::query()
            ->where('user_id', Auth::id())
            ->whereKey($planId)
            ->first();

        if ($plan === null) {
            return;
        }

        $this->openPlanId = $plan->id;
        $this->dispatch('plan-abierto', planId: $plan->id)->to('plan-sobres');
        $this->dispatch('plan-abierto', planId: $plan->id)->to('plan-accounts');
    }
};
?>

<div>
    @if (auth()->check())
        <section class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-emerald-100">
            <div class="flex items-center justify-between gap-3">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-forest">Planes</p>
                <a href="{{ route('planes.crear') }}" wire:navigate class="rounded-xl bg-forest px-3 py-2 text-sm font-semibold text-white shadow-sm">Crear plan</a>
            </div>

            @if ($this->plans->isEmpty())
                <p class="mt-4 text-sm text-slate-600">Aún no tienes planes.</p>
            @else
                <ul class="mt-3 space-y-1.5">
                    @foreach ($this->plans as $plan)
                        <li>
                            <button
                                type="button"
                                wire:click="open({{ $plan->id }})"
                                @class([
                                    'flex w-full items-center justify-between gap-2 rounded-xl px-3 py-2.5 text-left text-sm transition',
                                    'bg-mint font-semibold text-forest ring-1 ring-emerald-200' => $openPlanId === $plan->id,
                                    'text-slate-700 hover:bg-mint/60' => $openPlanId !== $plan->id,
                                ])
                            >
                                <span class="min-w-0 truncate">{{ $plan->name }}</span>
                                <span class="flex shrink-0 items-center gap-1.5">
                                    @if ($plan->id === $this->plans->last()->id)
                                        <span class="rounded-full bg-white px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-forest">Último</span>
                                    @endif
                                    <span class="rounded-full bg-white/80 px-2 py-0.5 text-[11px] font-medium text-slate-500">{{ $plan->currency }}</span>
                                </span>
                            </button>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    @endif
</div>
